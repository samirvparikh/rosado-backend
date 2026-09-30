<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Classification;
use App\Support\Presenters;
use Illuminate\Http\JsonResponse;

/**
 * One controller serves every classification group (spec section 33's
 * /api/fragrance-families, /api/occasions, /api/seasons, /api/time-of-day,
 * /api/intensities, /api/collections, plus /api/audiences, /api/longevities
 * and /api/scent-characters that the shop filters and product detail page
 * also need). Each route below just pins the `group` value.
 */
class ClassificationController extends Controller
{
    private function list(string $group): JsonResponse
    {
        $items = Classification::group($group)->get();

        return response()->json($items->map(fn ($item) => Presenters::classification($item))->values());
    }

    public function audiences(): JsonResponse
    {
        return $this->list('AUDIENCE');
    }

    public function fragranceFamilies(): JsonResponse
    {
        return $this->list('FRAGRANCE_FAMILY');
    }

    public function occasions(): JsonResponse
    {
        return $this->list('OCCASION');
    }

    public function seasons(): JsonResponse
    {
        return $this->list('SEASON');
    }

    public function timeOfDay(): JsonResponse
    {
        return $this->list('TIME_OF_DAY');
    }

    public function intensities(): JsonResponse
    {
        return $this->list('INTENSITY');
    }

    public function longevities(): JsonResponse
    {
        return $this->list('LONGEVITY');
    }

    public function scentCharacters(): JsonResponse
    {
        return $this->list('SCENT_CHARACTER');
    }

    public function collections(): JsonResponse
    {
        return $this->list('COLLECTION');
    }

    /** Convenience bundle so the shop page's filter sidebar is a single request. */
    public function shopFilters(): JsonResponse
    {
        return response()->json([
            'audiences' => Classification::group('AUDIENCE')->get()->map(fn ($i) => Presenters::classification($i))->values(),
            'fragranceFamilies' => Classification::group('FRAGRANCE_FAMILY')->get()->map(fn ($i) => Presenters::classification($i))->values(),
            'occasions' => Classification::group('OCCASION')->get()->map(fn ($i) => Presenters::classification($i))->values(),
            'seasons' => Classification::group('SEASON')->get()->map(fn ($i) => Presenters::classification($i))->values(),
            'timesOfDay' => Classification::group('TIME_OF_DAY')->get()->map(fn ($i) => Presenters::classification($i))->values(),
            'intensities' => Classification::group('INTENSITY')->get()->map(fn ($i) => Presenters::classification($i))->values(),
            'collections' => Classification::group('COLLECTION')->get()->map(fn ($i) => Presenters::classification($i))->values(),
        ]);
    }
}
