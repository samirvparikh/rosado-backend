<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CouponController extends Controller
{
    public function index(): View
    {
        $coupons = Coupon::orderBy('code')->get();

        return view('admin.coupons.index', compact('coupons'));
    }

    public function create(): View
    {
        return view('admin.coupons.form', ['coupon' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        Coupon::create($this->validated($request, true));

        return redirect()->route('admin.coupons.index')->with('status', 'Coupon created.');
    }

    public function edit(Coupon $coupon): View
    {
        return view('admin.coupons.form', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon): RedirectResponse
    {
        $coupon->update($this->validated($request, false));

        return redirect()->route('admin.coupons.index')->with('status', 'Coupon updated.');
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        $coupon->delete();

        return redirect()->route('admin.coupons.index')->with('status', 'Coupon deleted.');
    }

    private function validated(Request $request, bool $isCreate): array
    {
        $rules = [
            'type' => ['required', 'in:PERCENT,FLAT'],
            'value' => ['required', 'numeric', 'min:0'],
            'min_subtotal' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:ACTIVE,INACTIVE'],
        ];

        if ($isCreate) {
            $rules['code'] = ['required', 'string', 'max:30', 'unique:coupons,code'];
        }

        $data = $request->validate($rules);
        if ($isCreate) {
            $data['code'] = strtoupper($data['code']);
        }

        return $data;
    }
}
