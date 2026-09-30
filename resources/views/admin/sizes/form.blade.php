<x-admin-layout :title="$size ? 'Edit Size' : 'New Size'">
    <div class="max-w-xl rounded-lg border border-neutral-200 bg-white p-6">
        <form method="POST" action="{{ $size ? route('admin.sizes.update', $size) : route('admin.sizes.store') }}" class="space-y-4">
            @csrf
            @if ($size) @method('PUT') @endif

            @if (!$size)
                <x-admin.field name="id" label="ID (e.g. SIZE30)" required />
            @endif
            <x-admin.field name="name" label="Name" :value="$size?->name" required />
            <x-admin.field name="display_name" label="Display Name" :value="$size?->display_name" required />
            <x-admin.field name="size_ml" label="Size (ML)" type="number" :value="$size?->size_ml" required />
            <x-admin.field name="sort_order" label="Sort Order" type="number" :value="$size?->sort_order ?? 0" required />
            <x-admin.select name="status" label="Status" :options="['ACTIVE' => 'Active', 'INACTIVE' => 'Inactive']" :selected="$size?->status ?? 'ACTIVE'" required />

            <div class="flex gap-3 pt-2">
                <button type="submit" class="rounded-md bg-neutral-900 px-5 py-2.5 text-xs font-medium uppercase tracking-wider text-white hover:bg-neutral-700">Save</button>
                <a href="{{ route('admin.sizes.index') }}" class="rounded-md border border-neutral-300 px-5 py-2.5 text-xs font-medium uppercase tracking-wider text-neutral-600 hover:bg-neutral-50">Cancel</a>
            </div>
        </form>
    </div>
</x-admin-layout>
