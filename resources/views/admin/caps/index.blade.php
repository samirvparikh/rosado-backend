<x-admin-layout title="Caps">
    <x-admin.page-header title="Caps" :create-route="route('admin.caps.create')" />
    <p class="mb-4 -mt-4 text-xs text-neutral-500">Cap is a Custom Perfume component, never a standalone product -- it only appears inside the builder.</p>

    <div class="overflow-hidden rounded-lg border border-neutral-200 bg-white">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-neutral-100 bg-neutral-50 text-left text-xs uppercase tracking-wider text-neutral-500">
                    <th class="px-5 py-3">Name</th>
                    <th class="px-5 py-3">+ Price</th>
                    <th class="px-5 py-3">Available</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($caps as $cap)
                    <tr class="border-b border-neutral-50 last:border-0 hover:bg-neutral-50">
                        <td class="px-5 py-3 text-neutral-800">{{ $cap->name }}<span class="ml-2 font-mono text-xs text-neutral-400">{{ $cap->code }}</span></td>
                        <td class="px-5 py-3 text-neutral-600">₹{{ number_format($cap->additional_price, 2) }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $cap->inventory?->available_stock ?? 0 }}</td>
                        <td class="px-5 py-3">
                            <span class="rounded-full {{ $cap->status === 'ACTIVE' ? 'bg-emerald-50 text-emerald-700' : 'bg-neutral-100 text-neutral-500' }} px-2.5 py-1 text-xs">{{ $cap->status }}</span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('admin.caps.edit', $cap) }}" class="text-xs font-medium text-neutral-600 hover:text-neutral-900">Edit</a>
                            <form action="{{ route('admin.caps.destroy', $cap) }}" method="POST" class="inline" onsubmit="return confirm('Delete this cap?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="ml-3 text-xs font-medium text-rose-600 hover:text-rose-800">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-neutral-400">No caps yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>
