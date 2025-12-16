<?php

namespace App\Providers;

use App\Models\Customer;
use App\Services\SimpleJwtService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Auth::viaRequest('customer-jwt', function ($request) {
            $token = $request->bearerToken();
            if (! $token) {
                return null;
            }

            /** @var SimpleJwtService $jwt */
            $jwt = app(SimpleJwtService::class);
            $payload = $jwt->decode($token);
            if (! $payload || empty($payload['sub'])) {
                return null;
            }

            return Customer::find($payload['sub']);
        });
    }
}
