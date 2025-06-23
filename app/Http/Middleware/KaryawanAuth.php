<?php
// app/Http/Middleware/KaryawanAuth.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class KaryawanAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user authenticated dengan guard karyawan
        if (!Auth::guard('karyawan')->check()) {
            // Simpan intended URL untuk redirect setelah login
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            session(['url.intended' => $request->url()]);
            return redirect()->route('karyawan.login')
                ->with('warning', 'Silakan login terlebih dahulu untuk mengakses halaman ini.');
        }

        // Cek apakah karyawan masih aktif
        $karyawan = Auth::guard('karyawan')->user();

        if (!$karyawan) {
            Auth::guard('karyawan')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('karyawan.login')
                ->withErrors(['error' => 'Sesi Anda telah berakhir. Silakan login kembali.']);
        }

        if ($karyawan->aktif !== 'aktif') {
            Auth::guard('karyawan')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('karyawan.login')
                ->withErrors(['error' => 'Akun Anda telah dinonaktifkan. Hubungi administrator untuk informasi lebih lanjut.']);
        }

        // Set user data dalam session untuk akses cepat
        if (!session()->has('karyawan.data')) {
            session([
                'karyawan.data' => [
                    'id' => $karyawan->id,
                    'nip' => $karyawan->nip,
                    'nama' => $karyawan->nama,
                    'last_activity' => now()
                ]
            ]);
        }

        return $next($request);
    }
}