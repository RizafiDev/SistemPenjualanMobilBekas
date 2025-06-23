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
    public function showLoginForm(Request $request): View
    {
        // Jika sudah login, redirect ke dashboard
        if (Auth::guard('karyawan')->check()) {
            return redirect()->route('karyawan.dashboard');
        }

        return view('auth.karyawan.login');
    }

    /**
     * Menangani upaya otentikasi.
     */
    public function login(Request $request): RedirectResponse
    {
        // Validasi input
        $credentials = $request->validate([
            'nip' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'nip.required' => 'NIP wajib diisi.',
            'nip.max' => 'NIP tidak boleh lebih dari 20 karakter.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        // Rate limiting untuk mencegah brute force
        $throttleKey = Str::transliterate(Str::lower($credentials['nip']) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'nip' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        // Cari karyawan berdasarkan NIP
        $karyawan = Karyawan::where('nip', $credentials['nip'])->first();

        if (!$karyawan) {
            RateLimiter::hit($throttleKey, 300); // 5 menit
            return back()->withErrors([
                'nip' => 'NIP tidak ditemukan dalam sistem.',
            ])->onlyInput('nip');
        }

        // Cek status aktif karyawan
        if ($karyawan->aktif !== 'aktif') {
            RateLimiter::hit($throttleKey, 300);
            return back()->withErrors([
                'nip' => 'Akun Anda telah dinonaktifkan. Hubungi administrator.',
            ])->onlyInput('nip');
        }

        // Verifikasi password
        if (!Hash::check($credentials['password'], $karyawan->password)) {
            RateLimiter::hit($throttleKey, 300);
            return back()->withErrors([
                'password' => 'Password yang Anda masukkan salah.',
            ])->onlyInput('nip');
        }

        // Login berhasil
        RateLimiter::clear($throttleKey);

        // Update last login
        $karyawan->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        // Login menggunakan guard karyawan
        Auth::guard('karyawan')->login($karyawan, $request->boolean('remember'));

        // Regenerate session untuk keamanan
        $request->session()->regenerate();

        // Set session data
        session([
            'karyawan.data' => [
                'id' => $karyawan->id,
                'nip' => $karyawan->nip,
                'nama' => $karyawan->nama,
                'login_time' => now(),
            ]
        ]);

        // Redirect ke intended URL atau dashboard
        $intended = session('url.intended', route('karyawan.dashboard'));
        session()->forget('url.intended');

        return redirect($intended)->with('success', 'Selamat datang, ' . $karyawan->nama . '!');
    }

    /**
     * Mengeluarkan pengguna dari aplikasi.
     */
    public function logout(Request $request): RedirectResponse
    {
        $karyawan = Auth::guard('karyawan')->user();

        // Update logout time jika perlu
        if ($karyawan) {
            $karyawan->update(['last_logout_at' => now()]);
        }

        // Logout dari guard karyawan
        Auth::guard('karyawan')->logout();

        // Hapus session data
        $request->session()->forget('karyawan.data');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('karyawan.login')
            ->with('success', 'Anda telah berhasil keluar dari sistem.');
    }

    /**
     * Dashboard karyawan.
     */
    public function dashboard(Request $request): RedirectResponse|View
    {
        // Cek apakah ini request AJAX atau direct access
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'redirect_url' => route('karyawan.absensi.index')
            ]);
        }

        // Dashboard sekarang langsung mengarahkan ke panel absensi
        // karena itu adalah fungsi utama bagi seorang karyawan
        return redirect()->route('karyawan.absensi.index');
    }
}