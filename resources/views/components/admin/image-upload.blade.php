@props(['name', 'label' => 'Upload Image', 'current' => null, 'hint' => null, 'accept' => 'image/png,image/webp', 'spec' => null])
@php
    $specData = $spec ? \App\Support\ImageUpload::SPECS[$spec] : null;
    $formats = collect(explode(',', $accept))->map(fn ($type) => strtoupper(str_replace(['image/', 'jpeg', 'svg+xml'], ['', 'jpg', 'svg'], trim($type))))->unique()->implode(' / ');
@endphp
<div data-image-upload
     @if ($specData)
         data-min-width="{{ $specData['minWidth'] }}" data-max-width="{{ $specData['maxWidth'] }}"
         data-max-kb="{{ $specData['maxKb'] }}" data-transparent="{{ $specData['transparent'] ? '1' : '0' }}"
     @endif>
    <label for="{{ $name }}" class="block text-xs font-medium uppercase tracking-wider text-neutral-500">{{ $label }}</label>
    <div class="mt-1.5 flex items-center gap-3">
        @if ($current)
            <img src="{{ \App\Support\CustomizerLayers::assetUrl($current) }}" alt="" data-current-image class="h-14 w-14 rounded bg-neutral-100 object-contain">
        @endif
        <input id="{{ $name }}" type="file" name="{{ $name }}" accept="{{ $accept }}" class="block w-full text-sm text-neutral-600 file:mr-3 file:rounded-md file:border-0 file:bg-neutral-100 file:px-3 file:py-2 file:text-xs file:font-medium file:text-neutral-700 hover:file:bg-neutral-200">
    </div>

    {{-- Live check of the chosen (or current) file against the recommended spec. --}}
    <div data-image-info class="mt-2 hidden rounded-md border px-3 py-2 text-xs"></div>

    @if ($specData)
        <div class="mt-2 rounded-md border border-neutral-200 bg-neutral-50 px-3 py-2.5 text-xs text-neutral-600">
            <p class="font-medium text-neutral-800">Recommended {{ strtolower($specData['title']) }} size</p>
            <ul class="mt-1 list-disc space-y-0.5 pl-4">
                <li>Width: <strong>{{ $specData['minWidth'] }}–{{ $specData['maxWidth'] }} px</strong></li>
                @foreach ($specData['notes'] as $note)
                    <li>{!! $note !!}</li>
                @endforeach
                <li>Format: <strong>{{ $formats }}</strong>@if ($specData['transparent']) with a <strong>transparent background</strong>@endif</li>
                <li>File size: ideally under <strong>{{ $specData['maxKb'] }} KB</strong> (upload limit 5 MB)</li>
            </ul>
        </div>
    @endif
    @if ($hint)<p class="mt-1 text-xs text-neutral-400">{!! $hint !!}</p>@endif
    @error($name)<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
</div>

@once
    <script>
        document.querySelectorAll('[data-image-upload]').forEach((root) => {
            const input = root.querySelector('input[type=file]');
            const info = root.querySelector('[data-image-info]');
            const current = root.querySelector('[data-current-image]');
            const spec = root.dataset.minWidth ? {
                minWidth: Number(root.dataset.minWidth),
                maxWidth: Number(root.dataset.maxWidth),
                maxKb: Number(root.dataset.maxKb),
                transparent: root.dataset.transparent === '1',
            } : null;

            // Fully transparent corners = transparent background (sampled on a small canvas).
            function hasTransparentCorners(img) {
                try {
                    const canvas = document.createElement('canvas');
                    canvas.width = img.naturalWidth;
                    canvas.height = img.naturalHeight;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0);
                    const w = canvas.width - 1, h = canvas.height - 1;
                    return [[0, 0], [w, 0], [0, h], [w, h]]
                        .every(([x, y]) => ctx.getImageData(x, y, 1, 1).data[3] < 10);
                } catch (e) {
                    return null; // cross-origin image: can't inspect pixels
                }
            }

            function report(img, bytes, label) {
                const width = img.naturalWidth, height = img.naturalHeight;
                const warnings = [];
                if (spec) {
                    if (width < spec.minWidth) warnings.push(`narrower than the recommended ${spec.minWidth} px — may look soft on high-resolution screens`);
                    if (width > spec.maxWidth * 2) warnings.push(`much wider than needed (${spec.maxWidth} px is enough) — resize to keep the page fast`);
                    if (bytes !== null && bytes > spec.maxKb * 1024) warnings.push(`heavier than ${spec.maxKb} KB — compress it for faster loading`);
                    if (spec.transparent && hasTransparentCorners(img) === false) warnings.push('background does not look transparent — the box will show behind the bottle/cap');
                }
                const size = bytes === null ? '' : ` · ${bytes >= 1048576 ? (bytes / 1048576).toFixed(1) + ' MB' : Math.round(bytes / 1024) + ' KB'}`;
                info.classList.remove('hidden', 'border-emerald-200', 'bg-emerald-50', 'text-emerald-800', 'border-amber-200', 'bg-amber-50', 'text-amber-800');
                info.classList.add(...(warnings.length
                    ? ['border-amber-200', 'bg-amber-50', 'text-amber-800']
                    : ['border-emerald-200', 'bg-emerald-50', 'text-emerald-800']));
                info.innerHTML = `<strong>${label}: ${width} × ${height} px${size}</strong>` +
                    (warnings.length
                        ? '<ul class="mt-1 list-disc pl-4">' + warnings.map((w) => `<li>${w}</li>`).join('') + '</ul>'
                        : (spec ? ' — looks good.' : ''));
            }

            input.addEventListener('change', () => {
                const file = input.files?.[0];
                if (!file) {
                    info.classList.add('hidden');
                    return;
                }
                const img = new Image();
                const url = URL.createObjectURL(file);
                img.onload = () => {
                    report(img, file.size, 'Selected file');
                    URL.revokeObjectURL(url);
                };
                img.onerror = () => {
                    info.classList.remove('hidden');
                    info.textContent = 'This file could not be read as an image.';
                };
                img.src = url;
            });

            // Show the saved image's dimensions too (file size unknown for remote images).
            if (current) {
                const show = () => current.naturalWidth && report(current, null, 'Current image');
                current.complete ? show() : current.addEventListener('load', show);
            }
        });
    </script>
@endonce
