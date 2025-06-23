<?php
// routes/web.php

use App\Http\Controllers\Auth\KaryawanAuthController;
use App\Http\Controllers\Karyawan\AbsensiController;
use App\Http\Controllers\Karyawan\CutiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PenggajianController;

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

/*
|--------------------------------------------------------------------------
| Karyawan Routes
|--------------------------------------------------------------------------
*/
Route::prefix('karyawan')->name('karyawan.')->group(function () {

    // Guest routes (hanya bisa diakses jika belum login)
    Route::middleware(['guest:karyawan'])->group(function () {
        Route::get('/login', [KaryawanAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [KaryawanAuthController::class, 'login'])->name('login.post');
    });

    // Authenticated routes (hanya bisa diakses jika sudah login)
    Route::middleware(['auth:karyawan'])->group(function () {

        // Dashboard - redirect ke absensi
        Route::get('/dashboard', [KaryawanAuthController::class, 'dashboard'])->name('dashboard');

        // Logout
        Route::post('/logout', [KaryawanAuthController::class, 'logout'])->name('logout');

        // Default karyawan route - redirect ke absensi
        Route::get('/', function () {
            return redirect()->route('karyawan.absensi.index');
        });

        // Absensi routes
        Route::prefix('absensi')->name('absensi.')->controller(AbsensiController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/masuk', 'absenMasuk')->name('masuk');
            Route::post('/pulang', 'absenPulang')->name('pulang');
            Route::get('/riwayat', 'riwayat')->name('riwayat');
        });

        // Cuti routes
        Route::prefix('cuti')->name('cuti.')->controller(CutiController::class)->group(function () {
            Route::post('/ajukan', 'ajukan')->name('ajukan');
            Route::get('/riwayat', 'riwayat')->name('riwayat');
        });

        // Catch-all untuk route karyawan yang tidak valid - redirect ke absensi
        Route::fallback(function () {
            return redirect()->route('karyawan.absensi.index')
                ->with('info', 'Halaman yang Anda cari tidak ditemukan. Dialihkan ke panel absensi.');
        });
    });

    // Redirect root karyawan ke login jika guest, ke absensi jika authenticated
    Route::get('/', function () {
        return auth('karyawan')->check()
            ? redirect()->route('karyawan.absensi.index')
            : redirect()->route('karyawan.login');
    });
});

/*
|--------------------------------------------------------------------------
| Other Routes (Penggajian & Penjualan)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // Penggajian routes
    Route::get('/penggajian/{penggajian}/preview', [PenggajianController::class, 'preview'])->name('penggajian.preview');
    Route::get('/penggajian/{penggajian}/print', [PenggajianController::class, 'print'])->name('penggajian.print');
    Route::get('/penggajian/{penggajian}/download', [PenggajianController::class, 'download'])->name('penggajian.download');

    // Penjualan routes
    Route::get('/penjualan/{penjualan}/print', [PenjualanController::class, 'print'])->name('penjualan.print');
    Route::get('/penjualan/{penjualan}/download', [PenjualanController::class, 'downloadPdf'])->name('penjualan.download');
});

/*
|--------------------------------------------------------------------------
| Global Fallback
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    // Jika user adalah karyawan yang sudah login, redirect ke absensi
    if (auth('karyawan')->check()) {
        return redirect()->route('karyawan.absensi.index')
            ->with('warning', 'Halaman yang Anda cari tidak ditemukan.');
    }

    // Jika bukan karyawan atau belum login, tampilkan 404
    return response()->view('errors.404', [], 404);
});