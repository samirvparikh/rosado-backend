<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    private function present(Address $address): array
    {
        return [
            'id' => (string) $address->id,
            'fullName' => $address->full_name,
            'mobile' => $address->mobile,
            'email' => $address->email,
            'address' => $address->address_line,
            'city' => $address->city,
            'state' => $address->state,
            'pincode' => $address->pincode,
            'isDefault' => $address->is_default,
        ];
    }

    public function index(Request $request): JsonResponse
    {
        $addresses = $request->user()->addresses()->orderByDesc('is_default')->get();

        return response()->json($addresses->map(fn ($a) => $this->present($a))->values());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'fullName' => ['required', 'string'],
            'mobile' => ['required', 'string'],
            'email' => ['nullable', 'email'],
            'address' => ['required', 'string'],
            'city' => ['required', 'string'],
            'state' => ['required', 'string'],
            'pincode' => ['required', 'string'],
            'isDefault' => ['boolean'],
        ]);

        if ($data['isDefault'] ?? false) {
            $request->user()->addresses()->update(['is_default' => false]);
        }

        $address = $request->user()->addresses()->create([
            'full_name' => $data['fullName'],
            'mobile' => $data['mobile'],
            'email' => $data['email'] ?? null,
            'address_line' => $data['address'],
            'city' => $data['city'],
            'state' => $data['state'],
            'pincode' => $data['pincode'],
            'is_default' => $data['isDefault'] ?? false,
        ]);

        return response()->json($this->present($address), 201);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $request->user()->addresses()->where('id', $id)->delete();

        return response()->json(['ok' => true]);
    }
}
