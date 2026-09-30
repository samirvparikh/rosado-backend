<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShippingMethodController extends Controller
{
    public function index(): View
    {
        $shippingMethods = ShippingMethod::orderBy('sort_order')->get();

        return view('admin.shipping-methods.index', compact('shippingMethods'));
    }

    public function create(): View
    {
        return view('admin.shipping-methods.form', ['shippingMethod' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        ShippingMethod::create($this->validated($request, true));

        return redirect()->route('admin.shipping-methods.index')->with('status', 'Shipping method created.');
    }

    public function edit(ShippingMethod $shippingMethod): View
    {
        return view('admin.shipping-methods.form', compact('shippingMethod'));
    }

    public function update(Request $request, ShippingMethod $shippingMethod): RedirectResponse
    {
        $shippingMethod->update($this->validated($request, false));

        return redirect()->route('admin.shipping-methods.index')->with('status', 'Shipping method updated.');
    }

    public function destroy(ShippingMethod $shippingMethod): RedirectResponse
    {
        $shippingMethod->delete();

        return redirect()->route('admin.shipping-methods.index')->with('status', 'Shipping method deleted.');
    }

    private function validated(Request $request, bool $isCreate): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'eta' => ['required', 'string', 'max:100'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:ACTIVE,INACTIVE'],
        ];

        if ($isCreate) {
            $rules['id'] = ['required', 'string', 'max:30', 'unique:shipping_methods,id'];
        }

        return $request->validate($rules);
    }
}
