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
    /**
     * @param  array{fragranceId?: ?string, sizeId?: ?string, bottleId?: ?string, capId?: ?string, quantity?: mixed}  $payload
     * @return array{fragranceId: string, fragranceName: string, sizeId: string, sizeName: string, bottleId: string, bottleName: string, capId: string, capName: string, basePrice: float, bottlePrice: float, capPrice: float, unitPrice: float, quantity: int, lineTotal: float}
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

        $size = Size::where('id', $sizeId)->where('status', 'ACTIVE')->first();
        $fragrance = Fragrance::where('id', $fragranceId)->where('status', 'ACTIVE')->first();
        $bottle = Bottle::with('inventory')->find($bottleId);
        $cap = Cap::with(['inventory', 'sizeMappings'])->find($capId);

        if (! $size || ! $fragrance || ! $bottle || ! $cap) {
            throw new ApiException('This configuration is no longer available.', 422, 'UNKNOWN_ENTITY');
        }

        if ($bottle->status !== 'ACTIVE' || $cap->status !== 'ACTIVE') {
            throw new ApiException('A selected component is unavailable.', 422, 'INACTIVE');
        }

        if ($bottle->size_id !== $size->id) {
            throw new ApiException('Bottle is not compatible with the selected size.', 422, 'BOTTLE_SIZE_MISMATCH');
        }

        if (! $cap->isCompatibleWithSize($size->id)) {
            throw new ApiException('Cap is not compatible with the selected size.', 422, 'CAP_SIZE_MISMATCH');
        }

        $basePrice = $fragrance->basePriceForSize($size->id);
        if ($basePrice === null) {
            throw new ApiException('No price is defined for this size.', 422, 'UNKNOWN_ENTITY');
        }

        $bottleAvailable = $bottle->inventory?->available_stock ?? 0;
        $capAvailable = $cap->inventory?->available_stock ?? 0;

        if ($bottleAvailable < $quantity || $capAvailable < $quantity) {
            throw new ApiException('A selected component is out of stock.', 422, 'OUT_OF_STOCK');
        }

        $bottlePrice = (float) $bottle->additional_price;
        $capPrice = (float) $cap->additional_price;
        $unitPrice = $basePrice + $bottlePrice + $capPrice;

        return [
            'fragranceId' => $fragrance->id,
            'fragranceName' => $fragrance->name,
            'sizeId' => $size->id,
            'sizeName' => $size->display_name,
            'bottleId' => $bottle->id,
            'bottleName' => $bottle->name,
            'capId' => $cap->id,
            'capName' => $cap->name,
            'basePrice' => $basePrice,
            'bottlePrice' => $bottlePrice,
            'capPrice' => $capPrice,
            'unitPrice' => $unitPrice,
            'quantity' => $quantity,
            'lineTotal' => $unitPrice * $quantity,
        ];
    }

    /**
     * Same rules, but for the pre-quantity price preview shown live in the
     * builder. Never throws -- an incomplete/invalid config just quotes ₹0,
     * exactly like the mock estimateCustomPerfumePrice did.
     *
     * @param  array{fragranceId?: ?string, sizeId?: ?string, bottleId?: ?string, capId?: ?string}  $payload
     * @return array{basePrice: float, bottlePrice: float, capPrice: float, totalPrice: float}
     */
    public function estimate(array $payload): array
    {
        try {
            $result = $this->validate([...$payload, 'quantity' => 1]);
        } catch (ApiException) {
            return ['basePrice' => 0, 'bottlePrice' => 0, 'capPrice' => 0, 'totalPrice' => 0];
        }

        return [
            'basePrice' => $result['basePrice'],
            'bottlePrice' => $result['bottlePrice'],
            'capPrice' => $result['capPrice'],
            'totalPrice' => $result['unitPrice'],
        ];
    }
}
