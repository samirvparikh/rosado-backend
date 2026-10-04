<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Services\CartQuoteService;
use App\Services\CustomPerfumeValidator;
use App\Services\ReadyMadePricer;
use App\Support\CustomizerLayers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        private readonly ReadyMadePricer $readyMadePricer,
        private readonly CustomPerfumeValidator $customPerfumeValidator,
        private readonly CartQuoteService $cartQuoteService,
    ) {}

    /** POST /api/cart/price-ready-made */
    public function priceReadyMade(Request $request): JsonResponse
    {
        $data = $request->validate([
            'productId' => ['required', 'string'],
            'sizeId' => ['required', 'string'],
            'quantity' => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        return response()->json($this->readyMadePricer->price($data['productId'], $data['sizeId'], $data['quantity']));
    }

    /** POST /api/cart/price-custom */
    public function priceCustom(Request $request): JsonResponse
    {
        $data = $request->validate([
            'productId' => ['nullable', 'string'],
            'fragranceId' => ['required', 'string'],
            'sizeId' => ['required', 'string'],
            'bottleId' => ['required', 'string'],
            'capId' => ['required', 'string'],
            'quantity' => ['required', 'integer'],
            'remarks' => ['nullable', 'string', 'max:500'],
            'labelLine1' => ['nullable', 'string', 'max:'.CartQuoteService::LABEL_LINE_MAX],
            'labelLine2' => ['nullable', 'string', 'max:'.CartQuoteService::LABEL_LINE_MAX],
        ]);

        $result = $this->customPerfumeValidator->validate($data);
        $remarks = CartQuoteService::cleanRemarks($data['remarks'] ?? null);
        $labelLine1 = CartQuoteService::cleanLabelLine($data['labelLine1'] ?? null);
        $labelLine2 = CartQuoteService::cleanLabelLine($data['labelLine2'] ?? null);

        // Same configuration with different remarks/label text must stay a separate cart line.
        $id = 'CUSTOM-'.($result['productId'] ? "{$result['productId']}-" : '')
            ."{$result['fragranceId']}-{$result['sizeId']}-{$result['bottleId']}-{$result['capId']}";
        $personalisation = implode("
", [$remarks ?? '', $labelLine1 ?? '', $labelLine2 ?? '']);
        if (trim($personalisation) !== '') {
            $id .= '-'.substr(md5($personalisation), 0, 8);
        }

        return response()->json([
            'id' => $id,
            'productType' => 'CUSTOM_PERFUME',
            'productId' => $result['productId'],
            'productName' => $result['productName'],
            'fragranceId' => $result['fragranceId'],
            'fragranceName' => $result['fragranceName'],
            'sizeId' => $result['sizeId'],
            'sizeName' => $result['sizeName'],
            'bottleId' => $result['bottleId'],
            'bottleName' => $result['bottleName'],
            'capId' => $result['capId'],
            'capName' => $result['capName'],
            'remarks' => $remarks,
            'labelLine1' => $labelLine1,
            'labelLine2' => $labelLine2,
            'image' => $result['models']['bottle']->image,
            // Layer stack for the cart thumbnail -- display only; the IDs above are what's re-priced.
            'preview' => CustomizerLayers::compose(
                $result['models']['bottle'], $result['models']['cap'], $result['models']['fragrance'],
                $result['sizeName'], [$labelLine1, $labelLine2],
            ),
            'quantity' => $result['quantity'],
            'basePrice' => $result['basePrice'],
            'fragrancePrice' => $result['fragrancePrice'],
            'bottlePrice' => $result['bottlePrice'],
            'capPrice' => $result['capPrice'],
            'customizationPrice' => $result['customizationPrice'],
            'unitPrice' => $result['unitPrice'],
            'lineTotal' => $result['lineTotal'],
        ]);
    }

    /** POST /api/cart/quote -- authoritative totals for the cart/checkout summary. */
    public function quote(Request $request): JsonResponse
    {
        $data = $request->validate([
            'items' => ['present', 'array'],
            'shippingMethod' => ['required', 'string'],
            'couponCode' => ['nullable', 'string'],
        ]);

        if (count($data['items']) === 0) {
            throw new ApiException('Your cart is empty.', 400, 'EMPTY_CART');
        }

        $quote = $this->cartQuoteService->quote($data['items'], $data['shippingMethod'], $data['couponCode'] ?? null);

        return response()->json([
            'subtotal' => $quote['subtotal'],
            'discount' => $quote['discount'],
            'tax' => $quote['tax'],
            'shipping' => $quote['shipping'],
            'total' => $quote['total'],
            'shippingMethodId' => $quote['shippingMethod']->id,
            'shippingMethodLabel' => $quote['shippingMethod']->name,
        ]);
    }
}
