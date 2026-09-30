<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\Bottle;
use App\Models\Coupon;
use App\Models\ShippingMethod;

/**
 * Re-prices every cart/order line server-side (spec section 34 -- the
 * frontend is never the source of truth for price, stock, or discount) and
 * produces both the order totals and the exact snapshot rows an order should
 * persist (spec section 22).
 */
class CartQuoteService
{
    public function __construct(
        private readonly ReadyMadePricer $readyMadePricer,
        private readonly CustomPerfumeValidator $customPerfumeValidator,
    ) {}

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array{lines: array<int, array<string, mixed>>, subtotal: float, discount: float, tax: float, shipping: float, total: float, shippingMethod: ShippingMethod}
     */
    public function quote(array $items, string $shippingMethodId, ?string $couponCode = null): array
    {
        $lines = [];
        $subtotal = 0.0;

        foreach ($items as $item) {
            $line = $this->priceLine($item);
            $lines[] = $line;
            $subtotal += $line['lineTotal'];
        }

        $method = ShippingMethod::where('id', $shippingMethodId)->where('status', 'ACTIVE')->first()
            ?? ShippingMethod::where('status', 'ACTIVE')->orderBy('sort_order')->firstOrFail();

        $shipping = $method->id === 'standard' && $subtotal >= 999 ? 0.0 : (float) $method->price;

        $discount = 0.0;
        $normalizedCode = $couponCode ? strtoupper(trim($couponCode)) : null;
        if ($normalizedCode) {
            $coupon = Coupon::where('code', $normalizedCode)->where('status', 'ACTIVE')->first();
            if (! $coupon || $subtotal < (float) $coupon->min_subtotal) {
                throw new ApiException('This coupon cannot be applied.', 422, 'INVALID_COUPON');
            }
            $discount = $coupon->type === 'PERCENT'
                ? round($subtotal * (float) $coupon->value / 100)
                : min((float) $coupon->value, $subtotal);
        }

        $taxable = max(0.0, $subtotal - $discount);
        $tax = round($taxable * 18 / 118);
        $total = $taxable + $shipping;

        return [
            'lines' => $lines,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax' => $tax,
            'shipping' => $shipping,
            'total' => $total,
            'shippingMethod' => $method,
        ];
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    private function priceLine(array $item): array
    {
        $productType = $item['productType'] ?? null;
        $quantity = (int) ($item['quantity'] ?? 0);

        if ($productType === 'READY_MADE') {
            $priced = $this->readyMadePricer->price((string) ($item['productId'] ?? ''), (string) ($item['sizeId'] ?? ''), $quantity);

            return [
                'productType' => 'READY_MADE',
                'productName' => $priced['productName'],
                'sizeName' => $priced['sizeName'],
                'fragranceName' => null,
                'bottleName' => null,
                'capName' => null,
                'quantity' => $quantity,
                'basePrice' => $priced['unitPrice'],
                'bottlePrice' => 0,
                'capPrice' => 0,
                'discount' => 0,
                'tax' => round($priced['lineTotal'] * 18 / 118),
                'finalPrice' => $priced['lineTotal'],
                'lineTotal' => $priced['lineTotal'],
                'image' => $priced['image'],
            ];
        }

        if ($productType === 'CUSTOM_PERFUME') {
            $validated = $this->customPerfumeValidator->validate([
                'fragranceId' => $item['fragranceId'] ?? null,
                'sizeId' => $item['sizeId'] ?? null,
                'bottleId' => $item['bottleId'] ?? null,
                'capId' => $item['capId'] ?? null,
                'quantity' => $quantity,
            ]);

            $bottleImage = Bottle::where('id', $validated['bottleId'])->value('image');

            return [
                'productType' => 'CUSTOM_PERFUME',
                'productName' => 'CUSTOM ROSADO PERFUME',
                'sizeName' => $validated['sizeName'],
                'fragranceName' => $validated['fragranceName'],
                'bottleName' => $validated['bottleName'],
                'capName' => $validated['capName'],
                'quantity' => $validated['quantity'],
                'basePrice' => $validated['basePrice'],
                'bottlePrice' => $validated['bottlePrice'],
                'capPrice' => $validated['capPrice'],
                'discount' => 0,
                'tax' => round($validated['lineTotal'] * 18 / 118),
                'finalPrice' => $validated['lineTotal'],
                'lineTotal' => $validated['lineTotal'],
                'image' => $bottleImage,
            ];
        }

        throw new ApiException('Unknown cart item type.', 422, 'UNKNOWN_ENTITY');
    }
}
