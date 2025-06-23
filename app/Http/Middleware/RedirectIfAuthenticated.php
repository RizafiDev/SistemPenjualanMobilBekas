<?php
// app/Http/Middleware/RedirectIfAuthenticated.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Handle redirect berdasarkan guard yang digunakan
                return $this->redirectBasedOnGuard($guard, $request);
            }
        }

        return $next($request);
    }

    /**
     * Redirect user berdasarkan guard yang authenticated
     */
    private function redirectBasedOnGuard(?string $guard, Request $request): Response
    {
        switch ($guard) {
            case 'karyawan':
                // Cek apakah ada intended URL yang valid
                $intended = session('url.intended');
                if ($intended && $this->isValidKaryawanRoute($intended)) {
                    session()->forget('url.intended');
                    return redirect($intended);
                }
                return redirect()->route('karyawan.dashboard');

            case 'web':
            case null:
            default:
                // Default redirect untuk guard 'web' atau null
                $intended = session('url.intended');
                if ($intended && !str_contains($intended, '/karyawan/')) {
                    session()->forget('url.intended');
                    return redirect($intended);
                }
                return redirect('/dashboard');
        }
    }

    /**
     * Cek apakah URL intended adalah route karyawan yang valid
     */
    private function isValidKaryawanRoute(string $url): bool
    {
        return str_contains($url, '/karyawan/') &&
            !str_contains($url, '/karyawan/login');
    }
}