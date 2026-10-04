@props(['model' => null, 'title' => 'Preview position', 'hint' => null, 'defaults' => ['top' => 30, 'left' => 27, 'width' => 46, 'z' => 10]])
{{-- Canvas-% position of a customizer layer. Fine-tune visually in Catalog > Alignment Tool. --}}
<fieldset class="rounded-md border border-neutral-200 p-4">
    <legend class="px-1 text-xs font-medium uppercase tracking-wider text-neutral-500">{{ $title }}</legend>
    @if ($hint)<p class="mb-3 text-xs text-neutral-400">{{ $hint }}</p>@endif
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
        <x-admin.field name="layer_top" label="Top (%)" type="number" step="0.01" :value="$model?->layer_top ?? $defaults['top']" required />
        <x-admin.field name="layer_left" label="Left (%)" type="number" step="0.01" :value="$model?->layer_left ?? $defaults['left']" required />
        <x-admin.field name="layer_width" label="Width (%)" type="number" step="0.01" :value="$model?->layer_width ?? $defaults['width']" required />
        <x-admin.field name="layer_z" label="Z-index" type="number" :value="$model?->layer_z ?? $defaults['z']" required />
    </div>
</fieldset>
