<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fragrance;
use App\Support\Presenters;
use Illuminate\Http\JsonResponse;

class FragranceController extends Controller
{
    public function index(): JsonResponse
    {
        $fragrances = Fragrance::with(['noteLinks.note', 'classifications', 'sizePrices'])
            ->where('status', 'ACTIVE')
            ->get();

        return response()->json($fragrances->map(fn ($f) => Presenters::fragranceWithNotes($f))->values());
    }

    public function show(string $id): JsonResponse
    {
        $fragrance = Fragrance::with(['noteLinks.note', 'classifications', 'sizePrices'])
            ->where('id', $id)
            ->where('status', 'ACTIVE')
            ->first();

        return response()->json($fragrance ? Presenters::fragranceWithNotes($fragrance) : null);
    }
}
