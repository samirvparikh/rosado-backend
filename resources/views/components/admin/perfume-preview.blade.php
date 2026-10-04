@props(['preview' => null])
{{-- Renders a CustomizerLayers::compose() snapshot: the exact layer stack the customer saw. --}}
@if (is_array($preview) && ! empty($preview['layers']))
    <div {{ $attributes->merge(['class' => 'relative w-full overflow-hidden rounded-lg bg-gradient-to-b from-stone-100 to-white']) }}
         style="aspect-ratio: {{ $preview['aspect'] ?? '3 / 4' }}; container-type: inline-size;">
        @foreach ($preview['layers'] as $layer)
            <img src="{{ \App\Support\CustomizerLayers::assetUrl($layer['image']) }}" alt="{{ strtolower($layer['type']) }}"
                 style="position:absolute; top:{{ $layer['top'] }}%; left:{{ $layer['left'] }}%; width:{{ $layer['width'] }}%; z-index:{{ $layer['zIndex'] }}; height:auto;">
        @endforeach
        @if (! empty($preview['label']))
            @php $label = $preview['label']; @endphp
            <div style="position:absolute; top:{{ $label['top'] }}%; left:{{ $label['left'] }}%; width:{{ $label['width'] }}%; z-index:{{ $label['zIndex'] ?? 25 }}; transform:translate(-50%,-50%); text-align:center; background:rgba(247,243,238,.95); border:1px solid rgba(184,149,106,.5); border-radius:6px; padding:3cqw 2cqw;">
                <p style="font-size:2cqw; letter-spacing:.28em; color:#B8956A; line-height:1;">ROSADO</p>
                <p style="font-family:Georgia,serif; font-size:4.4cqw; line-height:1.15; color:#1A1614; margin-top:.8cqw;">{{ $label['title'] ?? '' }}</p>
                @foreach (($label['lines'] ?? []) as $line)
                    <p style="font-family:Georgia,serif; font-style:italic; font-size:2.9cqw; line-height:1.2; color:#2C2622;">{{ $line }}</p>
                @endforeach
                <p style="font-size:2.1cqw; letter-spacing:.16em; color:#8A8178; margin-top:1cqw; line-height:1;">{{ $label['size'] ?? '' }}</p>
            </div>
        @endif
    </div>
@endif
