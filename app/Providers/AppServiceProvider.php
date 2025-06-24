<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {https://chatgpt.com/c/68507aad-9fe8-8004-a6f0-ea7afa036b75
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        Inertia::share([
            'flash' => function () {
                return [
                    'error' => session('error'),
                    'success' => session('success'),
                ];
            },
        ]);
    }
}
