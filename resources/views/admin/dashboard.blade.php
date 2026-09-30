<x-admin-layout title="Dashboard">
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-lg border border-neutral-200 bg-white p-5">
            <p class="text-xs font-medium uppercase tracking-wider text-neutral-500">Products</p>
            <p class="mt-2 font-serif text-3xl text-neutral-900">{{ number_format($stats['products']) }}</p>
        </div>
        <div class="rounded-lg border border-neutral-200 bg-white p-5">
            <p class="text-xs font-medium uppercase tracking-wider text-neutral-500">Orders</p>
            <p class="mt-2 font-serif text-3xl text-neutral-900">{{ number_format($stats['orders']) }}</p>
        </div>
        <div class="rounded-lg border border-neutral-200 bg-white p-5">
            <p class="text-xs font-medium uppercase tracking-wider text-neutral-500">Customers</p>
            <p class="mt-2 font-serif text-3xl text-neutral-900">{{ number_format($stats['customers']) }}</p>
        </div>
        <div class="rounded-lg border border-neutral-200 bg-white p-5">
            <p class="text-xs font-medium uppercase tracking-wider text-neutral-500">Revenue</p>
            <p class="mt-2 font-serif text-3xl text-neutral-900">₹{{ number_format($stats['revenue']) }}</p>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="rounded-lg border border-neutral-200 bg-white lg:col-span-2">
            <div class="flex items-center justify-between border-b border-neutral-200 px-5 py-4">
                <h2 class="font-medium text-neutral-800">Recent Orders</h2>
                <a href="{{ route('admin.orders.index') }}" class="text-xs uppercase tracking-wider text-neutral-500 hover:text-neutral-800">View all</a>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-neutral-100 text-left text-xs uppercase tracking-wider text-neutral-500">
                        <th class="px-5 py-3">Order</th>
                        <th class="px-5 py-3">Customer</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentOrders as $order)
                        <tr class="border-b border-neutral-50 last:border-0 hover:bg-neutral-50">
                            <td class="px-5 py-3">
                                <a href="{{ route('admin.orders.show', $order) }}" class="font-medium text-neutral-800 hover:underline">{{ $order->order_number }}</a>
                            </td>
                            <td class="px-5 py-3 text-neutral-600">{{ $order->customer_full_name }}</td>
                            <td class="px-5 py-3">
                                <span class="rounded-full bg-neutral-100 px-2.5 py-1 text-xs text-neutral-700">{{ $order->status }}</span>
                            </td>
                            <td class="px-5 py-3 text-right text-neutral-800">₹{{ number_format($order->final_price, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-6 text-center text-neutral-400">No orders yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="space-y-6">
            <div class="rounded-lg border border-neutral-200 bg-white">
                <div class="border-b border-neutral-200 px-5 py-4">
                    <h2 class="font-medium text-neutral-800">Low Stock Bottles</h2>
                </div>
                <ul class="divide-y divide-neutral-50 text-sm">
                    @forelse ($lowStockBottles as $inv)
                        <li class="flex items-center justify-between px-5 py-3">
                            <span class="text-neutral-700">{{ $inv->bottle?->name }}</span>
                            <span class="text-rose-600">{{ $inv->available_stock }} left</span>
                        </li>
                    @empty
                        <li class="px-5 py-4 text-center text-neutral-400">All stocked.</li>
                    @endforelse
                </ul>
            </div>
            <div class="rounded-lg border border-neutral-200 bg-white">
                <div class="border-b border-neutral-200 px-5 py-4">
                    <h2 class="font-medium text-neutral-800">Low Stock Caps</h2>
                </div>
                <ul class="divide-y divide-neutral-50 text-sm">
                    @forelse ($lowStockCaps as $inv)
                        <li class="flex items-center justify-between px-5 py-3">
                            <span class="text-neutral-700">{{ $inv->cap?->name }}</span>
                            <span class="text-rose-600">{{ $inv->available_stock }} left</span>
                        </li>
                    @empty
                        <li class="px-5 py-4 text-center text-neutral-400">All stocked.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</x-admin-layout>
