@props(['name', 'label', 'value' => null, 'required' => false, 'rows' => 4])
<div>
    <label for="{{ $name }}" class="block text-xs font-medium uppercase tracking-wider text-neutral-500">
        {{ $label }} @if($required)<span class="text-rose-500">*</span>@endif
    </label>
    <textarea
        id="{{ $name }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'mt-1.5 block w-full rounded-md border border-neutral-300 px-3 py-2 text-sm text-neutral-900 focus:border-neutral-500 focus:outline-none focus:ring-1 focus:ring-neutral-500']) }}
    >{{ old($name, $value) }}</textarea>
    @error($name)
        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
    @enderror
</div>
