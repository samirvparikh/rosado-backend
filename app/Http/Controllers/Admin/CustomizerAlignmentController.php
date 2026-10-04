<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bottle;
use App\Models\Cap;
use App\Models\CustomizerLayerOverride;
use App\Models\Fragrance;
use App\Support\CustomizerLayers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Visual alignment of customizer layers. Pick a bottle + cap (+ fragrance
 * with a liquid layer), drag the sliders, save. The bottle and label are
 * saved on the bottle; the cap / liquid either as this bottle's override
 * for that exact pair, or as the component's default for every bottle.
 */
class CustomizerAlignmentController extends Controller
{
    public function index(Request $request): View
    {
        $bottles = Bottle::with(['size', 'layerOverrides'])->orderBy('size_id')->orderBy('sort_order')->get();
        $caps = Cap::orderBy('sort_order')->get();
        $fragrances = Fragrance::orderBy('name')->get();

        $bottle = $bottles->firstWhere('id', $request->query('bottle')) ?? $bottles->first();
        $cap = $caps->firstWhere('id', $request->query('cap')) ?? $caps->first();
        $fragrance = $fragrances->firstWhere('id', $request->query('fragrance'))
            ?? $fragrances->first(fn ($f) => $f->liquid_image)
            ?? $fragrances->first();

        $hasOverride = fn (string $type, ?string $id) => $bottle && $id
            && $bottle->layerOverrides->contains(fn ($o) => $o->layer_type === $type && $o->layer_id === $id);

        return view('admin.customizer.alignment', [
            'bottles' => $bottles,
            'caps' => $caps,
            'fragrances' => $fragrances,
            'bottle' => $bottle,
            'cap' => $cap,
            'fragrance' => $fragrance,
            'layers' => [
                'bottle' => $bottle ? CustomizerLayers::bottleLayer($bottle) : null,
                'cap' => $cap ? CustomizerLayers::capLayer($cap, $bottle) : null,
                'fragrance' => $fragrance ? CustomizerLayers::fragranceLayer($fragrance, $bottle) : null,
                'label' => $bottle ? CustomizerLayers::labelBox($bottle) : null,
            ],
            'capHasOverride' => $hasOverride('CAP', $cap?->id),
            'fragranceHasOverride' => $hasOverride('FRAGRANCE', $fragrance?->id),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $layer = fn (string $key) => [
            "$key.top" => ['required', 'numeric', 'between:-50,150'],
            "$key.left" => ['required', 'numeric', 'between:-50,150'],
            "$key.width" => ['required', 'numeric', 'between:1,200'],
            "$key.z" => ['required', 'integer', 'between:0,99'],
        ];

        $data = $request->validate([
            'bottle_id' => ['required', 'exists:bottles,id'],
            'cap_id' => ['nullable', 'exists:caps,id'],
            'fragrance_id' => ['nullable', 'exists:fragrances,id'],
            ...$layer('bottle'),
            'label.top' => ['required', 'numeric', 'between:0,100'],
            'label.left' => ['required', 'numeric', 'between:0,100'],
            'label.width' => ['required', 'numeric', 'between:5,100'],
            'cap' => ['nullable', 'array'],
            'cap_scope' => ['nullable', 'in:bottle,default,reset'],
            'fragrance' => ['nullable', 'array'],
            'fragrance_scope' => ['nullable', 'in:bottle,default,reset'],
        ]);

        // Cap / liquid boxes are only validated when that layer is on screen.
        if (! empty($data['cap_id']) && isset($data['cap'])) {
            $request->validate($layer('cap'));
        }
        if (! empty($data['fragrance_id']) && isset($data['fragrance'])) {
            $request->validate($layer('fragrance'));
        }

        $toColumns = fn (array $box) => [
            'layer_top' => $box['top'],
            'layer_left' => $box['left'],
            'layer_width' => $box['width'],
            'layer_z' => $box['z'],
        ];

        DB::transaction(function () use ($data, $toColumns) {
            Bottle::whereKey($data['bottle_id'])->update([
                ...$toColumns($data['bottle']),
                'label_top' => $data['label']['top'],
                'label_left' => $data['label']['left'],
                'label_width' => $data['label']['width'],
            ]);

            foreach ([['CAP', 'cap', Cap::class], ['FRAGRANCE', 'fragrance', Fragrance::class]] as [$type, $key, $model]) {
                $id = $data["{$key}_id"] ?? null;
                if (! $id || ! isset($data[$key])) {
                    continue;
                }
                $match = ['bottle_id' => $data['bottle_id'], 'layer_type' => $type, 'layer_id' => $id];

                match ($data["{$key}_scope"] ?? 'bottle') {
                    'default' => $model::whereKey($id)->update($toColumns($data[$key])),
                    'reset' => CustomizerLayerOverride::where($match)->delete(),
                    default => CustomizerLayerOverride::updateOrCreate($match, $toColumns($data[$key])),
                };
            }
        });

        return redirect()
            ->route('admin.customizer.alignment', array_filter([
                'bottle' => $data['bottle_id'],
                'cap' => $data['cap_id'] ?? null,
                'fragrance' => $data['fragrance_id'] ?? null,
            ]))
            ->with('status', 'Alignment saved.');
    }
}
