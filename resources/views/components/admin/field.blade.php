@props(['name', 'label', 'type' => 'text', 'value' => null, 'required' => false, 'step' => null])
<div>
    <label for="{{ $name }}" class="block text-xs font-medium uppercase tracking-wider text-neutral-500">
        {{ $label }} @if($required)<span class="text-rose-500">*</span>@endif
    </label>
    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        @if($step) step="{{ $step }}" @endif
        value="{{ old(str_replace(['[', ']'], ['.', ''], $name), $value) }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'mt-1.5 block w-full rounded-md border border-neutral-300 px-3 py-2 text-sm text-neutral-900 focus:border-neutral-500 focus:outline-none focus:ring-1 focus:ring-neutral-500']) }}
    >
    @error(str_replace(['[', ']'], ['.', ''], $name))
        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
    @enderror
</div>
