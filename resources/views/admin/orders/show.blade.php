<x-admin-layout :title="'Order '.$order->order_number">
    <div class="mb-4">
        <a href="{{ route('admin.orders.index') }}" class="text-xs text-neutral-500 hover:text-neutral-800">← Back to orders</a>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="overflow-hidden rounded-lg border border-neutral-200 bg-white">
                <div class="border-b border-neutral-100 px-5 py-4">
                    <h2 class="font-medium text-neutral-800">Items</h2>
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-neutral-100 bg-neutral-50 text-left text-xs uppercase tracking-wider text-neutral-500">
                            <th class="px-5 py-2.5">Item</th>
                            <th class="px-5 py-2.5">Qty</th>
                            <th class="px-5 py-2.5 text-right">Final Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr class="border-b border-neutral-50 last:border-0">
                                <td class="px-5 py-3">
                                    @if ($item->product_type === 'CUSTOM_PERFUME')
                                        <p class="text-[11px] uppercase tracking-wider text-amber-600">Custom ROSADO Perfume</p>
                                        <p class="text-neutral-800">{{ $item->size_name }} · {{ $item->fragrance_name }}</p>
                                        <p class="text-neutral-500">{{ $item->bottle_name }} · {{ $item->cap_name }}</p>
                                    @else
                                        <p class="text-neutral-800">{{ $item->product_name }}</p>
                                        <p class="text-neutral-500">{{ $item->size_name }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-neutral-600">{{ $item->quantity }}</td>
                                <td class="px-5 py-3 text-right text-neutral-800">₹{{ number_format($item->final_price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="space-y-1.5 border-t border-neutral-100 px-5 py-4 text-sm">
                    <div class="flex justify-between text-neutral-600"><span>Subtotal</span><span>₹{{ number_format($order->subtotal, 2) }}</span></div>
                    <div class="flex justify-between text-neutral-600"><span>Discount</span><span>-₹{{ number_format($order->discount, 2) }}</span></div>
                    <div class="flex justify-between text-neutral-600"><span>Tax (included)</span><span>₹{{ number_format($order->tax, 2) }}</span></div>
                    <div class="flex justify-between text-neutral-600"><span>Shipping ({{ $order->shipping_method_label }})</span><span>₹{{ number_format($order->shipping, 2) }}</span></div>
                    <div class="flex justify-between border-t border-neutral-100 pt-2 font-medium text-neutral-900"><span>Total</span><span>₹{{ number_format($order->final_price, 2) }}</span></div>
                </div>
            </div>

            <div class="rounded-lg border border-neutral-200 bg-white p-5">
                <h2 class="mb-3 font-medium text-neutral-800">Shipping Address</h2>
                <p class="text-sm text-neutral-700">{{ $order->customer_full_name }}</p>
                <p class="text-sm text-neutral-600">{{ $order->customer_mobile }} · {{ $order->customer_email }}</p>
                <p class="mt-2 text-sm text-neutral-600">{{ $order->customer_address }}</p>
                <p class="text-sm text-neutral-600">{{ $order->customer_city }}, {{ $order->customer_state }} {{ $order->customer_pincode }}</p>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-lg border border-neutral-200 bg-white p-5">
                <h2 class="mb-3 font-medium text-neutral-800">Order Status</h2>
                <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="space-y-3">
                    @csrf @method('PATCH')
                    <x-admin.select name="status" label="Status" :options="['PLACED' => 'Placed', 'CONFIRMED' => 'Confirmed', 'SHIPPED' => 'Shipped', 'DELIVERED' => 'Delivered', 'CANCELLED' => 'Cancelled']" :selected="$order->status" required />
                    <button type="submit" class="w-full rounded-md bg-neutral-900 px-4 py-2.5 text-xs font-medium uppercase tracking-wider text-white hover:bg-neutral-700">Update Status</button>
                </form>
            </div>
            <div class="rounded-lg border border-neutral-200 bg-white p-5 text-sm">
                <dl class="space-y-2">
                    <div class="flex justify-between"><dt class="text-neutral-500">Order #</dt><dd class="text-neutral-800">{{ $order->order_number }}</dd></div>
                    <div class="flex justify-between"><dt class="text-neutral-500">Placed</dt><dd class="text-neutral-800">{{ $order->created_at->format('d M Y, H:i') }}</dd></div>
                    <div class="flex justify-between"><dt class="text-neutral-500">Payment</dt><dd class="text-neutral-800">{{ $order->payment_method }}</dd></div>
                    @if ($order->coupon_code)
                        <div class="flex justify-between"><dt class="text-neutral-500">Coupon</dt><dd class="text-neutral-800">{{ $order->coupon_code }}</dd></div>
                    @endif
                </dl>
            </div>
        </div>
    </div>
</x-admin-layout>
