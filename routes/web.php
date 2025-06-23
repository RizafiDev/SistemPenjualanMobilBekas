<?php
// routes/web.php

use App\Http\Controllers\Auth\KaryawanAuthController;
use App\Http\Controllers\Karyawan\AbsensiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Route untuk halaman utama
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Default login route untuk Laravel auth system
Route::get('/login', function () {
    return redirect()->route('karyawan.login');
})->name('login');

// Routes untuk Authentication Karyawan
Route::prefix('karyawan')->name('karyawan.')->group(function () {

    // Routes yang tidak memerlukan authentication
    Route::middleware(['guest:karyawan', 'web'])->group(function () {
        Route::get('/login', [KaryawanAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [KaryawanAuthController::class, 'login'])->name('login.post');

        // Redirect root karyawan path ke login jika belum authenticated
        Route::get('/', function () {
            return redirect()->route('karyawan.login');
        });
    });

    // Routes yang memerlukan authentication karyawan
    Route::middleware(['web', 'auth:karyawan'])->group(function () {
        Route::get('/dashboard', [KaryawanAuthController::class, 'dashboard'])->name('dashboard');
        Route::post('/logout', [KaryawanAuthController::class, 'logout'])->name('logout');

        // Routes untuk Absensi
        Route::prefix('absensi')->name('absensi.')->group(function () {
            Route::get('/', [AbsensiController::class, 'index'])->name('index');
            Route::post('/masuk', [AbsensiController::class, 'absenMasuk'])->name('masuk');
            Route::post('/pulang', [AbsensiController::class, 'absenPulang'])->name('pulang');
            Route::get('/riwayat', [AbsensiController::class, 'riwayat'])->name('riwayat');
        });

        // Pengajuan Cuti
        Route::post('/cuti/ajukan', [\App\Http\Controllers\Karyawan\CutiController::class, 'ajukan'])->name('cuti.ajukan');
    });
});

// Route fallback untuk handle 404
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});

Route::get('/penggajian/{penggajian}/print', function (App\Models\Penggajian $penggajian) {
    return view('penggajian.slip', compact('penggajian'));
})->name('penggajian.print');