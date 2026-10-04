<x-admin-layout :title="$bottle ? 'Edit Bottle' : 'New Bottle'">
    <div class="grid max-w-5xl grid-cols-1 items-start gap-6 lg:grid-cols-[minmax(0,1fr)_340px]">
    <div class="rounded-lg border border-neutral-200 bg-white p-6">
        <form method="POST" action="{{ $bottle ? route('admin.bottles.update', $bottle) : route('admin.bottles.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @if ($bottle) @method('PUT') @endif

            @if (!$bottle)
                <x-admin.field name="id" label="ID (e.g. BTL007)" required />
            @endif
            <x-admin.field name="name" label="Name" :value="$bottle?->name" required />
            <x-admin.field name="code" label="Code" :value="$bottle?->code" required />
            <x-admin.image-upload name="image_file" :current="$bottle?->image" spec="bottle"
                hint="Stacked with the cap and label on the customizer canvas." />
            <x-admin.field name="image" label="or Image URL" :value="$bottle?->image" />
            <x-admin.select name="size_id" label="Size" :options="$sizes" :selected="$bottle?->size_id" blank="Select a size" required />
            <x-admin.field name="additional_price" label="Additional Price (₹)" type="number" step="0.01" :value="$bottle?->additional_price ?? 0" required />
            <x-admin.field name="stock" label="Stock" type="number" :value="$bottle?->stock ?? 0" required />
            <x-admin.field name="sort_order" label="Sort Order" type="number" :value="$bottle?->sort_order ?? 0" required />
            <x-admin.select name="status" label="Status" :options="['ACTIVE' => 'Active', 'INACTIVE' => 'Inactive']" :selected="$bottle?->status ?? 'ACTIVE'" required />

            <x-admin.layer-fields :model="$bottle" title="Bottle position on preview canvas" :defaults="['top' => 30, 'left' => 27, 'width' => 46, 'z' => 10]"
                hint="Canvas %: top of canvas height, left/width of canvas width." />
            <fieldset class="rounded-md border border-neutral-200 p-4">
                <legend class="px-1 text-xs font-medium uppercase tracking-wider text-neutral-500">Label on bottle</legend>
                <p class="mb-3 text-xs text-neutral-400">Centre point and width of the printed label (fragrance name, size, customer text).</p>
                <div class="grid grid-cols-3 gap-3">
                    <x-admin.field name="label_top" label="Centre top (%)" type="number" step="0.01" :value="$bottle?->label_top ?? 66" required />
                    <x-admin.field name="label_left" label="Centre left (%)" type="number" step="0.01" :value="$bottle?->label_left ?? 50" required />
                    <x-admin.field name="label_width" label="Width (%)" type="number" step="0.01" :value="$bottle?->label_width ?? 30" required />
                </div>
            </fieldset>
            @if ($bottle)
                <a href="{{ route('admin.customizer.alignment', ['bottle' => $bottle->id]) }}" class="inline-block text-xs font-medium text-amber-700 hover:text-amber-900">Align visually with caps in the Alignment Tool →</a>
            @endif

            <div class="flex gap-3 pt-2">
                <button type="submit" class="rounded-md bg-neutral-900 px-5 py-2.5 text-xs font-medium uppercase tracking-wider text-white hover:bg-neutral-700">Save</button>
                <a href="{{ route('admin.bottles.index') }}" class="rounded-md border border-neutral-300 px-5 py-2.5 text-xs font-medium uppercase tracking-wider text-neutral-600 hover:bg-neutral-50">Cancel</a>
            </div>
        </form>
    </div>
    <div class="lg:sticky lg:top-6">
        <x-admin.layer-canvas kind="bottle" :image="old('image', $bottle?->image)" :refs="$canvasRefs" />
    </div>
    </div>
</x-admin-layout>
