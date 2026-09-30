<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use App\Support\Presenters;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService) {}

    /** POST /api/orders -- guest checkout is allowed; if authenticated, the order is linked to the account. */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'customer.fullName' => ['required', 'string'],
            'customer.mobile' => ['required', 'string'],
            'customer.email' => ['required', 'email'],
            'customer.address' => ['required', 'string'],
            'customer.city' => ['required', 'string'],
            'customer.state' => ['required', 'string'],
            'customer.pincode' => ['required', 'string'],
            'shippingMethod' => ['required', 'string'],
            'paymentMethod' => ['required', 'string', 'in:COD,UPI,CARD'],
            'couponCode' => ['nullable', 'string'],
            'items' => ['present', 'array'],
        ]);

        $customer = $data['customer'];
        $customer['userId'] = $request->user('sanctum')?->id;

        $order = $this->orderService->create(
            $customer,
            $data['shippingMethod'],
            $data['paymentMethod'],
            $data['couponCode'] ?? null,
            $data['items'],
        );

        return response()->json(Presenters::order($order), 201);
    }

    /** GET /api/orders -- authenticated customer's order history. */
    public function index(Request $request): JsonResponse
    {
        $orders = Order::with('items')
            ->where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json($orders->map(fn ($o) => Presenters::order($o))->values());
    }

    /** GET /api/orders/:id */
    public function show(Request $request, string $id): JsonResponse
    {
        $order = Order::with('items')
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        return response()->json($order ? Presenters::order($order) : null);
    }
}
