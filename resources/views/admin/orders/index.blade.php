<x-admin-layout title="Orders">
    <div class="mb-6 flex items-center justify-between gap-4">
        <h2 class="text-lg font-medium text-neutral-800">Orders</h2>
        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search order # / customer"
                   class="rounded-md border border-neutral-300 px-3 py-2 text-sm focus:border-neutral-500 focus:outline-none focus:ring-1 focus:ring-neutral-500">
            <select name="status" class="rounded-md border border-neutral-300 bg-white px-3 py-2 text-sm">
                <option value="">All statuses</option>
                @foreach (['PLACED', 'CONFIRMED', 'SHIPPED', 'DELIVERED', 'CANCELLED'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-md bg-neutral-900 px-4 py-2 text-xs font-medium uppercase tracking-wider text-white hover:bg-neutral-700">Filter</button>
        </form>
    </div>

    <div class="overflow-hidden rounded-lg border border-neutral-200 bg-white">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-neutral-100 bg-neutral-50 text-left text-xs uppercase tracking-wider text-neutral-500">
                    <th class="px-5 py-3">Order</th>
                    <th class="px-5 py-3">Customer</th>
                    <th class="px-5 py-3">Placed</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr class="cursor-pointer border-b border-neutral-50 last:border-0 hover:bg-neutral-50" onclick="window.location='{{ route('admin.orders.show', $order) }}'">
                        <td class="px-5 py-3 font-medium text-neutral-800">{{ $order->order_number }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $order->customer_full_name }}</td>
                        <td class="px-5 py-3 text-neutral-500">{{ $order->created_at->format('d M Y, H:i') }}</td>
                        <td class="px-5 py-3">
                            <span class="rounded-full bg-neutral-100 px-2.5 py-1 text-xs text-neutral-700">{{ $order->status }}</span>
                        </td>
                        <td class="px-5 py-3 text-right text-neutral-800">₹{{ number_format($order->final_price, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-neutral-400">No orders found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $orders->links() }}</div>
</x-admin-layout>
