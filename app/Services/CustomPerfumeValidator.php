<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\Bottle;
use App\Models\Cap;
use App\Models\Fragrance;
use App\Models\Size;

/**
 * Server-authoritative recreation of the storefront's custom perfume rules
 * (spec sections 12, 15, 34, 51). The frontend never has the final say on
 * price, stock, or bottle/cap compatibility -- this is the source of truth.
 */
class CustomPerfumeValidator
{
    public function __construct(private readonly CustomizerCatalog $catalog) {}

    /**
     * Price = product base price for the size + fragrance price for the size
     * + bottle + cap. "customizationPrice" is everything except the base.
     *
     * @param  array{productId?: ?string, fragranceId?: ?string, sizeId?: ?string, bottleId?: ?string, capId?: ?string, quantity?: mixed}  $payload
     * @return array{productId: ?string, productName: string, fragranceId: string, fragranceName: string, sizeId: string, sizeName: string, bottleId: string, bottleName: string, capId: string, capName: string, basePrice: float, fragrancePrice: float, bottlePrice: float, capPrice: float, customizationPrice: float, unitPrice: float, quantity: int, lineTotal: float, models: array{bottle: Bottle, cap: Cap, fragrance: Fragrance}}
     *
     * @throws ApiException
     */
    public function validate(array $payload): array
    {
        $fragranceId = $payload['fragranceId'] ?? null;
        $sizeId = $payload['sizeId'] ?? null;
        $bottleId = $payload['bottleId'] ?? null;
        $capId = $payload['capId'] ?? null;
        $quantity = $payload['quantity'] ?? null;

        if (! $fragranceId || ! $sizeId || ! $bottleId || ! $capId) {
            throw new ApiException('Complete the perfume configuration.', 422, 'MISSING_FIELDS');
        }

        if (! is_int($quantity) || $quantity < 1 || $quantity > 10) {
            throw new ApiException('Quantity must be between 1 and 10.', 422, 'INVALID_QUANTITY');
        }

        // Older cart lines carry no productId -- they price against the default product.
        $product = $this->catalog->resolveProduct($payload['productId'] ?? null);

        $size = Size::where('id', $sizeId)->where('status', 'ACTIVE')->first();
        $fragrance = Fragrance::with('sizePrices')->where('id', $fragranceId)->where('status', 'ACTIVE')->first();
        $bottle = Bottle::with(['inventory', 'layerOverrides'])->find($bottleId);
        $cap = Cap::with(['inventory', 'sizeMappings'])->find($capId);

        if (! $size || ! $fragrance || ! $bottle || ! $cap) {
            throw new ApiException('This configuration is no longer available.', 422, 'UNKNOWN_ENTITY');
        }

        if ($bottle->status !== 'ACTIVE' || $cap->status !== 'ACTIVE') {
            throw new ApiException('A selected component is unavailable.', 422, 'INACTIVE');
        }

        if (! $this->catalog->offers($product, 'FRAGRANCE', $fragrance->id)
            || ! $this->catalog->offers($product, 'BOTTLE', $bottle->id)
            || ! $this->catalog->offers($product, 'CAP', $cap->id)) {
            throw new ApiException('A selected component is not offered for this perfume.', 422, 'NOT_OFFERED');
        }

        $basePrice = $this->catalog->basePriceFor($product, $size->id);
        if ($basePrice === null) {
            throw new ApiException('This size is not offered for this perfume.', 422, 'NOT_OFFERED');
        }

        if ($bottle->size_id !== $size->id) {
            throw new ApiException('Bottle is not compatible with the selected size.', 422, 'BOTTLE_SIZE_MISMATCH');
        }

        if (! $cap->isCompatibleWithSize($size->id)) {
            throw new ApiException('Cap is not compatible with the selected size.', 422, 'CAP_SIZE_MISMATCH');
        }

        $fragrancePrice = $fragrance->basePriceForSize($size->id);
        if ($fragrancePrice === null) {
            throw new ApiException('No price is defined for this size.', 422, 'UNKNOWN_ENTITY');
        }

        $bottleAvailable = $bottle->inventory?->available_stock ?? 0;
        $capAvailable = $cap->inventory?->available_stock ?? 0;

        if ($bottleAvailable < $quantity || $capAvailable < $quantity) {
            throw new ApiException('A selected component is out of stock.', 422, 'OUT_OF_STOCK');
        }

        $bottlePrice = (float) $bottle->additional_price;
        $capPrice = (float) $cap->additional_price;
        $customizationPrice = $fragrancePrice + $bottlePrice + $capPrice;
        $unitPrice = $basePrice + $customizationPrice;

        return [
            'productId' => $product?->id,
            'productName' => $product?->name ?? CustomizerCatalog::DEFAULT_NAME,
            'fragranceId' => $fragrance->id,
            'fragranceName' => $fragrance->name,
            'sizeId' => $size->id,
            'sizeName' => $size->display_name,
            'bottleId' => $bottle->id,
            'bottleName' => $bottle->name,
            'capId' => $cap->id,
            'capName' => $cap->name,
            'basePrice' => $basePrice,
            'fragrancePrice' => $fragrancePrice,
            'bottlePrice' => $bottlePrice,
            'capPrice' => $capPrice,
            'customizationPrice' => $customizationPrice,
            'unitPrice' => $unitPrice,
            'quantity' => $quantity,
            'lineTotal' => $unitPrice * $quantity,
            // Models handed on so callers can compose the preview without re-querying.
            'models' => compact('bottle', 'cap', 'fragrance'),
        ];
    }

    /**
     * Same rules, but for the pre-quantity price preview shown live in the
     * builder. Never throws -- an incomplete/invalid config just quotes ₹0,
     * exactly like the mock estimateCustomPerfumePrice did.
     *
     * @param  array{productId?: ?string, fragranceId?: ?string, sizeId?: ?string, bottleId?: ?string, capId?: ?string}  $payload
     * @return array{basePrice: float, fragrancePrice: float, bottlePrice: float, capPrice: float, customizationPrice: float, totalPrice: float}
     */
    public function estimate(array $payload): array
    {
        try {
            $result = $this->validate([...$payload, 'quantity' => 1]);
        } catch (ApiException) {
            return ['basePrice' => 0, 'fragrancePrice' => 0, 'bottlePrice' => 0, 'capPrice' => 0, 'customizationPrice' => 0, 'totalPrice' => 0];
        }

        return [
            'basePrice' => $result['basePrice'],
            'fragrancePrice' => $result['fragrancePrice'],
            'bottlePrice' => $result['bottlePrice'],
            'capPrice' => $result['capPrice'],
            'customizationPrice' => $result['customizationPrice'],
            'totalPrice' => $result['unitPrice'],
        ];
    }
}
