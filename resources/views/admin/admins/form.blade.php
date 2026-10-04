<x-admin-layout :title="$admin ? 'Edit Admin User' : 'New Admin User'">
    <div class="max-w-xl rounded-lg border border-neutral-200 bg-white p-6">
        <form method="POST" action="{{ $admin ? route('admin.admins.update', $admin) : route('admin.admins.store') }}" class="space-y-4" autocomplete="off">
            @csrf
            @if ($admin) @method('PUT') @endif

            <x-admin.field name="name" label="Name" :value="$admin?->name" required />
            <x-admin.field name="email" label="Email (used to sign in)" type="email" :value="$admin?->email" required />
            <x-admin.field name="mobile" label="Mobile" :value="$admin?->mobile" />

            <fieldset class="rounded-md border border-neutral-200 p-4">
                <legend class="px-1 text-xs font-medium uppercase tracking-wider text-neutral-500">Password</legend>
                @if ($admin)
                    <p class="mb-3 text-xs text-neutral-400">Leave blank to keep the current password.</p>
                @endif
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <x-admin.field name="password" label="Password" type="password" :required="! $admin" autocomplete="new-password" />
                    <x-admin.field name="password_confirmation" label="Confirm Password" type="password" :required="! $admin" autocomplete="new-password" />
                </div>
                <p class="mt-2 text-xs text-neutral-400">At least 8 characters.</p>
            </fieldset>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="rounded-md bg-neutral-900 px-5 py-2.5 text-xs font-medium uppercase tracking-wider text-white hover:bg-neutral-700">Save</button>
                <a href="{{ route('admin.admins.index') }}" class="rounded-md border border-neutral-300 px-5 py-2.5 text-xs font-medium uppercase tracking-wider text-neutral-600 hover:bg-neutral-50">Cancel</a>
            </div>
        </form>
    </div>
</x-admin-layout>
