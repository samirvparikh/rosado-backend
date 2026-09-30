<x-admin-layout title="Customers">
    <x-admin.page-header title="Customers" />

    <div class="overflow-hidden rounded-lg border border-neutral-200 bg-white">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-neutral-100 bg-neutral-50 text-left text-xs uppercase tracking-wider text-neutral-500">
                    <th class="px-5 py-3">Name</th>
                    <th class="px-5 py-3">Email</th>
                    <th class="px-5 py-3">Mobile</th>
                    <th class="px-5 py-3">Orders</th>
                    <th class="px-5 py-3">Joined</th>
                    <th class="px-5 py-3">Admin</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr class="border-b border-neutral-50 last:border-0 hover:bg-neutral-50">
                        <td class="px-5 py-3 text-neutral-800">{{ $user->name }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $user->email }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $user->mobile }}</td>
                        <td class="px-5 py-3 text-neutral-600">{{ $user->orders_count }}</td>
                        <td class="px-5 py-3 text-neutral-500">{{ $user->created_at->format('d M Y') }}</td>
                        <td class="px-5 py-3">
                            @if ($user->is_admin)
                                <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs text-amber-700">Admin</span>
                            @else
                                <span class="rounded-full bg-neutral-100 px-2.5 py-1 text-xs text-neutral-500">Customer</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            <form action="{{ route('admin.users.update', $user) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <input type="hidden" name="is_admin" value="{{ $user->is_admin ? '0' : '1' }}">
                                <button type="submit" class="text-xs font-medium text-neutral-600 hover:text-neutral-900">
                                    {{ $user->is_admin ? 'Revoke admin' : 'Make admin' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-8 text-center text-neutral-400">No customers yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>
</x-admin-layout>
