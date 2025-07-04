<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Add API key middleware to all Filament API routes
Route::middleware(['filament.api.key'])
    ->prefix('admin/api')
    ->group(function () {
        // This is just to register the middleware
        // Actual routes are handled by Filament API Service Plugin
    });
