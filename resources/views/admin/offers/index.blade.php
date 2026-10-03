<x-admin-layout title="Offer Header">
    <x-admin.page-header title="Offer Header" :create-route="route('admin.offers.create')" />

    <p class="mb-4 text-sm text-neutral-500">Active offers scroll as marquee text in a strip above the storefront header, in sort order.</p>

    <div class="overflow-hidden rounded-lg border border-neutral-200 bg-white">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-neutral-100 bg-neutral-50 text-left text-xs uppercase tracking-wider text-neutral-500">
                    <th class="px-5 py-3">Text</th>
                    <th class="px-5 py-3">Link</th>
                    <th class="px-5 py-3">Sort</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($offers as $offer)
                    <tr class="border-b border-neutral-50 last:border-0 hover:bg-neutral-50">
                        <td class="px-5 py-3 text-neutral-800">{{ $offer->text }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $offer->link_url ?: '—' }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $offer->sort_order }}</td>
                        <td class="px-5 py-3">
                            <span class="rounded-full {{ $offer->status === 'ACTIVE' ? 'bg-emerald-50 text-emerald-700' : 'bg-neutral-100 text-neutral-500' }} px-2.5 py-1 text-xs">{{ $offer->status }}</span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('admin.offers.edit', $offer) }}" class="text-xs font-medium text-neutral-600 hover:text-neutral-900">Edit</a>
                            <form action="{{ route('admin.offers.destroy', $offer) }}" method="POST" class="inline" onsubmit="return confirm('Delete this offer?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="ml-3 text-xs font-medium text-rose-600 hover:text-rose-800">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-neutral-400">No offers yet. The offer header stays hidden until one is active.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>
