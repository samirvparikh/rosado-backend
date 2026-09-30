<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Models\Bottle;
use App\Services\CartQuoteService;
use App\Services\CustomPerfumeValidator;
use App\Services\ReadyMadePricer;
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
            'fragranceId' => ['required', 'string'],
            'sizeId' => ['required', 'string'],
            'bottleId' => ['required', 'string'],
            'capId' => ['required', 'string'],
            'quantity' => ['required', 'integer'],
        ]);

        $result = $this->customPerfumeValidator->validate($data);
        $image = Bottle::where('id', $result['bottleId'])->value('image');

        return response()->json([
            'id' => "CUSTOM-{$result['fragranceId']}-{$result['sizeId']}-{$result['bottleId']}-{$result['capId']}",
            'productType' => 'CUSTOM_PERFUME',
            'fragranceId' => $result['fragranceId'],
            'fragranceName' => $result['fragranceName'],
            'sizeId' => $result['sizeId'],
            'sizeName' => $result['sizeName'],
            'bottleId' => $result['bottleId'],
            'bottleName' => $result['bottleName'],
            'capId' => $result['capId'],
            'capName' => $result['capName'],
            'image' => $image,
            'quantity' => $result['quantity'],
            'basePrice' => $result['basePrice'],
            'bottlePrice' => $result['bottlePrice'],
            'capPrice' => $result['capPrice'],
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
