<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Size;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SizeController extends Controller
{
    public function index(): View
    {
        $sizes = Size::orderBy('sort_order')->get();

        return view('admin.sizes.index', compact('sizes'));
    }

    public function create(): View
    {
        return view('admin.sizes.form', ['size' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, true);
        Size::create($data);

        return redirect()->route('admin.sizes.index')->with('status', 'Size created.');
    }

    public function edit(Size $size): View
    {
        return view('admin.sizes.form', compact('size'));
    }

    public function update(Request $request, Size $size): RedirectResponse
    {
        $size->update($this->validated($request, false));

        return redirect()->route('admin.sizes.index')->with('status', 'Size updated.');
    }

    public function destroy(Size $size): RedirectResponse
    {
        $size->delete();

        return redirect()->route('admin.sizes.index')->with('status', 'Size deleted.');
    }

    private function validated(Request $request, bool $isCreate): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:100'],
            'size_ml' => ['required', 'integer', 'min:1'],
            'display_name' => ['required', 'string', 'max:100'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:ACTIVE,INACTIVE'],
        ];

        if ($isCreate) {
            $rules['id'] = ['required', 'string', 'max:30', 'unique:sizes,id'];
        }

        return $request->validate($rules);
    }
}
