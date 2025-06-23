<?php
// app/Http/Controllers/Auth/KaryawanAuthController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class KaryawanAuthController extends Controller
{
    /**
     * Menampilkan form login karyawan.
     */
    public function showLoginForm(): RedirectResponse|View
    {
        // Jika sudah login, redirect ke absensi
        if (Auth::guard('karyawan')->check()) {
            return redirect()->route('karyawan.absensi.index');
        }

        return view('auth.karyawan.login');
    }

    /**
     * Menangani proses login
     */
    public function login(Request $request): RedirectResponse
    {
        // Validasi input
        $credentials = $this->validateLoginRequest($request);

        // Rate limiting
        $throttleKey = $this->getThrottleKey($credentials['nip'], $request->ip());
        $this->checkRateLimit($throttleKey);

        // Proses autentikasi
        $karyawan = $this->findKaryawan($credentials['nip']);
        $this->validateKaryawan($karyawan, $throttleKey);
        $this->validatePassword($credentials['password'], $karyawan, $throttleKey);

        // Login berhasil
        return $this->handleSuccessfulLogin($request, $karyawan, $throttleKey);
    }

    /**
     * Logout karyawan
     */
    public function logout(Request $request): RedirectResponse
    {
        $karyawan = Auth::guard('karyawan')->user();

        // Update logout time
        if ($karyawan) {
            $karyawan->update(['last_logout_at' => now()]);
        }

        // Logout dan bersihkan session
        Auth::guard('karyawan')->logout();
        $request->session()->forget(['karyawan.data', 'url.intended']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('karyawan.login')
            ->with('success', 'Anda telah berhasil keluar dari sistem.');
    }

    /**
     * Dashboard redirect ke absensi
     */
    public function dashboard(): RedirectResponse
    {
        return redirect()->route('karyawan.absensi.index')
            ->with('info', 'Selamat datang di panel absensi karyawan.');
    }

    /**
     * Validasi request login
     */
    private function validateLoginRequest(Request $request): array
    {
        return $request->validate([
            'nip' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'nip.required' => 'NIP wajib diisi.',
            'nip.max' => 'NIP tidak boleh lebih dari 20 karakter.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);
    }

    /**
     * Generate throttle key untuk rate limiting
     */
    private function getThrottleKey(string $nip, string $ip): string
    {
        return Str::transliterate(Str::lower($nip) . '|' . $ip);
    }

    /**
     * Cek rate limiting
     */
    private function checkRateLimit(string $throttleKey): void
    {
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'nip' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
            ]);
        }
    }

    /**
     * Cari karyawan berdasarkan NIP
     */
    private function findKaryawan(string $nip): ?Karyawan
    {
        return Karyawan::where('nip', $nip)->first();
    }

    /**
     * Validasi karyawan dan statusnya
     */
    private function validateKaryawan(?Karyawan $karyawan, string $throttleKey): void
    {
        if (!$karyawan) {
            RateLimiter::hit($throttleKey, 300);
            throw ValidationException::withMessages([
                'nip' => 'NIP tidak ditemukan dalam sistem.',
            ]);
        }

        if ($karyawan->aktif !== 'aktif') {
            RateLimiter::hit($throttleKey, 300);
            throw ValidationException::withMessages([
                'nip' => 'Akun Anda telah dinonaktifkan. Hubungi administrator.',
            ]);
        }
    }

    /**
     * Validasi password
     */
    private function validatePassword(string $password, Karyawan $karyawan, string $throttleKey): void
    {
        if (!Hash::check($password, $karyawan->password)) {
            RateLimiter::hit($throttleKey, 300);
            throw ValidationException::withMessages([
                'password' => 'Password yang Anda masukkan salah.',
            ]);
        }
    }

    /**
     * Handle login yang berhasil
     */
    private function handleSuccessfulLogin(Request $request, Karyawan $karyawan, string $throttleKey): RedirectResponse
    {
        // Clear rate limiting
        RateLimiter::clear($throttleKey);

        // Update data karyawan
        $karyawan->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        // Login dengan guard karyawan
        Auth::guard('karyawan')->login($karyawan, $request->boolean('remember'));

        // Regenerate session
        $request->session()->regenerate();

        // Set session data
        session([
            'karyawan.data' => [
                'id' => $karyawan->id,
                'nip' => $karyawan->nip,
                'nama' => $karyawan->nama,
                'login_time' => now()->timestamp,
            ]
        ]);

        // Redirect ke intended URL atau absensi
        $intended = session('url.intended', route('karyawan.absensi.index'));
        session()->forget('url.intended');

        return redirect($intended)
            ->with('success', 'Selamat datang, ' . $karyawan->nama . '!');
    }
}