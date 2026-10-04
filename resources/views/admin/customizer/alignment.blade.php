@php
    $asset = fn (?string $path) => \App\Support\CustomizerLayers::assetUrl($path);
    $style = fn (?array $box) => $box
        ? "top:{$box['top']}%;left:{$box['left']}%;width:{$box['width']}%;z-index:{$box['zIndex']};"
        : '';
@endphp
<x-admin-layout title="Customizer Alignment">
    <p class="mb-6 max-w-3xl text-sm text-neutral-500">
        Line up each bottle, its cap, label and liquid on the 3:4 preview canvas exactly as customers will see it.
        Values are canvas percentages. Cap and liquid positions can be saved for <em>this bottle only</em>, so every
        bottle/cap pair can fit perfectly without editing images.
    </p>

    @if ($bottles->isEmpty())
        <div class="rounded-lg border border-neutral-200 bg-white p-8 text-center text-sm text-neutral-500">
            Add a bottle first under <a href="{{ route('admin.bottles.create') }}" class="underline">Catalog › Bottles</a>.
        </div>
    @else
        <form method="GET" action="{{ route('admin.customizer.alignment') }}" class="mb-6 grid max-w-4xl grid-cols-1 gap-4 rounded-lg border border-neutral-200 bg-white p-4 sm:grid-cols-3" data-picker>
            <x-admin.select name="bottle" label="Bottle" :options="$bottles->mapWithKeys(fn ($b) => [$b->id => $b->name.' · '.($b->size?->display_name ?? $b->size_id)])" :selected="$bottle?->id" />
            <x-admin.select name="cap" label="Cap" :options="$caps->pluck('name', 'id')" :selected="$cap?->id" blank="No cap" />
            <x-admin.select name="fragrance" label="Fragrance (liquid layer)" :options="$fragrances->mapWithKeys(fn ($f) => [$f->id => $f->name.($f->liquid_image ? '' : ' (no liquid image)')])" :selected="$fragrance?->id" blank="None" />
        </form>

        <div class="grid grid-cols-1 gap-8 xl:grid-cols-[minmax(320px,440px)_1fr]">
            <div class="xl:sticky xl:top-6 xl:self-start">
                <div id="align-canvas" class="relative w-full overflow-hidden rounded-xl border border-neutral-200 bg-gradient-to-b from-stone-100 to-white"
                     style="aspect-ratio: 3 / 4; container-type: inline-size;">
                    <div data-guides class="pointer-events-none absolute inset-0 z-[90]" style="background-image: linear-gradient(to right, rgba(0,0,0,.06) 1px, transparent 1px), linear-gradient(to bottom, rgba(0,0,0,.06) 1px, transparent 1px); background-size: 10% 7.5%;">
                        <div class="absolute inset-y-0 left-1/2 w-px bg-rose-400/50"></div>
                    </div>
                    @if ($layers['bottle'])
                        <img id="layer-bottle" src="{{ $asset($layers['bottle']['image']) }}" alt="Bottle" class="absolute h-auto" style="{{ $style($layers['bottle']) }}">
                    @endif
                    @if ($layers['fragrance'])
                        <img id="layer-fragrance" src="{{ $asset($layers['fragrance']['image']) }}" alt="Liquid" class="absolute h-auto" style="{{ $style($layers['fragrance']) }}">
                    @endif
                    @if ($layers['cap'])
                        <img id="layer-cap" src="{{ $asset($layers['cap']['image']) }}" alt="Cap" class="absolute h-auto" style="{{ $style($layers['cap']) }}">
                    @endif
                    @if ($layers['label'])
                        <div id="layer-label" class="absolute text-center"
                             style="{{ $style($layers['label']) }} transform:translate(-50%,-50%); background:rgba(247,243,238,.95); border:1px solid rgba(184,149,106,.5); border-radius:6px; padding:3cqw 2cqw;">
                            <p style="font-size:2cqw; letter-spacing:.28em; color:#B8956A; line-height:1;">ROSADO</p>
                            <p style="font-family:Georgia,serif; font-size:4.4cqw; line-height:1.15; color:#1A1614; margin-top:.8cqw;">{{ $fragrance?->name ?? 'Fragrance' }}</p>
                            <p style="font-family:Georgia,serif; font-style:italic; font-size:2.9cqw; color:#2C2622;">Your text here</p>
                            <p style="font-size:2.1cqw; letter-spacing:.16em; color:#8A8178; margin-top:1cqw; line-height:1;">{{ $bottle->size?->display_name }}</p>
                        </div>
                    @endif
                </div>
                <label class="mt-3 flex items-center gap-2 text-xs text-neutral-500">
                    <input type="checkbox" checked data-toggle-guides class="rounded border-neutral-300"> Show grid &amp; centre line
                </label>
                @if (! $layers['bottle'])
                    <p class="mt-2 text-xs text-rose-600">This bottle has no image yet — upload one on the bottle's edit page.</p>
                @endif
            </div>

            <form method="POST" action="{{ route('admin.customizer.alignment.update') }}" class="max-w-2xl space-y-5">
                @csrf
                <input type="hidden" name="bottle_id" value="{{ $bottle->id }}">
                @if ($cap)<input type="hidden" name="cap_id" value="{{ $cap->id }}">@endif
                @if ($fragrance)<input type="hidden" name="fragrance_id" value="{{ $fragrance->id }}">@endif

                <x-admin.align-group layer="bottle" :title="'Bottle — '.$bottle->name" :box="$layers['bottle'] ?? \App\Support\CustomizerLayers::box($bottle)">
                    <p class="mb-3 text-xs text-neutral-400">Saved on the bottle.</p>
                </x-admin.align-group>

                <x-admin.align-group layer="label" title="Label (centre point)" :box="$layers['label']" :with-z="false">
                    <p class="mb-3 text-xs text-neutral-400">Top/Left are the label's centre. Saved on the bottle.</p>
                </x-admin.align-group>

                @if ($cap)
                    <x-admin.align-group layer="cap" :title="'Cap — '.$cap->name" :box="$layers['cap'] ?? \App\Support\CustomizerLayers::box($cap)">
                        <p class="mb-3 text-xs">
                            @if ($capHasOverride)
                                <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-emerald-700">Using a fit saved for this bottle</span>
                            @else
                                <span class="rounded-full bg-neutral-100 px-2 py-0.5 text-neutral-600">Using the cap's default position</span>
                            @endif
                        </p>
                        <div class="mb-3 flex flex-wrap gap-4 text-xs text-neutral-700">
                            <label class="flex items-center gap-1.5"><input type="radio" name="cap_scope" value="bottle" checked> Save for this bottle only</label>
                            <label class="flex items-center gap-1.5"><input type="radio" name="cap_scope" value="default"> Save as cap default (all bottles)</label>
                            @if ($capHasOverride)
                                <label class="flex items-center gap-1.5 text-rose-600"><input type="radio" name="cap_scope" value="reset"> Remove this bottle's fit</label>
                            @endif
                        </div>
                    </x-admin.align-group>
                @endif

                @if ($fragrance && $layers['fragrance'])
                    <x-admin.align-group layer="fragrance" :title="'Liquid — '.$fragrance->name" :box="$layers['fragrance']">
                        <p class="mb-3 text-xs">
                            @if ($fragranceHasOverride)
                                <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-emerald-700">Using a fit saved for this bottle</span>
                            @else
                                <span class="rounded-full bg-neutral-100 px-2 py-0.5 text-neutral-600">Using the liquid's default position</span>
                            @endif
                        </p>
                        <div class="mb-3 flex flex-wrap gap-4 text-xs text-neutral-700">
                            <label class="flex items-center gap-1.5"><input type="radio" name="fragrance_scope" value="bottle" checked> Save for this bottle only</label>
                            <label class="flex items-center gap-1.5"><input type="radio" name="fragrance_scope" value="default"> Save as default (all bottles)</label>
                            @if ($fragranceHasOverride)
                                <label class="flex items-center gap-1.5 text-rose-600"><input type="radio" name="fragrance_scope" value="reset"> Remove this bottle's fit</label>
                            @endif
                        </div>
                    </x-admin.align-group>
                @elseif ($fragrance)
                    <p class="rounded-md border border-dashed border-neutral-300 p-4 text-xs text-neutral-500">
                        {{ $fragrance->name }} has no liquid layer image, so the bottle artwork's own liquid is shown.
                        <a href="{{ route('admin.fragrances.edit', $fragrance) }}" class="underline">Add one</a> to align it here.
                    </p>
                @endif

                @if ($errors->any())
                    <p class="text-xs text-rose-600">{{ $errors->first() }}</p>
                @endif

                <div class="flex gap-3">
                    <button type="submit" class="rounded-md bg-neutral-900 px-5 py-2.5 text-xs font-medium uppercase tracking-wider text-white hover:bg-neutral-700">Save alignment</button>
                    <a href="{{ request()->fullUrl() }}" class="rounded-md border border-neutral-300 px-5 py-2.5 text-xs font-medium uppercase tracking-wider text-neutral-600 hover:bg-neutral-50">Discard changes</a>
                </div>
            </form>
        </div>

        <script>
            (function () {
                // Picker: reload with the chosen combination.
                document.querySelector('[data-picker]').addEventListener('change', (e) => e.currentTarget.submit());

                const guides = document.querySelector('[data-guides]');
                document.querySelector('[data-toggle-guides]').addEventListener('change', (e) => {
                    guides.style.display = e.target.checked ? '' : 'none';
                });

                const field = (layer, prop) => document.querySelector(`input[type=number][data-layer="${layer}"][data-prop="${prop}"]`);
                const slider = (layer, prop) => document.querySelector(`input[type=range][data-layer="${layer}"][data-prop="${prop}"]`);
                const num = (layer, prop) => parseFloat(field(layer, prop)?.value || '0');

                function apply(layer) {
                    const el = document.getElementById(`layer-${layer}`);
                    if (!el) return;
                    el.style.top = num(layer, 'top') + '%';
                    el.style.left = num(layer, 'left') + '%';
                    el.style.width = num(layer, 'width') + '%';
                    if (field(layer, 'z')) el.style.zIndex = String(Math.round(num(layer, 'z')));
                }

                function set(layer, prop, value) {
                    const rounded = Math.round(value * 100) / 100;
                    if (field(layer, prop)) field(layer, prop).value = rounded;
                    if (slider(layer, prop)) slider(layer, prop).value = rounded;
                }

                // Slider <-> number pairs.
                document.querySelectorAll('[data-layer][data-prop]').forEach((input) => {
                    input.addEventListener('input', () => {
                        const { layer, prop } = input.dataset;
                        set(layer, prop, parseFloat(input.value || '0'));
                        resetScale(layer);
                        apply(layer);
                    });
                });

                // Scale: resize around the layer's centre (label is centre-anchored already).
                const scaleBase = {};
                function resetScale(layer) {
                    delete scaleBase[layer];
                    const s = document.querySelector(`[data-scale="${layer}"]`);
                    if (s) s.value = 100;
                    const r = document.querySelector(`[data-scale-readout="${layer}"]`);
                    if (r) r.textContent = '100%';
                }
                document.querySelectorAll('[data-scale]').forEach((input) => {
                    input.addEventListener('input', () => {
                        const layer = input.dataset.scale;
                        const el = document.getElementById(`layer-${layer}`);
                        if (!scaleBase[layer]) {
                            scaleBase[layer] = { top: num(layer, 'top'), left: num(layer, 'left'), width: num(layer, 'width') };
                        }
                        const base = scaleBase[layer];
                        const factor = parseFloat(input.value) / 100;
                        const width = base.width * factor;
                        set(layer, 'width', width);
                        if (layer !== 'label' && el) {
                            // Height as % of canvas height: width% * (3/4) * (natural h / w).
                            const ratio = el.naturalWidth ? el.naturalHeight / el.naturalWidth : 1;
                            const baseHeight = base.width * 0.75 * ratio;
                            const height = width * 0.75 * ratio;
                            set(layer, 'left', base.left + (base.width - width) / 2);
                            set(layer, 'top', base.top + (baseHeight - height) / 2);
                        }
                        document.querySelector(`[data-scale-readout="${layer}"]`).textContent = input.value + '%';
                        apply(layer);
                    });
                });
            })();
        </script>
    @endif
</x-admin-layout>
