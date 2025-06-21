<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/penjualan/{penjualan}/print', [InvoiceController::class, 'printInvoice'])
        ->name('penjualan.print');

    Route::get('/penjualan/{penjualan}/download', [InvoiceController::class, 'downloadInvoice'])
        ->name('penjualan.download');
});