<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use Illuminate\Http\JsonResponse;

class OfferController extends Controller
{
    /** Active offers for the storefront homepage marquee. */
    public function index(): JsonResponse
    {
        $offers = Offer::where('status', 'ACTIVE')->orderBy('sort_order')->orderBy('id')->get();

        return response()->json($offers->map(fn ($o) => [
            'id' => $o->id,
            'text' => $o->text,
            'linkUrl' => $o->link_url,
        ])->values());
    }
}
