@php
    $selectedFamilyIds = $fragrance?->classifications->where('group', 'FRAGRANCE_FAMILY')->pluck('id')->all() ?? [];
    $selectedCharacterIds = $fragrance?->classifications->where('group', 'SCENT_CHARACTER')->pluck('id')->all() ?? [];
    $selectedNoteIds = fn (string $type) => $fragrance?->noteLinks->where('note_type', $type)->pluck('note_id')->all() ?? [];
    $priceFor = fn (string $sizeId) => $fragrance?->sizePrices->firstWhere('size_id', $sizeId)?->base_price;
@endphp
<x-admin-layout :title="$fragrance ? 'Edit Fragrance' : 'New Fragrance'">
    <div class="max-w-3xl rounded-lg border border-neutral-200 bg-white p-6">
        <form method="POST" action="{{ $fragrance ? route('admin.fragrances.update', $fragrance) : route('admin.fragrances.store') }}" class="space-y-6">
            @csrf
            @if ($fragrance) @method('PUT') @endif

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                @if (!$fragrance)
                    <x-admin.field name="id" label="ID (e.g. FRG007)" required />
                @endif
                <x-admin.field name="name" label="Name" :value="$fragrance?->name" required />
                <x-admin.field name="slug" label="Slug" :value="$fragrance?->slug" required />
                <x-admin.select name="gender" label="Gender" :options="['MEN' => 'Men', 'WOMEN' => 'Women', 'UNISEX' => 'Unisex']" :selected="$fragrance?->gender ?? 'UNISEX'" required />
                <x-admin.field name="image" label="Image URL" :value="$fragrance?->image" />
                <x-admin.select name="status" label="Status" :options="['ACTIVE' => 'Active', 'INACTIVE' => 'Inactive']" :selected="$fragrance?->status ?? 'ACTIVE'" required />
            </div>

            <x-admin.field name="short_description" label="Short Description" :value="$fragrance?->short_description" required />
            <x-admin.textarea name="description" label="Description" :value="$fragrance?->description" required />

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-neutral-500">Fragrance Families</p>
                    <div class="mt-2 space-y-1.5 rounded-md border border-neutral-200 p-3">
                        @foreach ($families as $family)
                            <label class="flex items-center gap-2 text-sm text-neutral-700">
                                <input type="checkbox" name="families[]" value="{{ $family->id }}" @checked(in_array($family->id, old('families', $selectedFamilyIds))) class="rounded border-neutral-300">
                                {{ $family->name }}
                            </label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-neutral-500">Scent Character</p>
                    <div class="mt-2 space-y-1.5 rounded-md border border-neutral-200 p-3">
                        @foreach ($characters as $character)
                            <label class="flex items-center gap-2 text-sm text-neutral-700">
                                <input type="checkbox" name="characters[]" value="{{ $character->id }}" @checked(in_array($character->id, old('characters', $selectedCharacterIds))) class="rounded border-neutral-300">
                                {{ $character->name }}
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div>
                <p class="text-xs font-medium uppercase tracking-wider text-neutral-500">Fragrance Notes</p>
                <p class="text-xs text-neutral-400">Hold Ctrl/Cmd to select multiple notes per layer.</p>
                <div class="mt-2 grid grid-cols-1 gap-4 sm:grid-cols-3">
                    @foreach (['TOP' => 'Top Notes', 'HEART' => 'Heart Notes', 'BASE' => 'Base Notes'] as $type => $label)
                        <div>
                            <label class="block text-xs text-neutral-500">{{ $label }}</label>
                            <select name="notes[{{ $type }}][]" multiple size="6" class="mt-1 block w-full rounded-md border border-neutral-300 px-2 py-1 text-sm">
                                @foreach ($notesByType[$type] as $note)
                                    <option value="{{ $note->id }}" @selected(in_array($note->id, old("notes.$type", $selectedNoteIds($type))))>{{ $note->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach
                </div>
            </div>

            <div>
                <p class="text-xs font-medium uppercase tracking-wider text-neutral-500">Custom Perfume Base Price by Size</p>
                <p class="text-xs text-neutral-400">Leave blank to make this fragrance unavailable at that size in the builder.</p>
                <div class="mt-2 grid grid-cols-1 gap-4 sm:grid-cols-3">
                    @foreach ($sizes as $size)
                        <x-admin.field
                            :name="'size_prices['.$size->id.']'"
                            :label="$size->display_name.' (₹)'"
                            type="number" step="0.01"
                            :value="$priceFor($size->id)"
                        />
                    @endforeach
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="rounded-md bg-neutral-900 px-5 py-2.5 text-xs font-medium uppercase tracking-wider text-white hover:bg-neutral-700">Save</button>
                <a href="{{ route('admin.fragrances.index') }}" class="rounded-md border border-neutral-300 px-5 py-2.5 text-xs font-medium uppercase tracking-wider text-neutral-600 hover:bg-neutral-50">Cancel</a>
            </div>
        </form>
    </div>
</x-admin-layout>
