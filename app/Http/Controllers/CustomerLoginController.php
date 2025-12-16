<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerLoginController extends Controller
{
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::guard('customer')->attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 422);
        }

        // verify customer email verification status
        $customer = Auth::guard('customer')->user();
        if (! $customer->verified) {
            Auth::guard('customer')->logout();

            return response()->json(['message' => 'please verify your email address'], 403);
        }

        $request->session()->regenerate();

        return response()->json(['message' => 'Logged in']);
    }

    public function destroy(Request $request)
    {
        // Log out from the CUSTOMER guard only
        Auth::guard('customer')->logout();

        // Invalidate session completely
        $request->session()->invalidate();

        // Regenerate CSRF token
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out'], 200);
    }
}
