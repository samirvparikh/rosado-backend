<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CustomizerCatalog;
use Illuminate\Http\JsonResponse;

class CustomizerController extends Controller
{
    public function __construct(private readonly CustomizerCatalog $catalog) {}

    /**
     * GET /api/perfume-customizer/{product?} -- everything the customizer
     * needs for one CUSTOM_PERFUME product (by id or slug; omitted = the
     * default custom product): sizes with base prices, offered fragrances,
     * bottles and caps, and each layer's position on the preview canvas.
     */
    public function show(?string $product = null): JsonResponse
    {
        return response()->json($this->catalog->payload($this->catalog->resolveProduct($product)));
    }
}
