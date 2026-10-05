<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
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
        $user = $request->user('sanctum');
        $customer['userId'] = $user?->id;

        $order = $this->orderService->create(
            $customer,
            $data['shippingMethod'],
            $data['paymentMethod'],
            $data['couponCode'] ?? null,
            $data['items'],
        );

        if ($user) {
            $this->rememberCheckoutDetails($user, $data['customer']);
        }

        // The access key is returned once, to whoever placed the order, so the
        // confirmation page works for guests too (see show()).
        return response()->json([...Presenters::order($order), 'accessToken' => $order->access_token], 201);
    }

    /**
     * Signed-in checkout keeps the customer's details current: name + mobile
     * on the account, and the shipping details as their default address (so
     * the next checkout is pre-filled). The login email is never changed
     * here; the checkout email is kept on the address. Runs after the order
     * exists -- a failure here must never fail the order.
     *
     * @param  array<string, string>  $details
     */
    private function rememberCheckoutDetails(User $user, array $details): void
    {
        try {
            $user->update(['name' => $details['fullName'], 'mobile' => $details['mobile']]);

            $values = [
                'full_name' => $details['fullName'],
                'mobile' => $details['mobile'],
                'email' => $details['email'],
                'address_line' => $details['address'],
                'city' => $details['city'],
                'state' => $details['state'],
                'pincode' => $details['pincode'],
                'is_default' => true,
            ];

            $default = $user->addresses()->orderByDesc('is_default')->orderByDesc('id')->first();
            $default ? $default->update($values) : $user->addresses()->create($values);
        } catch (\Throwable $e) {
            report($e);
        }
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

    /**
     * GET /api/orders/:id[?token=...] -- readable by the signed-in owner, or
     * by anyone holding the order's access key (guest checkout confirmation).
     * IDs are sequential, so the ID alone never grants access.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $order = Order::with('items')->find($id);
        $user = $request->user('sanctum');
        $token = (string) $request->query('token', '');

        $isOwner = $order && $user && $order->user_id === $user->id;
        $hasKey = $order && $token !== '' && $order->access_token && hash_equals($order->access_token, $token);

        if (! $isOwner && ! $hasKey) {
            // Same answer whether the order is missing or not theirs -- don't confirm IDs exist.
            return response()->json([
                'message' => $user ? 'This order could not be found.' : 'Please sign in to view this order.',
                'code' => $user ? 'NOT_FOUND' : 'UNAUTHENTICATED',
            ], $user ? 404 : 401);
        }

        return response()->json(Presenters::order($order));
    }
}
