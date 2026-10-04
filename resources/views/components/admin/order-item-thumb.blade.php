@props(['item', 'large' => false])
{{--
    Product image for an order line: custom perfumes show the assembled bottle
    from the order snapshot; ready-made lines show the product photo saved
    with the order (falls back to a neutral tile).
--}}
@php
    $box = $large ? 'h-20 w-20' : 'h-12 w-12';
    $previewWidth = $large ? 'w-[60px]' : 'w-9'; // 3:4 canvas fills the square's height
@endphp
<div {{ $attributes->merge(['class' => "$box flex shrink-0 items-center justify-center overflow-hidden rounded-md border border-neutral-200 bg-stone-50"]) }}
     title="{{ $item->product_name }}{{ $item->size_name ? ' · '.$item->size_name : '' }}">
    @if ($item->product_type === 'CUSTOM_PERFUME' && $item->preview)
        <x-admin.perfume-preview :preview="$item->preview" :class="$previewWidth" />
    @elseif ($item->image)
        <img src="{{ \App\Support\CustomizerLayers::assetUrl($item->image) }}" alt="{{ $item->product_name }}" loading="lazy" class="h-full w-full object-cover">
    @else
        <span class="text-[9px] text-neutral-400">No image</span>
    @endif
</div>
