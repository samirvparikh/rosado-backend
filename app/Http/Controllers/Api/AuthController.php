<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    private function session(User $user, ?string $token = null): array
    {
        $payload = [
            'customer' => [
                'id' => (string) $user->id,
                'fullName' => $user->name,
                'email' => $user->email,
                'mobile' => $user->mobile,
            ],
            'addresses' => $user->addresses->sortByDesc(fn ($a) => [$a->is_default, $a->id])->map(fn ($a) => [
                'id' => (string) $a->id,
                'isDefault' => (bool) $a->is_default,
                'fullName' => $a->full_name,
                'mobile' => $a->mobile,
                'email' => $a->email,
                'address' => $a->address_line,
                'city' => $a->city,
                'state' => $a->state,
                'pincode' => $a->pincode,
            ])->values(),
        ];

        if ($token !== null) {
            $payload['token'] = $token;
        }

        return $payload;
    }

    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'fullName' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'mobile' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create([
            'name' => $data['fullName'],
            'email' => $data['email'],
            'mobile' => $data['mobile'],
            'password' => Hash::make($data['password']),
        ]);
        $user->setRelation('addresses', collect());

        $token = $user->createToken('rosado-storefront')->plainTextToken;

        return response()->json($this->session($user, $token), 201);
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $data['email'])->first();
        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw new ApiException('Invalid email or password.', 401, 'INVALID_CREDENTIALS');
        }

        $user->load('addresses');
        $token = $user->createToken('rosado-storefront')->plainTextToken;

        return response()->json($this->session($user, $token));
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['ok' => true]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load('addresses');

        return response()->json($this->session($user));
    }
}
