@props(['layer', 'title', 'box', 'withZ' => true])
{{-- Slider + number pairs for one layer of the alignment tool; JS live-updates the canvas. --}}
@php
    $rows = [
        'top' => ['Top', -20, 110, 0.1],
        'left' => ['Left', -20, 110, 0.1],
        'width' => ['Width', 1, 120, 0.1],
    ];
    if ($withZ) {
        $rows['z'] = ['Z-index', 0, 60, 1];
    }
    $value = fn ($prop) => $prop === 'z' ? ($box['zIndex'] ?? 0) : ($box[$prop] ?? 0);
@endphp
<fieldset class="rounded-md border border-neutral-200 p-4" data-align-group="{{ $layer }}">
    <legend class="px-1 text-xs font-semibold uppercase tracking-wider text-neutral-600">{{ $title }}</legend>
    {{ $slot }}
    <div class="space-y-2">
        @foreach ($rows as $prop => [$label, $min, $max, $step])
            <div class="grid grid-cols-[64px_1fr_76px] items-center gap-3">
                <label class="text-xs text-neutral-500" for="{{ $layer }}-{{ $prop }}">{{ $label }}</label>
                <input type="range" min="{{ $min }}" max="{{ $max }}" step="{{ $step }}" value="{{ $value($prop) }}" data-layer="{{ $layer }}" data-prop="{{ $prop }}" class="w-full accent-neutral-900">
                <input id="{{ $layer }}-{{ $prop }}" type="number" step="{{ $step }}" name="{{ $layer }}[{{ $prop }}]" value="{{ $value($prop) }}" data-layer="{{ $layer }}" data-prop="{{ $prop }}" class="w-full rounded-md border border-neutral-300 px-2 py-1 text-right text-xs">
            </div>
        @endforeach
        <div class="grid grid-cols-[64px_1fr_76px] items-center gap-3">
            <label class="text-xs text-neutral-500">Scale</label>
            <input type="range" min="50" max="150" step="1" value="100" data-scale="{{ $layer }}" class="w-full accent-amber-600" title="Resize around the layer's centre">
            <span class="text-right text-xs text-neutral-400" data-scale-readout="{{ $layer }}">100%</span>
        </div>
    </div>
</fieldset>
