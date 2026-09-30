<x-admin-layout :title="$shippingMethod ? 'Edit Shipping Method' : 'New Shipping Method'">
    <div class="max-w-xl rounded-lg border border-neutral-200 bg-white p-6">
        <form method="POST" action="{{ $shippingMethod ? route('admin.shipping-methods.update', $shippingMethod) : route('admin.shipping-methods.store') }}" class="space-y-4">
            @csrf
            @if ($shippingMethod) @method('PUT') @endif

            @if (!$shippingMethod)
                <x-admin.field name="id" label="ID (e.g. overnight)" required />
            @endif
            <x-admin.field name="name" label="Name" :value="$shippingMethod?->name" required />
            <x-admin.field name="description" label="Description" :value="$shippingMethod?->description" required />
            <x-admin.field name="price" label="Price (₹)" type="number" step="0.01" :value="$shippingMethod?->price ?? 0" required />
            <x-admin.field name="eta" label="ETA (e.g. 4–6 days)" :value="$shippingMethod?->eta" required />
            <x-admin.field name="sort_order" label="Sort Order" type="number" :value="$shippingMethod?->sort_order ?? 0" required />
            <x-admin.select name="status" label="Status" :options="['ACTIVE' => 'Active', 'INACTIVE' => 'Inactive']" :selected="$shippingMethod?->status ?? 'ACTIVE'" required />

            <div class="flex gap-3 pt-2">
                <button type="submit" class="rounded-md bg-neutral-900 px-5 py-2.5 text-xs font-medium uppercase tracking-wider text-white hover:bg-neutral-700">Save</button>
                <a href="{{ route('admin.shipping-methods.index') }}" class="rounded-md border border-neutral-300 px-5 py-2.5 text-xs font-medium uppercase tracking-wider text-neutral-600 hover:bg-neutral-50">Cancel</a>
            </div>
        </form>
    </div>
</x-admin-layout>
