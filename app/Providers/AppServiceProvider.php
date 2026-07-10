<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        Password::defaults(function () {
            return Password::min(8)
                ->mixedCase()
                ->numbers();
        });

        // Rate limiters for booking-related actions
        RateLimiter::for('booking-verify', function (Request $request) {
            return Limit::perMinute(10)
                ->by($request->ip())
                ->response(function () {
                    return back()->with('error', 'Terlalu banyak percobaan. Silakan tunggu sebentar lalu coba lagi.');
                });
        });

        RateLimiter::for('booking-store', function (Request $request) {
            return Limit::perMinute(5)
                ->by(optional($request->user())->id ?: $request->ip())
                ->response(function () {
                    return back()->with('error', 'Terlalu banyak percobaan. Silakan tunggu sebentar lalu coba lagi.');
                });
        });

        RateLimiter::for('payment-initiate', function (Request $request) {
            return Limit::perMinute(10)
                ->by($request->ip())
                ->response(function () {
                    return back()->with('error', 'Terlalu banyak percobaan. Silakan tunggu sebentar lalu coba lagi.');
                });
        });

        RateLimiter::for('admin-login', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->ip())
                ->response(function () {
                    return back()->with('error', 'Terlalu banyak percobaan masuk. Silakan tunggu sebentar lalu coba lagi.');
                });
        });
    }
}
