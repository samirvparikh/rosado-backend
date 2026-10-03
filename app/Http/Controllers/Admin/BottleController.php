<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bottle;
use App\Models\BottleInventory;
use App\Models\Size;
use App\Support\ImageUpload;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BottleController extends Controller
{
    public function index(): View
    {
        $bottles = Bottle::with(['size', 'inventory'])->orderBy('sort_order')->get();

        return view('admin.bottles.index', compact('bottles'));
    }

    public function create(): View
    {
        $sizes = Size::orderBy('sort_order')->pluck('display_name', 'id');

        return view('admin.bottles.form', ['bottle' => null, 'sizes' => $sizes]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, true);
        $bottle = Bottle::create($data);

        BottleInventory::create([
            'bottle_id' => $bottle->id,
            'current_stock' => $data['stock'],
            'reserved_stock' => 0,
            'available_stock' => $data['stock'],
            'reorder_level' => 5,
            'status' => $data['status'],
        ]);

        return redirect()->route('admin.bottles.index')->with('status', 'Bottle created.');
    }

    public function edit(Bottle $bottle): View
    {
        $sizes = Size::orderBy('sort_order')->pluck('display_name', 'id');

        return view('admin.bottles.form', compact('bottle', 'sizes'));
    }

    public function update(Request $request, Bottle $bottle): RedirectResponse
    {
        $data = $this->validated($request, false);
        $bottle->update($data);

        $inventory = $bottle->inventory ?? new BottleInventory(['bottle_id' => $bottle->id, 'reserved_stock' => 0]);
        $inventory->current_stock = $data['stock'];
        $inventory->available_stock = max(0, $data['stock'] - $inventory->reserved_stock);
        $inventory->status = $data['status'];
        $inventory->save();

        return redirect()->route('admin.bottles.index')->with('status', 'Bottle updated.');
    }

    public function destroy(Bottle $bottle): RedirectResponse
    {
        $bottle->delete();

        return redirect()->route('admin.bottles.index')->with('status', 'Bottle deleted.');
    }

    private function validated(Request $request, bool $isCreate): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:150'],
            'code' => ['required', 'string', 'max:50', 'unique:bottles,code'.($isCreate ? '' : ",{$request->route('bottle')?->id},id")],
            'image' => ['nullable', 'string', 'max:500'],
            'image_file' => ['nullable', ...ImageUpload::RULES],
            'size_id' => ['required', 'exists:sizes,id'],
            'additional_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:ACTIVE,INACTIVE'],
        ];

        if ($isCreate) {
            $rules['id'] = ['required', 'string', 'max:30', 'unique:bottles,id'];
        }

        $data = $request->validate($rules);

        if ($request->hasFile('image_file')) {
            $data['image'] = ImageUpload::store($request->file('image_file'), 'bottles');
        }
        unset($data['image_file']);

        return $data;
    }
}
