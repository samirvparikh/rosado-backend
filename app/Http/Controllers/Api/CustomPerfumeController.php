<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CustomPerfumeValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomPerfumeController extends Controller
{
    public function __construct(private readonly CustomPerfumeValidator $validator) {}

    /** POST /api/custom-perfume/validate -- authoritative check before add-to-cart. */
    public function validateConfiguration(Request $request): JsonResponse
    {
        $data = $request->validate([
            'fragranceId' => ['required', 'string'],
            'sizeId' => ['required', 'string'],
            'bottleId' => ['required', 'string'],
            'capId' => ['required', 'string'],
            'quantity' => ['required', 'integer'],
        ]);

        $result = $this->validator->validate($data);

        return response()->json($result);
    }

    /** POST /api/custom-perfume/price -- live price preview while building. */
    public function price(Request $request): JsonResponse
    {
        $data = $request->validate([
            'fragranceId' => ['nullable', 'string'],
            'sizeId' => ['nullable', 'string'],
            'bottleId' => ['nullable', 'string'],
            'capId' => ['nullable', 'string'],
        ]);

        return response()->json($this->validator->estimate($data));
    }
}
