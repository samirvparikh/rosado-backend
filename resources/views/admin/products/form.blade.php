@php
    $selectedClassificationIds = $product?->classifications->pluck('id')->all() ?? [];
    $sizeRow = fn (string $sizeId) => $product?->sizes->firstWhere('size_id', $sizeId);
    $imageAt = fn (int $i) => $product?->images->values()->get($i);
    $flags = ['is_new_arrival' => 'New Arrival', 'is_best_seller' => 'Best Seller', 'is_featured' => 'Featured', 'is_limited_edition' => 'Limited Edition', 'is_trending' => 'Trending', 'is_sale' => 'Sale'];
@endphp
<x-admin-layout :title="$product ? 'Edit Product' : 'New Product'">
    <div class="max-w-4xl rounded-lg border border-neutral-200 bg-white p-6">
        <form method="POST" action="{{ $product ? route('admin.products.update', $product) : route('admin.products.store') }}" class="space-y-8">
            @csrf
            @if ($product) @method('PUT') @endif

            <section class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                @if (!$product)
                    <x-admin.field name="id" label="ID (e.g. PRD-NEW-PERFUME)" required />
                @endif
                <x-admin.field name="sku" label="SKU" :value="$product?->sku" required />
                <x-admin.field name="name" label="Name" :value="$product?->name" required />
                <x-admin.field name="slug" label="Slug" :value="$product?->slug" required />
                <x-admin.field name="brand" label="Brand" :value="$product?->brand ?? 'ROSADO'" required />
                <x-admin.select name="product_type" label="Product Type" :options="['READY_MADE' => 'Ready Made', 'CUSTOM_PERFUME' => 'Custom Perfume', 'GIFT_SET' => 'Gift Set', 'BUNDLE' => 'Bundle', 'LIMITED_EDITION' => 'Limited Edition']" :selected="$product?->product_type ?? 'READY_MADE'" required />
                <x-admin.select name="status" label="Status" :options="['ACTIVE' => 'Active', 'INACTIVE' => 'Inactive']" :selected="$product?->status ?? 'ACTIVE'" required />
                <x-admin.select name="fragrance_id" label="Signature Fragrance (for PDP notes)" :options="$fragrances" :selected="$product?->fragrance_id" blank="None" />
            </section>

            <x-admin.field name="short_description" label="Short Description" :value="$product?->short_description" required />
            <x-admin.textarea name="description" label="Description" :value="$product?->description" required />

            <section class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                <x-admin.field name="tax_rate" label="Tax Rate (%)" type="number" step="0.01" :value="$product?->tax_rate ?? 18" required />
                <x-admin.select name="discount_type" label="Discount Type" :options="['NONE' => 'None', 'PERCENT' => 'Percent', 'FLAT' => 'Flat']" :selected="$product?->discount_type ?? 'NONE'" required />
                <x-admin.field name="discount_value" label="Discount Value" type="number" step="0.01" :value="$product?->discount_value ?? 0" required />
                <x-admin.field name="rating" label="Rating" type="number" step="0.1" :value="$product?->rating ?? 0" required />
                <x-admin.field name="review_count" label="Review Count" type="number" :value="$product?->review_count ?? 0" required />
            </section>

            <section>
                <p class="text-xs font-medium uppercase tracking-wider text-neutral-500">Badges</p>
                <div class="mt-2 flex flex-wrap gap-4">
                    @foreach ($flags as $flag => $label)
                        <label class="flex items-center gap-2 text-sm text-neutral-700">
                            <input type="checkbox" name="{{ $flag }}" value="1" @checked(old($flag, $product?->$flag ?? false)) class="rounded border-neutral-300">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </section>

            <section>
                <p class="text-xs font-medium uppercase tracking-wider text-neutral-500">Classifications</p>
                <div class="mt-2 grid grid-cols-1 gap-4 sm:grid-cols-3">
                    @foreach ($classificationGroups as $groupKey => $groupLabel)
                        <div class="rounded-md border border-neutral-200 p-3">
                            <p class="text-xs font-medium text-neutral-600">{{ $groupLabel }}</p>
                            <div class="mt-1.5 max-h-32 space-y-1 overflow-y-auto">
                                @foreach (($classificationsByGroup[$groupKey] ?? []) as $item)
                                    <label class="flex items-center gap-2 text-xs text-neutral-700">
                                        <input type="checkbox" name="classifications[]" value="{{ $item->id }}" @checked(in_array($item->id, old('classifications', $selectedClassificationIds))) class="rounded border-neutral-300">
                                        {{ $item->name }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <section>
                <p class="text-xs font-medium uppercase tracking-wider text-neutral-500">Sizes &amp; Pricing</p>
                <p class="text-xs text-neutral-400">Leave a size's SKU blank to remove it from this product.</p>
                <div class="mt-2 space-y-3">
                    @foreach ($sizes as $size)
                        @php $row = $sizeRow($size->id); @endphp
                        <div class="grid grid-cols-2 gap-3 rounded-md border border-neutral-200 p-3 sm:grid-cols-6 sm:items-end">
                            <div class="col-span-2 sm:col-span-1">
                                <p class="text-xs font-medium text-neutral-600">{{ $size->display_name }}</p>
                            </div>
                            <x-admin.field :name="'sizes['.$size->id.'][sku]'" label="SKU" :value="$row?->sku" />
                            <x-admin.field :name="'sizes['.$size->id.'][mrp]'" label="MRP" type="number" step="0.01" :value="$row?->mrp" />
                            <x-admin.field :name="'sizes['.$size->id.'][selling_price]'" label="Selling Price" type="number" step="0.01" :value="$row?->selling_price" />
                            <x-admin.field :name="'sizes['.$size->id.'][cost_price]'" label="Cost Price" type="number" step="0.01" :value="$row?->cost_price" />
                            <x-admin.field :name="'sizes['.$size->id.'][stock]'" label="Stock" type="number" :value="$row?->stock" />
                        </div>
                    @endforeach
                </div>
            </section>

            <section>
                <p class="text-xs font-medium uppercase tracking-wider text-neutral-500">Images</p>
                <div class="mt-2 space-y-3">
                    @for ($i = 0; $i < $imageSlots; $i++)
                        @php $image = $imageAt($i); @endphp
                        <div class="grid grid-cols-1 gap-3 rounded-md border border-neutral-200 p-3 sm:grid-cols-12 sm:items-end">
                            <div class="sm:col-span-5">
                                <x-admin.field :name="'images['.$i.'][image_url]'" label="Image URL" :value="$image?->image_url" />
                            </div>
                            <div class="sm:col-span-3">
                                <x-admin.select :name="'images['.$i.'][image_type]'" label="Type" :options="['MAIN' => 'Main', 'GALLERY' => 'Gallery', 'LIFESTYLE' => 'Lifestyle', 'PACKAGING' => 'Packaging', 'DETAIL' => 'Detail']" :selected="$image?->image_type ?? 'GALLERY'" />
                            </div>
                            <div class="sm:col-span-3">
                                <x-admin.field :name="'images['.$i.'][alt]'" label="Alt Text" :value="$image?->alt" />
                            </div>
                            <label class="flex items-center gap-2 pb-2 text-xs text-neutral-600 sm:col-span-1">
                                <input type="checkbox" name="images[{{ $i }}][is_primary]" value="1" @checked($image?->is_primary) class="rounded border-neutral-300">
                                Primary
                            </label>
                        </div>
                    @endfor
                </div>
            </section>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="rounded-md bg-neutral-900 px-5 py-2.5 text-xs font-medium uppercase tracking-wider text-white hover:bg-neutral-700">Save</button>
                <a href="{{ route('admin.products.index') }}" class="rounded-md border border-neutral-300 px-5 py-2.5 text-xs font-medium uppercase tracking-wider text-neutral-600 hover:bg-neutral-50">Cancel</a>
            </div>
        </form>
    </div>
</x-admin-layout>
