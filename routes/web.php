<?php

use App\Http\Controllers\CustomerBookingController;
use App\Http\Controllers\CustomerJwtAuthController;
use App\Http\Controllers\CustomerLoginController;
use App\Http\Controllers\CustomerRegisterController;
use App\Http\Controllers\Dashboard\EventsController;
use App\Http\Controllers\Dashboard\MenuController;
use App\Http\Controllers\Dashboard\PageController;
use App\Http\Controllers\Dashboard\StoreConfigController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->prefix('dashboard')->group(function () {
    Route::get('/menu', [MenuController::class, 'index'])
        ->name('dashboard.menu.index');
    Route::post('/menu', [MenuController::class, 'store'])
        ->name('dashboard.menu.store');

    Route::resource('/pages', PageController::class)
        ->names('dashboard.pages');
    Route::post('/pages/upload-image', [PageController::class, 'uploadImage'])
        ->name('dashboard.pages.upload-image');

    Route::get('/events', [EventsController::class, 'index'])
        ->name('dashboard.events.index');

    Route::get('/pending-events', [EventsController::class, 'pending'])
        ->name('dashboard.events.pending');

    Route::get('/canceled-events', [EventsController::class, 'canceled'])
        ->name('dashboard.events.canceled');

    Route::get('/events/{event}/edit', [EventsController::class, 'edit'])
        ->name('dashboard.events.edit');

    Route::get('/events/create', [EventsController::class, 'create'])
        ->name('dashboard.events.create');

    Route::patch('/events/{event}/approve', [EventsController::class, 'approve'])
        ->name('dashboard.events.approve');

    Route::patch('/events/{event}/reject', [EventsController::class, 'reject'])
        ->name('dashboard.events.reject');

    Route::delete('/events/{event}', [EventsController::class, 'destroy'])
        ->name('dashboard.events.destroy');

    Route::put('/events/{event}', [EventsController::class, 'update'])
        ->name('dashboard.events.update');
    Route::post('/events', [EventsController::class, 'store'])
        ->name('dashboard.events.store');

    Route::get('/config', [StoreConfigController::class, 'index'])
        ->name('dashboard.config.index');

    Route::post('/config', [StoreConfigController::class, 'store'])
        ->name('dashboard.config.store');

    Route::post('/config/model', [StoreConfigController::class, 'storeModel'])
        ->name('dashboard.config.updatemodel');
    Route::get('/config/{config}/edit', [StoreConfigController::class, 'edit'])
        ->name('dashboard.config.edit');
    Route::put('/config/{config}', [StoreConfigController::class, 'update'])
        ->name('dashboard.config.update');
    Route::delete('/config/{config}', [StoreConfigController::class, 'destroy'])
        ->name('dashboard.config.destroy');

});

Route::prefix('/customer')
    ->withoutMiddleware([VerifyCsrfToken::class])
    ->group(function () {
        // Registration (React)
        Route::post('/register', [CustomerRegisterController::class, 'store'])
            ->middleware('guest:customer')
            ->name('customer.register');

        Route::post('/login', [CustomerLoginController::class, 'store'])
            ->middleware('guest:customer');

        Route::post('/jwt/login', [CustomerJwtAuthController::class, 'login'])
            ->middleware('guest:customer');
        Route::post('/jwt/refresh', [CustomerJwtAuthController::class, 'refresh']);
        Route::post('/jwt/logout', [CustomerJwtAuthController::class, 'logout'])
            ->middleware('customer.auth');
        Route::get('/jwt/me', [CustomerJwtAuthController::class, 'me'])
            ->middleware('customer.auth');

        Route::get('/me', fn () => auth('customer')->user())
            ->middleware('customer.auth');

        Route::post('/logout', [CustomerLoginController::class, 'destroy'])
            ->middleware('customer.auth');

        Route::post('/book-event', [CustomerBookingController::class, 'store'])
            ->middleware(['web', 'customer.auth']);

        Route::post('/cancel-ticket', [CustomerBookingController::class, 'cancel'])
            ->middleware(['web', 'customer.auth']);

        Route::get('/ticket/status/{event}', [CustomerBookingController::class, 'check'])
            ->middleware('customer.auth');

    });

Route::get('/event/ticket/{ticket}/{hash}/{customer}',
    function () {
        return view('app.index');
    }
)->name('ticket.viewer');

Route::get('/api/pages/{slug}', [PageController::class, 'showBySlug'])
    ->name('api.pages.show');

Route::get('/{any?}', function () {
    return view('app.index');
})->where('any', '.*');
