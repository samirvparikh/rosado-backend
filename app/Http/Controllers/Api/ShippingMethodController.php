<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShippingMethod;
use Illuminate\Http\JsonResponse;

class ShippingMethodController extends Controller
{
    public function index(): JsonResponse
    {
        $methods = ShippingMethod::where('status', 'ACTIVE')->orderBy('sort_order')->get();

        return response()->json($methods->map(fn ($m) => [
            'id' => $m->id,
            'name' => $m->name,
            'description' => $m->description,
            'price' => (float) $m->price,
            'eta' => $m->eta,
        ])->values());
    }
}
