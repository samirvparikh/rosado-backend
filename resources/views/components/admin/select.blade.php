@props(['name', 'label', 'options' => [], 'selected' => null, 'required' => false, 'blank' => null])
<div>
    <label for="{{ $name }}" class="block text-xs font-medium uppercase tracking-wider text-neutral-500">
        {{ $label }} @if($required)<span class="text-rose-500">*</span>@endif
    </label>
    <select
        id="{{ $name }}"
        name="{{ $name }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'mt-1.5 block w-full rounded-md border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 focus:border-neutral-500 focus:outline-none focus:ring-1 focus:ring-neutral-500']) }}
    >
        @if($blank !== null)
            <option value="">{{ $blank }}</option>
        @endif
        @foreach ($options as $optValue => $optLabel)
            <option value="{{ $optValue }}" @selected(old($name, $selected) == $optValue)>{{ $optLabel }}</option>
        @endforeach
    </select>
    @error($name)
        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
    @enderror
</div>
