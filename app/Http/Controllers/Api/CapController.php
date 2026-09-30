<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cap;
use App\Support\Presenters;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CapController extends Controller
{
    /** GET /api/caps?size=SIZE50 (size is optional -- empty mapping = available everywhere). */
    public function index(Request $request): JsonResponse
    {
        $sizeId = $request->query('size');

        $caps = Cap::with(['inventory', 'sizeMappings'])
            ->where('status', 'ACTIVE')
            ->orderBy('sort_order')
            ->get()
            ->filter(fn ($cap) => ! $sizeId || $cap->isCompatibleWithSize($sizeId))
            ->filter(fn ($cap) => ($cap->inventory?->available_stock ?? 0) > 0)
            ->values();

        return response()->json($caps->map(fn ($c) => Presenters::cap($c))->values());
    }
}
