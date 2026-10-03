<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cap;
use App\Models\CapInventory;
use App\Support\ImageUpload;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CapController extends Controller
{
    public function index(): View
    {
        $caps = Cap::with('inventory')->orderBy('sort_order')->get();

        return view('admin.caps.index', compact('caps'));
    }

    public function create(): View
    {
        return view('admin.caps.form', ['cap' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, true);
        $cap = Cap::create($data);

        CapInventory::create([
            'cap_id' => $cap->id,
            'current_stock' => $data['stock'],
            'reserved_stock' => 0,
            'available_stock' => $data['stock'],
            'reorder_level' => 8,
            'status' => $data['status'],
        ]);

        return redirect()->route('admin.caps.index')->with('status', 'Cap created.');
    }

    public function edit(Cap $cap): View
    {
        return view('admin.caps.form', compact('cap'));
    }

    public function update(Request $request, Cap $cap): RedirectResponse
    {
        $data = $this->validated($request, false);
        $cap->update($data);

        $inventory = $cap->inventory ?? new CapInventory(['cap_id' => $cap->id, 'reserved_stock' => 0]);
        $inventory->current_stock = $data['stock'];
        $inventory->available_stock = max(0, $data['stock'] - $inventory->reserved_stock);
        $inventory->status = $data['status'];
        $inventory->save();

        return redirect()->route('admin.caps.index')->with('status', 'Cap updated.');
    }

    public function destroy(Cap $cap): RedirectResponse
    {
        $cap->delete();

        return redirect()->route('admin.caps.index')->with('status', 'Cap deleted.');
    }

    private function validated(Request $request, bool $isCreate): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:150'],
            'code' => ['required', 'string', 'max:50', 'unique:caps,code'.($isCreate ? '' : ",{$request->route('cap')?->id},id")],
            'image' => ['nullable', 'string', 'max:500'],
            'image_file' => ['nullable', ...ImageUpload::RULES],
            'additional_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:ACTIVE,INACTIVE'],
        ];

        if ($isCreate) {
            $rules['id'] = ['required', 'string', 'max:30', 'unique:caps,id'];
        }

        $data = $request->validate($rules);

        if ($request->hasFile('image_file')) {
            $data['image'] = ImageUpload::store($request->file('image_file'), 'caps');
        }
        unset($data['image_file']);

        return $data;
    }
}
