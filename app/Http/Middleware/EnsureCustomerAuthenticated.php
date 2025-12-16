<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureCustomerAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        // Prefer JWT guard, but fall back to session-based customer guard.
        if (Auth::guard('customer_jwt')->check()) {
            Auth::shouldUse('customer_jwt');

            return $next($request);
        }

        if (Auth::guard('customer')->check()) {
            Auth::shouldUse('customer');

            return $next($request);
        }

        return response()->json(['message' => 'Unauthenticated'], 401);
    }
}
