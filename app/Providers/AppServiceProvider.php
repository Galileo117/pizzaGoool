<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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

    public function boot()
    {
        // Si la URL contiene ngrok-free, fuerza a Laravel a generar todos sus enlaces usando HTTPS
        if (str_contains(request()->url(), 'ngrok-free')) {
            URL::forceScheme('https');
        }
    }
}
