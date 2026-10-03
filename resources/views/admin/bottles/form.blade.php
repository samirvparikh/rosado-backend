<x-admin-layout :title="$bottle ? 'Edit Bottle' : 'New Bottle'">
    <div class="max-w-xl rounded-lg border border-neutral-200 bg-white p-6">
        <form method="POST" action="{{ $bottle ? route('admin.bottles.update', $bottle) : route('admin.bottles.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @if ($bottle) @method('PUT') @endif

            @if (!$bottle)
                <x-admin.field name="id" label="ID (e.g. BTL007)" required />
            @endif
            <x-admin.field name="name" label="Name" :value="$bottle?->name" required />
            <x-admin.field name="code" label="Code" :value="$bottle?->code" required />
            <div>
                <label for="image_file" class="block text-xs font-medium uppercase tracking-wider text-neutral-500">Upload Image</label>
                <div class="mt-1.5 flex items-center gap-3">
                    @if ($bottle?->image)
                        <img src="{{ $bottle->image }}" alt="" class="h-14 w-14 rounded bg-neutral-100 object-contain">
                    @endif
                    <input id="image_file" type="file" name="image_file" accept="image/*" class="block w-full text-sm text-neutral-600 file:mr-3 file:rounded-md file:border-0 file:bg-neutral-100 file:px-3 file:py-2 file:text-xs file:font-medium file:text-neutral-700 hover:file:bg-neutral-200">
                </div>
                <p class="mt-1 text-xs text-neutral-400">Transparent PNG/SVG of the bottle <strong>without its cap</strong>, cropped tight to the glass — the builder preview stacks the chosen cap on top and prints the label on it.</p>
                @error('image_file')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <x-admin.field name="image" label="or Image URL" :value="$bottle?->image" />
            <x-admin.select name="size_id" label="Size" :options="$sizes" :selected="$bottle?->size_id" blank="Select a size" required />
            <x-admin.field name="additional_price" label="Additional Price (₹)" type="number" step="0.01" :value="$bottle?->additional_price ?? 0" required />
            <x-admin.field name="stock" label="Stock" type="number" :value="$bottle?->stock ?? 0" required />
            <x-admin.field name="sort_order" label="Sort Order" type="number" :value="$bottle?->sort_order ?? 0" required />
            <x-admin.select name="status" label="Status" :options="['ACTIVE' => 'Active', 'INACTIVE' => 'Inactive']" :selected="$bottle?->status ?? 'ACTIVE'" required />

            <div class="flex gap-3 pt-2">
                <button type="submit" class="rounded-md bg-neutral-900 px-5 py-2.5 text-xs font-medium uppercase tracking-wider text-white hover:bg-neutral-700">Save</button>
                <a href="{{ route('admin.bottles.index') }}" class="rounded-md border border-neutral-300 px-5 py-2.5 text-xs font-medium uppercase tracking-wider text-neutral-600 hover:bg-neutral-50">Cancel</a>
            </div>
        </form>
    </div>
</x-admin-layout>
