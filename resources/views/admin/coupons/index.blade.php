<x-admin-layout title="Coupons">
    <x-admin.page-header title="Coupons" :create-route="route('admin.coupons.create')" />

    <div class="overflow-hidden rounded-lg border border-neutral-200 bg-white">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-neutral-100 bg-neutral-50 text-left text-xs uppercase tracking-wider text-neutral-500">
                    <th class="px-5 py-3">Code</th>
                    <th class="px-5 py-3">Type</th>
                    <th class="px-5 py-3">Value</th>
                    <th class="px-5 py-3">Min Subtotal</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($coupons as $coupon)
                    <tr class="border-b border-neutral-50 last:border-0 hover:bg-neutral-50">
                        <td class="px-5 py-3 font-mono text-neutral-800">{{ $coupon->code }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $coupon->type }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $coupon->type === 'PERCENT' ? $coupon->value.'%' : '₹'.number_format($coupon->value, 2) }}</td>
                        <td class="px-5 py-3 text-neutral-600">₹{{ number_format($coupon->min_subtotal, 2) }}</td>
                        <td class="px-5 py-3">
                            <span class="rounded-full {{ $coupon->status === 'ACTIVE' ? 'bg-emerald-50 text-emerald-700' : 'bg-neutral-100 text-neutral-500' }} px-2.5 py-1 text-xs">{{ $coupon->status }}</span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('admin.coupons.edit', $coupon) }}" class="text-xs font-medium text-neutral-600 hover:text-neutral-900">Edit</a>
                            <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="inline" onsubmit="return confirm('Delete this coupon?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="ml-3 text-xs font-medium text-rose-600 hover:text-rose-800">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-neutral-400">No coupons yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>
