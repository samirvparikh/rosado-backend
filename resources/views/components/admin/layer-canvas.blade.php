@props([
    // 'bottle' = the form edits a bottle (+ its label); refs are caps shown on top.
    // 'cap'    = the form edits a cap; refs are bottles it is previewed on.
    'kind',
    'image' => null,
    'refs' => [],
])
@php
    $asset = fn (?string $path) => \App\Support\CustomizerLayers::assetUrl($path);
    $refLabel = $kind === 'bottle' ? 'Preview with cap' : 'Preview on bottle';
@endphp
{{--
    Live 3:4 customizer canvas for the bottle / cap forms. Reads the form's
    own inputs (image_file, image, layer_*, label_*) so a freshly chosen file
    or a position change shows immediately, before saving.
--}}
<div class="rounded-lg border border-neutral-200 bg-white p-4" data-layer-canvas data-kind="{{ $kind }}"
     data-frontend-url="{{ rtrim((string) config('app.frontend_url'), '/') }}">
    <p class="text-xs font-semibold uppercase tracking-wider text-neutral-600">Canvas preview</p>
    <p class="mt-1 text-xs text-neutral-400">Updates live as you upload or change the position. Nothing is saved until you press Save.</p>

    <div class="relative mt-3 w-full overflow-hidden rounded-lg border border-neutral-100 bg-gradient-to-b from-stone-100 to-white"
         style="aspect-ratio: 3 / 4; container-type: inline-size;">
        <div data-guides class="pointer-events-none absolute inset-0" style="z-index: 90; background-image: linear-gradient(to right, rgba(0,0,0,.05) 1px, transparent 1px), linear-gradient(to bottom, rgba(0,0,0,.05) 1px, transparent 1px); background-size: 10% 7.5%;">
            <div class="absolute inset-y-0 left-1/2 w-px bg-rose-400/40"></div>
        </div>

        <img data-subject alt="" class="absolute h-auto" style="display:none; max-width:none;" @if ($image) src="{{ $asset($image) }}" @endif>
        <img data-ref alt="" class="absolute h-auto" style="display:none; max-width:none;">

        <div data-label class="absolute text-center" style="display:none; transform:translate(-50%,-50%); background:rgba(247,243,238,.95); border:1px solid rgba(184,149,106,.5); border-radius:6px; padding:3cqw 2cqw;">
            <p style="font-size:2cqw; letter-spacing:.28em; color:#B8956A; line-height:1;">ROSADO</p>
            <p style="font-family:Georgia,serif; font-size:4.4cqw; line-height:1.15; color:#1A1614; margin-top:.8cqw;">Fragrance</p>
            <p style="font-family:Georgia,serif; font-style:italic; font-size:2.9cqw; color:#2C2622;">Your text here</p>
            <p style="font-size:2.1cqw; letter-spacing:.16em; color:#8A8178; margin-top:1cqw; line-height:1;">50 ML</p>
        </div>

        <p data-empty class="absolute inset-0 flex items-center justify-center px-6 text-center text-xs text-neutral-400">
            Upload an image or enter an image URL to preview it here.
        </p>
    </div>

    @if (count($refs))
        <label class="mt-3 block text-xs font-medium uppercase tracking-wider text-neutral-500" for="layer-canvas-ref">{{ $refLabel }}</label>
        <select id="layer-canvas-ref" data-ref-select class="mt-1.5 block w-full rounded-md border border-neutral-300 bg-white px-3 py-2 text-sm">
            <option value="">None</option>
            @foreach ($refs as $index => $ref)
                <option value="{{ $index }}" @selected($index === 0)>{{ $ref['name'] }}</option>
            @endforeach
        </select>
        <p data-ref-note class="mt-1.5 text-xs text-amber-700" style="display:none;"></p>
    @endif

    <label class="mt-3 flex items-center gap-2 text-xs text-neutral-500">
        <input type="checkbox" checked data-toggle-guides class="rounded border-neutral-300"> Grid &amp; centre line
    </label>

    <script type="application/json" data-refs>@json(array_map(fn ($ref) => [...$ref, 'image' => $asset($ref['image'] ?? null)], $refs))</script>
</div>

@once
    <script>
        document.querySelectorAll('[data-layer-canvas]').forEach((root) => {
            const kind = root.dataset.kind;
            const frontendUrl = root.dataset.frontendUrl;
            const refs = JSON.parse(root.querySelector('[data-refs]').textContent || '[]');
            const subject = root.querySelector('[data-subject]');
            const ref = root.querySelector('[data-ref]');
            const label = root.querySelector('[data-label]');
            const empty = root.querySelector('[data-empty]');
            const refSelect = root.querySelector('[data-ref-select]');
            const refNote = root.querySelector('[data-ref-note]');
            const input = (name) => document.querySelector(`[name="${name}"]`);
            const num = (name, fallback = 0) => {
                const value = parseFloat(input(name)?.value);
                return Number.isFinite(value) ? value : fallback;
            };

            // Storefront-relative paths (/images/...) are served by the storefront host.
            const assetUrl = (path) => {
                if (!path) return '';
                if (/^(https?:)?\/\//i.test(path) || /^(data|blob):/i.test(path)) return path;
                return frontendUrl + '/' + path.replace(/^\/+/, '');
            };

            const place = (el, box) => {
                el.style.top = box.top + '%';
                el.style.left = box.left + '%';
                el.style.width = box.width + '%';
                if (box.zIndex !== undefined) el.style.zIndex = box.zIndex;
            };

            let uploadedUrl = null;

            function render() {
                // Subject: newly chosen file wins over the URL field.
                const url = uploadedUrl || assetUrl(input('image')?.value.trim());
                if (url && subject.getAttribute('src') !== url) subject.src = url;
                subject.style.display = url ? '' : 'none';
                empty.style.display = url ? 'none' : '';
                place(subject, {
                    top: num('layer_top'), left: num('layer_left'), width: num('layer_width', 30),
                    zIndex: Math.round(num('layer_z', kind === 'cap' ? 30 : 10)),
                });

                const chosen = refSelect && refSelect.value !== '' ? refs[Number(refSelect.value)] : null;
                ref.style.display = chosen?.image ? '' : 'none';
                if (chosen?.image) {
                    if (ref.getAttribute('src') !== chosen.image) ref.src = chosen.image;
                    place(ref, chosen.box);
                }

                // Label: the bottle form edits it; the cap form shows the reference bottle's.
                const labelBox = kind === 'bottle'
                    ? { top: num('label_top', 66), left: num('label_left', 50), width: num('label_width', 30), zIndex: 25 }
                    : chosen?.label;
                label.style.display = labelBox && (kind === 'bottle' ? url : chosen) ? '' : 'none';
                if (labelBox) place(label, labelBox);

                if (refNote) {
                    refNote.textContent = chosen?.note || '';
                    refNote.style.display = chosen?.note ? '' : 'none';
                }
            }

            input('image_file')?.addEventListener('change', (event) => {
                const file = event.target.files?.[0];
                if (uploadedUrl) URL.revokeObjectURL(uploadedUrl);
                uploadedUrl = file ? URL.createObjectURL(file) : null;
                render();
            });
            ['image', 'layer_top', 'layer_left', 'layer_width', 'layer_z', 'label_top', 'label_left', 'label_width']
                .forEach((name) => input(name)?.addEventListener('input', render));
            refSelect?.addEventListener('change', render);
            root.querySelector('[data-toggle-guides]').addEventListener('change', (event) => {
                root.querySelector('[data-guides]').style.display = event.target.checked ? '' : 'none';
            });

            render();
        });
    </script>
@endonce
