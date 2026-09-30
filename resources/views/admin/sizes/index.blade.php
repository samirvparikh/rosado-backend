<x-admin-layout title="Sizes">
    <x-admin.page-header title="Sizes" :create-route="route('admin.sizes.create')" />

    <div class="overflow-hidden rounded-lg border border-neutral-200 bg-white">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-neutral-100 bg-neutral-50 text-left text-xs uppercase tracking-wider text-neutral-500">
                    <th class="px-5 py-3">ID</th>
                    <th class="px-5 py-3">Display Name</th>
                    <th class="px-5 py-3">ML</th>
                    <th class="px-5 py-3">Sort</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sizes as $size)
                    <tr class="border-b border-neutral-50 last:border-0 hover:bg-neutral-50">
                        <td class="px-5 py-3 font-mono text-xs text-neutral-500">{{ $size->id }}</td>
                        <td class="px-5 py-3 text-neutral-800">{{ $size->display_name }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $size->size_ml }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $size->sort_order }}</td>
                        <td class="px-5 py-3">
                            <span class="rounded-full {{ $size->status === 'ACTIVE' ? 'bg-emerald-50 text-emerald-700' : 'bg-neutral-100 text-neutral-500' }} px-2.5 py-1 text-xs">{{ $size->status }}</span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('admin.sizes.edit', $size) }}" class="text-xs font-medium text-neutral-600 hover:text-neutral-900">Edit</a>
                            <form action="{{ route('admin.sizes.destroy', $size) }}" method="POST" class="inline" onsubmit="return confirm('Delete this size?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="ml-3 text-xs font-medium text-rose-600 hover:text-rose-800">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-neutral-400">No sizes yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>
