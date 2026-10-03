<x-admin-layout title="Products">
    <x-admin.page-header title="Products" :create-route="route('admin.products.create')" />

    <div class="overflow-hidden rounded-lg border border-neutral-200 bg-white">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-neutral-100 bg-neutral-50 text-left text-xs uppercase tracking-wider text-neutral-500">
                    <th class="px-5 py-3"></th>
                    <th class="px-5 py-3">Name</th>
                    <th class="px-5 py-3">SKU</th>
                    <th class="px-5 py-3">Type</th>
                    <th class="px-5 py-3">Rating</th>
                    <th class="px-5 py-3">Online</th>
                    <th class="px-5 py-3">Stock</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr class="border-b border-neutral-50 last:border-0 hover:bg-neutral-50">
                        <td class="px-5 py-2.5">
                            @php $primary = $product->images->firstWhere('is_primary', true) ?? $product->images->first(); @endphp
                            @if ($primary)
                                <img src="{{ $primary->image_url }}" alt="" class="h-10 w-10 rounded object-cover">
                            @else
                                <div class="h-10 w-10 rounded bg-neutral-100"></div>
                            @endif
                        </td>
                        <td class="px-5 py-2.5 text-neutral-800">{{ $product->name }}</td>
                        <td class="px-5 py-2.5 font-mono text-xs text-neutral-500">{{ $product->sku }}</td>
                        <td class="px-5 py-2.5 text-neutral-600">{{ $product->product_type }}</td>
                        <td class="px-5 py-2.5 text-neutral-600">★ {{ $product->rating }} ({{ $product->review_count }})</td>
                        <td class="px-5 py-2.5">
                            <span class="rounded-full {{ $product->status === 'ACTIVE' ? 'bg-emerald-50 text-emerald-700' : 'bg-neutral-100 text-neutral-500' }} px-2.5 py-1 text-xs">{{ $product->status === 'ACTIVE' ? 'Online' : 'Offline' }}</span>
                        </td>
                        <td class="px-5 py-2.5">
                            <span class="rounded-full {{ $product->in_stock ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }} px-2.5 py-1 text-xs">{{ $product->in_stock ? 'In Stock' : 'Sold Out' }}</span>
                        </td>
                        <td class="px-5 py-2.5 text-right">
                            <a href="{{ route('admin.products.edit', $product) }}" class="text-xs font-medium text-neutral-600 hover:text-neutral-900">Edit</a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Delete this product?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="ml-3 text-xs font-medium text-rose-600 hover:text-rose-800">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-5 py-8 text-center text-neutral-400">No products yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>
