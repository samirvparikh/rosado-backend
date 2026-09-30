<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\Presenters;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** Only ready-made products are wish-listable (spec section 41). */
class WishlistController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $products = $request->user()->wishlists()->with('product.images', 'product.sizes', 'product.classifications')->get()
            ->pluck('product')
            ->filter();

        return response()->json($products->map(fn ($p) => Presenters::productListItem($p))->values());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate(['productId' => ['required', 'string', 'exists:products,id']]);

        $request->user()->wishlists()->firstOrCreate(['product_id' => $data['productId']]);

        return response()->json(['ok' => true], 201);
    }

    public function destroy(Request $request, string $productId): JsonResponse
    {
        $request->user()->wishlists()->where('product_id', $productId)->delete();

        return response()->json(['ok' => true]);
    }
}
