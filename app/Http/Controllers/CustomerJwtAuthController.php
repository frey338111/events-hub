<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\SimpleJwtService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerJwtAuthController extends Controller
{
    public function __construct(private SimpleJwtService $jwt) {}

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $customer = Customer::where('email', $credentials['email'])->first();
        if (! $customer || ! Hash::check($credentials['password'], $customer->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        if (! $customer->verified) {
            return response()->json(['message' => 'please verify your email address'], 403);
        }

        $token = $this->jwt->createToken($customer);

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->jwt->ttl(),
            'user' => $customer,
        ]);
    }

    public function refresh(Request $request)
    {
        $token = $request->bearerToken();
        $payload = $token ? $this->jwt->decode($token) : null;

        if (! $payload || empty($payload['sub'])) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        $customer = Customer::find($payload['sub']);
        if (! $customer) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        $newToken = $this->jwt->createToken($customer);

        return response()->json([
            'access_token' => $newToken,
            'token_type' => 'bearer',
            'expires_in' => $this->jwt->ttl(),
            'user' => $customer,
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function logout()
    {
        // Stateless JWT; instruct client to delete token
        return response()->json(['message' => 'Logged out']);
    }
}
