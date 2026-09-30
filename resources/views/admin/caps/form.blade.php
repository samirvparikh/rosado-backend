<x-admin-layout :title="$cap ? 'Edit Cap' : 'New Cap'">
    <div class="max-w-xl rounded-lg border border-neutral-200 bg-white p-6">
        <form method="POST" action="{{ $cap ? route('admin.caps.update', $cap) : route('admin.caps.store') }}" class="space-y-4">
            @csrf
            @if ($cap) @method('PUT') @endif

            @if (!$cap)
                <x-admin.field name="id" label="ID (e.g. CAP004)" required />
            @endif
            <x-admin.field name="name" label="Name" :value="$cap?->name" required />
            <x-admin.field name="code" label="Code" :value="$cap?->code" required />
            <x-admin.field name="image" label="Image URL" :value="$cap?->image" />
            <x-admin.field name="additional_price" label="Additional Price (₹)" type="number" step="0.01" :value="$cap?->additional_price ?? 0" required />
            <x-admin.field name="stock" label="Stock" type="number" :value="$cap?->stock ?? 0" required />
            <x-admin.field name="sort_order" label="Sort Order" type="number" :value="$cap?->sort_order ?? 0" required />
            <x-admin.select name="status" label="Status" :options="['ACTIVE' => 'Active', 'INACTIVE' => 'Inactive']" :selected="$cap?->status ?? 'ACTIVE'" required />

            <div class="flex gap-3 pt-2">
                <button type="submit" class="rounded-md bg-neutral-900 px-5 py-2.5 text-xs font-medium uppercase tracking-wider text-white hover:bg-neutral-700">Save</button>
                <a href="{{ route('admin.caps.index') }}" class="rounded-md border border-neutral-300 px-5 py-2.5 text-xs font-medium uppercase tracking-wider text-neutral-600 hover:bg-neutral-50">Cancel</a>
            </div>
        </form>
    </div>
</x-admin-layout>
