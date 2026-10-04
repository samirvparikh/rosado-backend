<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Support\CustomizerLayers;
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
    public const LABEL_LINE_MAX = 24;

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
                'productId' => $priced['productId'],
                'productName' => $priced['productName'],
                'sizeId' => $priced['sizeId'],
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
                'productId' => $item['productId'] ?? null,
                'fragranceId' => $item['fragranceId'] ?? null,
                'sizeId' => $item['sizeId'] ?? null,
                'bottleId' => $item['bottleId'] ?? null,
                'capId' => $item['capId'] ?? null,
                'quantity' => $quantity,
            ]);

            ['bottle' => $bottle, 'cap' => $cap, 'fragrance' => $fragrance] = $validated['models'];
            $labelLine1 = self::cleanLabelLine($item['labelLine1'] ?? null);
            $labelLine2 = self::cleanLabelLine($item['labelLine2'] ?? null);

            return [
                'productType' => 'CUSTOM_PERFUME',
                'productId' => $validated['productId'],
                'productName' => $validated['productName'],
                'sizeId' => $validated['sizeId'],
                'sizeName' => $validated['sizeName'],
                'fragranceId' => $validated['fragranceId'],
                'fragranceName' => $validated['fragranceName'],
                'bottleId' => $validated['bottleId'],
                'bottleName' => $validated['bottleName'],
                'capId' => $validated['capId'],
                'capName' => $validated['capName'],
                'remarks' => self::cleanRemarks($item['remarks'] ?? null),
                'labelLine1' => $labelLine1,
                'labelLine2' => $labelLine2,
                'quantity' => $validated['quantity'],
                'basePrice' => $validated['basePrice'],
                'fragrancePrice' => $validated['fragrancePrice'],
                'bottlePrice' => $validated['bottlePrice'],
                'capPrice' => $validated['capPrice'],
                'customizationPrice' => $validated['customizationPrice'],
                'discount' => 0,
                'tax' => round($validated['lineTotal'] * 18 / 118),
                'finalPrice' => $validated['lineTotal'],
                'lineTotal' => $validated['lineTotal'],
                'image' => $bottle->image,
                'preview' => CustomizerLayers::compose($bottle, $cap, $fragrance, $validated['sizeName'], [$labelLine1, $labelLine2]),
            ];
        }

        throw new ApiException('Unknown cart item type.', 422, 'UNKNOWN_ENTITY');
    }

    /** Optional free-text customer remarks on a custom perfume: trimmed, capped, empty → null. */
    public static function cleanRemarks(mixed $remarks): ?string
    {
        if (! is_string($remarks)) {
            return null;
        }

        $remarks = mb_substr(trim($remarks), 0, 500);

        return $remarks === '' ? null : $remarks;
    }

    /** One personalised line printed on the bottle label: single-line, trimmed, capped, empty → null. */
    public static function cleanLabelLine(mixed $line): ?string
    {
        if (! is_string($line)) {
            return null;
        }

        $line = mb_substr(trim(preg_replace('/\s+/u', ' ', $line)), 0, self::LABEL_LINE_MAX);

        return $line === '' ? null : $line;
    }
}
