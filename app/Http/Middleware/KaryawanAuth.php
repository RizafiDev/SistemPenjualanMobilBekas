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
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek authentication dengan guard karyawan
        if (!Auth::guard('karyawan')->check()) {
            return $this->handleUnauthenticated($request);
        }

        // Validasi status karyawan
        $karyawan = Auth::guard('karyawan')->user();
        if (!$this->isKaryawanValid($karyawan)) {
            return $this->handleInvalidKaryawan($request);
        }

        // Update session data
        $this->updateSessionData($karyawan);

        return $next($request);
    }

    /**
     * Handle unauthenticated request
     */
    private function handleUnauthenticated(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Unauthenticated.',
                'redirect' => route('karyawan.login')
            ], 401);
        }

        // Simpan intended URL
        session(['url.intended' => $request->url()]);

        return redirect()->route('karyawan.login')
            ->with('warning', 'Silakan login terlebih dahulu untuk mengakses halaman ini.');
    }

    /**
     * Validate karyawan status
     */
    private function isKaryawanValid($karyawan): bool
    {
        return $karyawan && $karyawan->aktif === 'aktif';
    }

    /**
     * Handle invalid karyawan (inactive or deleted)
     */
    private function handleInvalidKaryawan(Request $request): Response
    {
        // Logout dan bersihkan session
        Auth::guard('karyawan')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $message = !Auth::guard('karyawan')->user()
            ? 'Sesi Anda telah berakhir. Silakan login kembali.'
            : 'Akun Anda telah dinonaktifkan. Hubungi administrator.';

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'redirect' => route('karyawan.login')
            ], 401);
        }

        return redirect()->route('karyawan.login')->withErrors(['error' => $message]);
    }

    /**
     * Update session data for quick access
     */
    private function updateSessionData($karyawan): void
    {
        if (!session()->has('karyawan.data') || $this->shouldUpdateSession()) {
            session([
                'karyawan.data' => [
                    'id' => $karyawan->id,
                    'nip' => $karyawan->nip,
                    'nama' => $karyawan->nama,
                    'last_activity' => now()->timestamp
                ]
            ]);
        }
    }

    /**
     * Check if session should be updated (every 5 minutes)
     */
    private function shouldUpdateSession(): bool
    {
        $lastActivity = session('karyawan.data.last_activity', 0);
        return (now()->timestamp - $lastActivity) > 300; // 5 minutes
    }
}