<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassificationController extends Controller
{
    private const GROUPS = [
        'AUDIENCE' => 'Audience',
        'FRAGRANCE_FAMILY' => 'Fragrance Family',
        'OCCASION' => 'Occasion',
        'SEASON' => 'Season',
        'TIME_OF_DAY' => 'Time of Day',
        'INTENSITY' => 'Intensity',
        'LONGEVITY' => 'Longevity',
        'SCENT_CHARACTER' => 'Scent Character',
        'COLLECTION' => 'Collection',
    ];

    public function index(): View
    {
        $classifications = Classification::orderBy('group')->orderBy('sort_order')->get()->groupBy('group');

        return view('admin.classifications.index', ['classifications' => $classifications, 'groups' => self::GROUPS]);
    }

    public function create(): View
    {
        return view('admin.classifications.form', ['classification' => null, 'groups' => self::GROUPS]);
    }

    public function store(Request $request): RedirectResponse
    {
        Classification::create($this->validated($request, true));

        return redirect()->route('admin.classifications.index')->with('status', 'Classification created.');
    }

    public function edit(Classification $classification): View
    {
        return view('admin.classifications.form', ['classification' => $classification, 'groups' => self::GROUPS]);
    }

    public function update(Request $request, Classification $classification): RedirectResponse
    {
        $classification->update($this->validated($request, false));

        return redirect()->route('admin.classifications.index')->with('status', 'Classification updated.');
    }

    public function destroy(Classification $classification): RedirectResponse
    {
        $classification->delete();

        return redirect()->route('admin.classifications.index')->with('status', 'Classification deleted.');
    }

    private function validated(Request $request, bool $isCreate): array
    {
        $rules = [
            'group' => ['required', 'in:'.implode(',', array_keys(self::GROUPS))],
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['required', 'string', 'max:100'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:ACTIVE,INACTIVE'],
        ];

        if ($isCreate) {
            $rules['id'] = ['required', 'string', 'max:40', 'unique:classifications,id'];
        }

        return $request->validate($rules);
    }
}
