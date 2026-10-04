<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classification;
use App\Models\Fragrance;
use App\Models\FragranceNote;
use App\Models\FragranceSizePrice;
use App\Models\Note;
use App\Models\Size;
use App\Support\CustomizerLayers;
use App\Support\ImageUpload;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FragranceController extends Controller
{
    public function index(): View
    {
        $fragrances = Fragrance::orderBy('name')->get();

        return view('admin.fragrances.index', compact('fragrances'));
    }

    public function create(): View
    {
        return view('admin.fragrances.form', [
            'fragrance' => null,
            ...$this->formData(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, true);

        DB::transaction(function () use ($data) {
            $fragrance = Fragrance::create($data['fragrance']);
            $this->syncRelations($fragrance, $data);
        });

        return redirect()->route('admin.fragrances.index')->with('status', 'Fragrance created.');
    }

    public function edit(Fragrance $fragrance): View
    {
        $fragrance->load(['noteLinks', 'classifications', 'sizePrices']);

        return view('admin.fragrances.form', [
            'fragrance' => $fragrance,
            ...$this->formData(),
        ]);
    }

    public function update(Request $request, Fragrance $fragrance): RedirectResponse
    {
        $data = $this->validated($request, false);

        DB::transaction(function () use ($fragrance, $data) {
            $fragrance->update($data['fragrance']);
            $this->syncRelations($fragrance, $data);
        });

        return redirect()->route('admin.fragrances.index')->with('status', 'Fragrance updated.');
    }

    public function destroy(Fragrance $fragrance): RedirectResponse
    {
        $fragrance->delete();

        return redirect()->route('admin.fragrances.index')->with('status', 'Fragrance deleted.');
    }

    private function formData(): array
    {
        return [
            'families' => Classification::group('FRAGRANCE_FAMILY')->get(),
            'characters' => Classification::group('SCENT_CHARACTER')->get(),
            'notesByType' => [
                'TOP' => Note::where('note_type', 'TOP')->where('status', 'ACTIVE')->orderBy('name')->get(),
                'HEART' => Note::where('note_type', 'HEART')->where('status', 'ACTIVE')->orderBy('name')->get(),
                'BASE' => Note::where('note_type', 'BASE')->where('status', 'ACTIVE')->orderBy('name')->get(),
            ],
            'sizes' => Size::orderBy('sort_order')->get(),
        ];
    }

    private function syncRelations(Fragrance $fragrance, array $data): void
    {
        $fragrance->classifications()->sync([...$data['familyIds'], ...$data['characterIds']]);

        FragranceNote::where('fragrance_id', $fragrance->id)->delete();
        foreach (['TOP', 'HEART', 'BASE'] as $type) {
            foreach (($data['notes'][$type] ?? []) as $index => $noteId) {
                FragranceNote::create([
                    'fragrance_id' => $fragrance->id,
                    'note_id' => $noteId,
                    'note_type' => $type,
                    'sort_order' => $index,
                ]);
            }
        }

        foreach ($data['sizePrices'] as $sizeId => $price) {
            if ($price === null || $price === '') {
                FragranceSizePrice::where('fragrance_id', $fragrance->id)->where('size_id', $sizeId)->delete();

                continue;
            }
            FragranceSizePrice::updateOrCreate(
                ['fragrance_id' => $fragrance->id, 'size_id' => $sizeId],
                ['base_price' => $price],
            );
        }
    }

    private function validated(Request $request, bool $isCreate): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'string', 'max:150'],
            'short_description' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'gender' => ['required', 'in:MEN,WOMEN,UNISEX'],
            'image' => ['nullable', 'string', 'max:500'],
            'image_file' => ['nullable', ...ImageUpload::RULES],
            'liquid_image' => ['nullable', 'string', 'max:500'],
            'liquid_image_file' => ['nullable', ...ImageUpload::LAYER_RULES],
            'remove_liquid_image' => ['sometimes'],
            ...CustomizerLayers::rules(),
            'status' => ['required', 'in:ACTIVE,INACTIVE'],
            'families' => ['array'],
            'characters' => ['array'],
            'notes' => ['array'],
            'notes.TOP' => ['array'],
            'notes.HEART' => ['array'],
            'notes.BASE' => ['array'],
            'size_prices' => ['array'],
            'size_prices.*' => ['nullable', 'numeric', 'min:0'],
        ];

        if ($isCreate) {
            $rules['id'] = ['required', 'string', 'max:30', 'unique:fragrances,id'];
        }

        $validated = $request->validate($rules);

        if ($request->hasFile('image_file')) {
            $validated['image'] = ImageUpload::store($request->file('image_file'), 'fragrances');
        }
        if ($request->hasFile('liquid_image_file')) {
            $validated['liquid_image'] = ImageUpload::store($request->file('liquid_image_file'), 'fragrances');
        }
        if ($request->boolean('remove_liquid_image')) {
            $validated['liquid_image'] = null;
        }

        return [
            'fragrance' => collect($validated)->only([
                'id', 'name', 'slug', 'short_description', 'description', 'gender', 'image', 'liquid_image',
                'layer_top', 'layer_left', 'layer_width', 'layer_z', 'status',
            ])->all(),
            'familyIds' => $validated['families'] ?? [],
            'characterIds' => $validated['characters'] ?? [],
            'notes' => $validated['notes'] ?? [],
            'sizePrices' => $validated['size_prices'] ?? [],
        ];
    }
}
