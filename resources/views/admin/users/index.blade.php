<x-admin-layout title="Customers">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-medium text-neutral-800">Customers</h2>
            <p class="text-xs text-neutral-500">Customers who signed up on the website. Admin panel accounts are under Admin Users.</p>
        </div>
        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ $search }}" placeholder="Name, email, mobile, city, pincode"
                   class="w-72 rounded-md border border-neutral-300 px-3 py-2 text-sm focus:border-neutral-500 focus:outline-none focus:ring-1 focus:ring-neutral-500">
            <button type="submit" class="rounded-md bg-neutral-900 px-4 py-2 text-xs font-medium uppercase tracking-wider text-white hover:bg-neutral-700">Search</button>
            @if ($search !== '')
                <a href="{{ route('admin.users.index') }}" class="rounded-md border border-neutral-300 px-4 py-2 text-xs font-medium uppercase tracking-wider text-neutral-600 hover:bg-neutral-50">Clear</a>
            @endif
        </form>
    </div>

    <div class="overflow-x-auto rounded-lg border border-neutral-200 bg-white">
        <table class="w-full min-w-[1280px] text-sm">
            <thead>
                <tr class="border-b border-neutral-100 bg-neutral-50 text-left text-xs uppercase tracking-wider text-neutral-500">
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Mobile</th>
                    <th class="px-4 py-3">Address</th>
                    <th class="px-4 py-3">City</th>
                    <th class="px-4 py-3">State</th>
                    <th class="px-4 py-3">Pincode</th>
                    <th class="px-4 py-3 text-right">Orders</th>
                    <th class="px-4 py-3 text-right">Total Spent</th>
                    <th class="px-4 py-3">Last Order</th>
                    <th class="px-4 py-3">Joined</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    @php
                        $address = $user->addresses->first();
                        $others = $user->addresses->count() - 1;
                    @endphp
                    <tr class="border-b border-neutral-50 align-top last:border-0 hover:bg-neutral-50">
                        <td class="px-4 py-3 font-mono text-xs text-neutral-400">{{ $user->id }}</td>
                        <td class="px-4 py-3 font-medium text-neutral-800">{{ $user->name }}</td>
                        <td class="px-4 py-3 text-neutral-600"><a href="mailto:{{ $user->email }}" class="hover:underline">{{ $user->email }}</a></td>
                        <td class="whitespace-nowrap px-4 py-3 text-neutral-600">{{ $user->mobile ?: '—' }}</td>
                        <td class="max-w-[260px] px-4 py-3 text-neutral-600">
                            @if ($address)
                                <p>{{ $address->address_line }}</p>
                                @if ($address->full_name !== $user->name || $address->mobile !== $user->mobile)
                                    <p class="mt-0.5 text-xs text-neutral-400">Ship to: {{ $address->full_name }} · {{ $address->mobile }}</p>
                                @endif
                                @if ($others > 0)
                                    <p class="mt-0.5 text-xs text-neutral-400">+ {{ $others }} more saved address{{ $others > 1 ? 'es' : '' }}</p>
                                @endif
                            @else
                                <span class="text-xs text-neutral-400">No address yet</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-neutral-600">{{ $address?->city ?? '—' }}</td>
                        <td class="px-4 py-3 text-neutral-600">{{ $address?->state ?? '—' }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-neutral-600">{{ $address?->pincode ?? '—' }}</td>
                        <td class="px-4 py-3 text-right text-neutral-800">
                            @if ($user->orders_count)
                                <a href="{{ route('admin.orders.index', ['search' => $user->email]) }}" class="hover:underline" title="View this customer's orders">{{ $user->orders_count }}</a>
                            @else
                                <span class="text-neutral-400">0</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right text-neutral-800">₹{{ number_format((float) $user->total_spent, 2) }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-neutral-500">{{ $user->last_order_at ? \Illuminate\Support\Carbon::parse($user->last_order_at)->format('d M Y') : '—' }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-neutral-500">{{ $user->created_at->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="12" class="px-5 py-8 text-center text-neutral-400">{{ $search !== '' ? 'No customers match your search.' : 'No customers yet.' }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>
</x-admin-layout>
