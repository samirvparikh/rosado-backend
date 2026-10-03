<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Support\Presenters;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private function arrayParam(Request $request, string $key): array
    {
        $value = $request->query($key);
        if ($value === null) {
            return [];
        }

        return is_array($value) ? $value : array_filter(explode(',', (string) $value));
    }

    public function index(Request $request): JsonResponse
    {
        $products = Product::with(['images', 'sizes.size', 'classifications'])
            ->where('status', 'ACTIVE')
            ->where('product_type', 'READY_MADE')
            ->get();

        $audience = $this->arrayParam($request, 'audience');
        $fragranceFamily = $this->arrayParam($request, 'fragranceFamily');
        $occasion = $this->arrayParam($request, 'occasion');
        $season = $this->arrayParam($request, 'season');
        $timeOfDay = $this->arrayParam($request, 'timeOfDay');
        $intensity = $this->arrayParam($request, 'intensity');
        $size = $this->arrayParam($request, 'size');
        $badges = $this->arrayParam($request, 'badges');
        $priceMin = $request->query('priceMin');
        $priceMax = $request->query('priceMax');
        $search = trim((string) $request->query('query', ''));
        $sort = $request->query('sort', 'featured');

        $matchesAny = fn (array $selected, array $values) => count($selected) === 0 || count(array_intersect($selected, $values)) > 0;

        $filtered = $products->filter(function (Product $product) use (
            $matchesAny, $audience, $fragranceFamily, $occasion, $season, $timeOfDay, $intensity,
            $size, $badges, $priceMin, $priceMax, $search
        ) {
            $sizeIds = $product->sizes->pluck('size_id')->values()->all();
            $minPrice = $product->fromPrice() ?? 0;

            if (! $matchesAny($audience, $product->classificationIdsByGroup('AUDIENCE'))) {
                return false;
            }
            if (! $matchesAny($fragranceFamily, $product->classificationIdsByGroup('FRAGRANCE_FAMILY'))) {
                return false;
            }
            if (! $matchesAny($occasion, $product->classificationIdsByGroup('OCCASION'))) {
                return false;
            }
            if (! $matchesAny($season, $product->classificationIdsByGroup('SEASON'))) {
                return false;
            }
            if (! $matchesAny($timeOfDay, $product->classificationIdsByGroup('TIME_OF_DAY'))) {
                return false;
            }
            if (! $matchesAny($intensity, $product->classificationIdsByGroup('INTENSITY'))) {
                return false;
            }
            if (! $matchesAny($size, $sizeIds)) {
                return false;
            }
            if ($priceMin !== null && $minPrice < (float) $priceMin) {
                return false;
            }
            if ($priceMax !== null && $minPrice > (float) $priceMax) {
                return false;
            }
            if (in_array('bestSeller', $badges, true) && ! $product->is_best_seller) {
                return false;
            }
            if (in_array('newArrival', $badges, true) && ! $product->is_new_arrival) {
                return false;
            }
            if (in_array('featured', $badges, true) && ! $product->is_featured) {
                return false;
            }
            if (in_array('sale', $badges, true) && ! $product->is_sale) {
                return false;
            }
            if ($search !== '') {
                $familyText = implode(' ', $product->classifications->where('group', 'FRAGRANCE_FAMILY')->pluck('name')->all());
                $tagText = implode(' ', $product->tagList());
                $haystack = mb_strtolower("{$product->name} {$product->short_description} {$familyText} {$tagText}");
                if (! str_contains($haystack, mb_strtolower($search))) {
                    return false;
                }
            }

            return true;
        })->values();

        $items = $filtered->map(fn (Product $p) => Presenters::productListItem($p))->values();

        $items = match ($sort) {
            'price-asc' => $items->sortBy('fromPrice')->values(),
            'price-desc' => $items->sortByDesc('fromPrice')->values(),
            'newest' => $items->sortByDesc('isNewArrival')->values(),
            'best-selling' => $items->sortByDesc('reviewCount')->values(),
            default => $items->sortBy(fn ($item) => [$item['isFeatured'] ? 0 : 1, -$item['rating']])->values(),
        };

        return response()->json($items->values());
    }

    public function show(string $slug): JsonResponse
    {
        $product = Product::with(['images', 'sizes.size', 'classifications', 'fragrance'])
            ->where('slug', $slug)
            ->where('status', 'ACTIVE')
            ->first();

        return response()->json($product ? Presenters::productDetail($product) : null);
    }

    public function featured(): JsonResponse
    {
        $products = Product::with(['images', 'sizes.size', 'classifications'])
            ->where('status', 'ACTIVE')->where('product_type', 'READY_MADE')
            ->where('is_featured', true)
            ->get();

        return response()->json($products->map(fn ($p) => Presenters::productListItem($p))->take(4)->values());
    }

    public function bestSellers(): JsonResponse
    {
        $products = Product::with(['images', 'sizes.size', 'classifications'])
            ->where('status', 'ACTIVE')->where('product_type', 'READY_MADE')
            ->where('is_best_seller', true)
            ->get();

        return response()->json($products->map(fn ($p) => Presenters::productListItem($p))->take(4)->values());
    }

    public function search(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('query', ''));
        if ($query === '') {
            return response()->json([]);
        }

        return $this->index(Request::create('', 'GET', ['query' => $query]));
    }

    /** GET /api/products/:id/recommendations -- other products sharing a fragrance family. */
    public function recommendations(string $id): JsonResponse
    {
        $product = Product::with('classifications')->find($id);
        if (! $product) {
            return response()->json([]);
        }

        $familyIds = $product->classificationIdsByGroup('FRAGRANCE_FAMILY');

        $candidates = Product::with(['images', 'sizes.size', 'classifications'])
            ->where('status', 'ACTIVE')->where('product_type', 'READY_MADE')
            ->where('id', '!=', $product->id)
            ->get()
            ->filter(fn (Product $p) => count(array_intersect($familyIds, $p->classificationIdsByGroup('FRAGRANCE_FAMILY'))) > 0)
            ->sortByDesc('rating')
            ->take(4)
            ->values();

        return response()->json($candidates->map(fn ($p) => Presenters::productListItem($p))->values());
    }
}
