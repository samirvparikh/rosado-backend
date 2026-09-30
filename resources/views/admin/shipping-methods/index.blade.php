<x-admin-layout title="Shipping Methods">
    <x-admin.page-header title="Shipping Methods" :create-route="route('admin.shipping-methods.create')" />

    <div class="overflow-hidden rounded-lg border border-neutral-200 bg-white">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-neutral-100 bg-neutral-50 text-left text-xs uppercase tracking-wider text-neutral-500">
                    <th class="px-5 py-3">Name</th>
                    <th class="px-5 py-3">Description</th>
                    <th class="px-5 py-3">Price</th>
                    <th class="px-5 py-3">ETA</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($shippingMethods as $method)
                    <tr class="border-b border-neutral-50 last:border-0 hover:bg-neutral-50">
                        <td class="px-5 py-3 text-neutral-800">{{ $method->name }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $method->description }}</td>
                        <td class="px-5 py-3 text-neutral-600">₹{{ number_format($method->price, 2) }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $method->eta }}</td>
                        <td class="px-5 py-3">
                            <span class="rounded-full {{ $method->status === 'ACTIVE' ? 'bg-emerald-50 text-emerald-700' : 'bg-neutral-100 text-neutral-500' }} px-2.5 py-1 text-xs">{{ $method->status }}</span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('admin.shipping-methods.edit', $method) }}" class="text-xs font-medium text-neutral-600 hover:text-neutral-900">Edit</a>
                            <form action="{{ route('admin.shipping-methods.destroy', $method) }}" method="POST" class="inline" onsubmit="return confirm('Delete this shipping method?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="ml-3 text-xs font-medium text-rose-600 hover:text-rose-800">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-neutral-400">No shipping methods yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>
