<x-admin-layout :title="$cap ? 'Edit Cap' : 'New Cap'">
    <div class="grid max-w-5xl grid-cols-1 items-start gap-6 lg:grid-cols-[minmax(0,1fr)_340px]">
    <div class="rounded-lg border border-neutral-200 bg-white p-6">
        <form method="POST" action="{{ $cap ? route('admin.caps.update', $cap) : route('admin.caps.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @if ($cap) @method('PUT') @endif

            @if (!$cap)
                <x-admin.field name="id" label="ID (e.g. CAP004)" required />
            @endif
            <x-admin.field name="name" label="Name" :value="$cap?->name" required />
            <x-admin.field name="code" label="Code" :value="$cap?->code" required />
            <x-admin.image-upload name="image_file" :current="$cap?->image" spec="cap"
                hint="Placed over the selected bottle on the customizer canvas." />
            <x-admin.field name="image" label="or Image URL" :value="$cap?->image" />
            <x-admin.field name="additional_price" label="Additional Price (₹)" type="number" step="0.01" :value="$cap?->additional_price ?? 0" required />
            <x-admin.field name="stock" label="Stock" type="number" :value="$cap?->stock ?? 0" required />
            <x-admin.field name="sort_order" label="Sort Order" type="number" :value="$cap?->sort_order ?? 0" required />
            <x-admin.select name="status" label="Status" :options="['ACTIVE' => 'Active', 'INACTIVE' => 'Inactive']" :selected="$cap?->status ?? 'ACTIVE'" required />

            <x-admin.layer-fields :model="$cap" title="Default cap position on preview canvas" :defaults="['top' => 20, 'left' => 41.7, 'width' => 16.6, 'z' => 30]"
                hint="Used on any bottle without its own alignment for this cap. Per-bottle fits are saved from the Alignment Tool." />
            @if ($cap)
                <a href="{{ route('admin.customizer.alignment', ['cap' => $cap->id]) }}" class="inline-block text-xs font-medium text-amber-700 hover:text-amber-900">Align on each bottle in the Alignment Tool →</a>
            @endif

            <div class="flex gap-3 pt-2">
                <button type="submit" class="rounded-md bg-neutral-900 px-5 py-2.5 text-xs font-medium uppercase tracking-wider text-white hover:bg-neutral-700">Save</button>
                <a href="{{ route('admin.caps.index') }}" class="rounded-md border border-neutral-300 px-5 py-2.5 text-xs font-medium uppercase tracking-wider text-neutral-600 hover:bg-neutral-50">Cancel</a>
            </div>
        </form>
    </div>
    <div class="lg:sticky lg:top-6">
        <x-admin.layer-canvas kind="cap" :image="old('image', $cap?->image)" :refs="$canvasRefs" />
    </div>
    </div>
</x-admin-layout>
