<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Models\Bottle;
use App\Models\Size;
use App\Support\Presenters;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BottleController extends Controller
{
    /**
     * Rule 5/6: bottles are only ever returned for an active, matching size.
     * GET /api/bottles?size=SIZE50
     */
    public function index(Request $request): JsonResponse
    {
        $sizeId = $request->query('size');

        $query = Bottle::with('inventory')->where('status', 'ACTIVE');

        if ($sizeId) {
            $size = Size::where('id', $sizeId)->where('status', 'ACTIVE')->first();
            if (! $size) {
                throw new ApiException('Unknown size.', 404, 'UNKNOWN_ENTITY');
            }
            $query->where('size_id', $sizeId);
        }

        $bottles = $query->orderBy('sort_order')->get()
            ->filter(fn ($bottle) => ($bottle->inventory?->available_stock ?? 0) > 0)
            ->values();

        return response()->json($bottles->map(fn ($b) => Presenters::bottle($b))->values());
    }
}
