<x-admin-layout title="Admin Users">
    <x-admin.page-header title="Admin Users" :create-route="route('admin.admins.create')" />
    <p class="mb-4 -mt-4 text-xs text-neutral-500">Accounts that can sign in to this admin panel. Customers who sign up on the website are listed under Customers.</p>

    @error('admin')
        <p class="mb-4 rounded-md border border-rose-200 bg-rose-50 px-4 py-2.5 text-sm text-rose-700">{{ $message }}</p>
    @enderror

    <div class="overflow-hidden rounded-lg border border-neutral-200 bg-white">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-neutral-100 bg-neutral-50 text-left text-xs uppercase tracking-wider text-neutral-500">
                    <th class="px-5 py-3">Name</th>
                    <th class="px-5 py-3">Email</th>
                    <th class="px-5 py-3">Mobile</th>
                    <th class="px-5 py-3">Created</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($admins as $admin)
                    <tr class="border-b border-neutral-50 last:border-0 hover:bg-neutral-50">
                        <td class="px-5 py-3 text-neutral-800">
                            {{ $admin->name }}
                            @if ($admin->is(auth()->user()))
                                <span class="ml-2 rounded-full bg-amber-50 px-2 py-0.5 text-[11px] text-amber-700">You</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-neutral-600">{{ $admin->email }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $admin->mobile ?: '—' }}</td>
                        <td class="px-5 py-3 text-neutral-500">{{ $admin->created_at?->format('d M Y') }}</td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('admin.admins.edit', $admin) }}" class="text-xs font-medium text-neutral-600 hover:text-neutral-900">Edit</a>
                            @unless ($admin->is(auth()->user()))
                                <form action="{{ route('admin.admins.destroy', $admin) }}" method="POST" class="inline" onsubmit="return confirm('Delete admin user {{ $admin->email }}? They will no longer be able to sign in.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="ml-3 text-xs font-medium text-rose-600 hover:text-rose-800">Delete</button>
                                </form>
                            @endunless
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-neutral-400">No admin users.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>
