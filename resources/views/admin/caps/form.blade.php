<x-admin-layout :title="$cap ? 'Edit Cap' : 'New Cap'">
    <div class="max-w-xl rounded-lg border border-neutral-200 bg-white p-6">
        <form method="POST" action="{{ $cap ? route('admin.caps.update', $cap) : route('admin.caps.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @if ($cap) @method('PUT') @endif

            @if (!$cap)
                <x-admin.field name="id" label="ID (e.g. CAP004)" required />
            @endif
            <x-admin.field name="name" label="Name" :value="$cap?->name" required />
            <x-admin.field name="code" label="Code" :value="$cap?->code" required />
            <div>
                <label for="image_file" class="block text-xs font-medium uppercase tracking-wider text-neutral-500">Upload Image</label>
                <div class="mt-1.5 flex items-center gap-3">
                    @if ($cap?->image)
                        <img src="{{ $cap->image }}" alt="" class="h-14 w-14 rounded bg-neutral-100 object-contain">
                    @endif
                    <input id="image_file" type="file" name="image_file" accept="image/*" class="block w-full text-sm text-neutral-600 file:mr-3 file:rounded-md file:border-0 file:bg-neutral-100 file:px-3 file:py-2 file:text-xs file:font-medium file:text-neutral-700 hover:file:bg-neutral-200">
                </div>
                <p class="mt-1 text-xs text-neutral-400">Transparent PNG/SVG of the cap only, cropped tight — it is placed on top of the selected bottle in the builder preview.</p>
                @error('image_file')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <x-admin.field name="image" label="or Image URL" :value="$cap?->image" />
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
