<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\BottleInventory;
use App\Models\CapInventory;
use App\Models\Order;
use App\Models\ProductSize;
use Illuminate\Support\Facades\DB;

/**
 * Creates orders the way spec sections 21/22/23 require: server recalculates
 * everything, then persists a frozen snapshot that must never drift when
 * master data (prices, names) changes later.
 */
class OrderService
{
    public function __construct(private readonly CartQuoteService $cartQuoteService) {}

    /**
     * @param  array<string, mixed>  $customer
     * @param  array<int, array<string, mixed>>  $items
     */
    public function create(
        array $customer,
        string $shippingMethodId,
        string $paymentMethod,
        ?string $couponCode,
        array $items,
    ): Order {
        if (count($items) === 0) {
            throw new ApiException('Your cart is empty.', 400, 'EMPTY_CART');
        }

        return DB::transaction(function () use ($customer, $shippingMethodId, $paymentMethod, $couponCode, $items) {
            // Lock the component rows this order touches before quoting so two
            // concurrent checkouts can't both oversell the same bottle/cap/size.
            $this->lockComponents($items);

            $quote = $this->cartQuoteService->quote($items, $shippingMethodId, $couponCode);

            $order = Order::create([
                'order_number' => 'PENDING',
                'user_id' => $customer['userId'] ?? null,
                'status' => 'PLACED',
                'customer_full_name' => $customer['fullName'],
                'customer_mobile' => $customer['mobile'],
                'customer_email' => $customer['email'],
                'customer_address' => $customer['address'],
                'customer_city' => $customer['city'],
                'customer_state' => $customer['state'],
                'customer_pincode' => $customer['pincode'],
                'shipping_method_id' => $quote['shippingMethod']->id,
                'shipping_method_label' => $quote['shippingMethod']->name,
                'payment_method' => $paymentMethod,
                'coupon_code' => $couponCode ? strtoupper(trim($couponCode)) : null,
                'subtotal' => $quote['subtotal'],
                'discount' => $quote['discount'],
                'tax' => $quote['tax'],
                'shipping' => $quote['shipping'],
                'final_price' => $quote['total'],
            ]);

            $order->order_number = 'ROS'.(10024 + $order->id);
            $order->save();

            foreach ($quote['lines'] as $line) {
                $order->items()->create([
                    'product_type' => $line['productType'],
                    'product_name' => $line['productName'],
                    'size_name' => $line['sizeName'],
                    'fragrance_name' => $line['fragranceName'],
                    'bottle_name' => $line['bottleName'],
                    'cap_name' => $line['capName'],
                    'remarks' => $line['remarks'] ?? null,
                    'label_line1' => $line['labelLine1'] ?? null,
                    'label_line2' => $line['labelLine2'] ?? null,
                    'quantity' => $line['quantity'],
                    'base_price' => $line['basePrice'],
                    'bottle_price' => $line['bottlePrice'],
                    'cap_price' => $line['capPrice'],
                    'discount' => $line['discount'],
                    'tax' => $line['tax'],
                    'final_price' => $line['finalPrice'],
                    'image' => $line['image'],
                ]);
            }

            $this->applyInventory($items);

            return $order->load('items');
        });
    }

    /** @param  array<int, array<string, mixed>>  $items */
    private function lockComponents(array $items): void
    {
        foreach ($items as $item) {
            if (($item['productType'] ?? null) === 'READY_MADE') {
                ProductSize::where('product_id', $item['productId'] ?? null)
                    ->where('size_id', $item['sizeId'] ?? null)
                    ->lockForUpdate()
                    ->first();
            } else {
                BottleInventory::where('bottle_id', $item['bottleId'] ?? null)->lockForUpdate()->first();
                CapInventory::where('cap_id', $item['capId'] ?? null)->lockForUpdate()->first();
            }
        }
    }

    /** @param  array<int, array<string, mixed>>  $items */
    private function applyInventory(array $items): void
    {
        foreach ($items as $item) {
            $quantity = (int) ($item['quantity'] ?? 0);

            if (($item['productType'] ?? null) === 'READY_MADE') {
                $sizeRow = ProductSize::where('product_id', $item['productId'])->where('size_id', $item['sizeId'])->first();
                if ($sizeRow) {
                    $sizeRow->decrement('stock', min($quantity, $sizeRow->stock));
                }

                continue;
            }

            $bottleInventory = BottleInventory::find($item['bottleId']);
            $bottleInventory?->reserve($quantity);

            $capInventory = CapInventory::find($item['capId']);
            $capInventory?->reserve($quantity);
        }
    }
}
