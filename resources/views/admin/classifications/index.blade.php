<x-admin-layout title="Classifications">
    <x-admin.page-header title="Classifications" :create-route="route('admin.classifications.create')" />

    <div class="space-y-6">
        @foreach ($groups as $key => $label)
            <div class="overflow-hidden rounded-lg border border-neutral-200 bg-white">
                <div class="border-b border-neutral-100 bg-neutral-50 px-5 py-3">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-neutral-600">{{ $label }}</h3>
                </div>
                <table class="w-full text-sm">
                    <tbody>
                        @forelse (($classifications[$key] ?? []) as $item)
                            <tr class="border-b border-neutral-50 last:border-0 hover:bg-neutral-50">
                                <td class="px-5 py-2.5 font-mono text-xs text-neutral-400">{{ $item->id }}</td>
                                <td class="px-5 py-2.5 text-neutral-800">{{ $item->name }}</td>
                                <td class="px-5 py-2.5 text-neutral-500">{{ $item->slug }}</td>
                                <td class="px-5 py-2.5">
                                    <span class="rounded-full {{ $item->status === 'ACTIVE' ? 'bg-emerald-50 text-emerald-700' : 'bg-neutral-100 text-neutral-500' }} px-2.5 py-1 text-xs">{{ $item->status }}</span>
                                </td>
                                <td class="px-5 py-2.5 text-right">
                                    <a href="{{ route('admin.classifications.edit', $item) }}" class="text-xs font-medium text-neutral-600 hover:text-neutral-900">Edit</a>
                                    <form action="{{ route('admin.classifications.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Delete?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="ml-3 text-xs font-medium text-rose-600 hover:text-rose-800">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-4 text-center text-xs text-neutral-400">None yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>
</x-admin-layout>
