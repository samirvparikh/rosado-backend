<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Size;
use App\Support\Presenters;
use Illuminate\Http\JsonResponse;

class SizeController extends Controller
{
    public function index(): JsonResponse
    {
        $sizes = Size::where('status', 'ACTIVE')->orderBy('sort_order')->get();

        return response()->json($sizes->map(fn ($size) => Presenters::size($size))->values());
    }
}
