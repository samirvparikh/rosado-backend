<x-admin-layout title="Fragrances">
    <x-admin.page-header title="Fragrances" :create-route="route('admin.fragrances.create')" />

    <div class="overflow-hidden rounded-lg border border-neutral-200 bg-white">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-neutral-100 bg-neutral-50 text-left text-xs uppercase tracking-wider text-neutral-500">
                    <th class="px-5 py-3">Name</th>
                    <th class="px-5 py-3">Gender</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($fragrances as $fragrance)
                    <tr class="border-b border-neutral-50 last:border-0 hover:bg-neutral-50">
                        <td class="px-5 py-3 text-neutral-800">{{ $fragrance->name }}<span class="ml-2 font-mono text-xs text-neutral-400">{{ $fragrance->id }}</span></td>
                        <td class="px-5 py-3 text-neutral-600">{{ $fragrance->gender }}</td>
                        <td class="px-5 py-3">
                            <span class="rounded-full {{ $fragrance->status === 'ACTIVE' ? 'bg-emerald-50 text-emerald-700' : 'bg-neutral-100 text-neutral-500' }} px-2.5 py-1 text-xs">{{ $fragrance->status }}</span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('admin.fragrances.edit', $fragrance) }}" class="text-xs font-medium text-neutral-600 hover:text-neutral-900">Edit</a>
                            <form action="{{ route('admin.fragrances.destroy', $fragrance) }}" method="POST" class="inline" onsubmit="return confirm('Delete this fragrance?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="ml-3 text-xs font-medium text-rose-600 hover:text-rose-800">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-8 text-center text-neutral-400">No fragrances yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>
