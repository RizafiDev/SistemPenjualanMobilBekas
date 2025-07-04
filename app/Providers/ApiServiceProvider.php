<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class ApiServiceProvider extends ServiceProvider
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
        // Hook into the route registration to add middleware for API routes
        Route::matched(function ($event) {
            $request = $event->request;
            $route = $event->route;

            // Check if this is a Filament API route
            if ($request->is('admin/api/*')) {
                // Add our custom middleware to API routes
                $route->middleware('filament.api.key');
            }
        });
    }
}
