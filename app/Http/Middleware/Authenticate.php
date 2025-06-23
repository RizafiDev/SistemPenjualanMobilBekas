<?php
// app/Http/Middleware/Authenticate.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        // Store the intended URL before authentication check
        if (!$request->expectsJson() && $request->route() && !$request->routeIs('*.login*')) {
            session(['url.intended' => $request->url()]);
        }

        $this->authenticate($request, $guards);

        return $next($request);
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // Check if this is a karyawan route
        if ($request->is('karyawan*')) {
            return route('karyawan.login');
        }

        // Default redirect to main login
        return route('login');
    }

    /**
     * Determine if the user is logged in to any of the given guards.
     */
    protected function authenticate($request, array $guards)
    {
        if (empty($guards)) {
            $guards = [null];
        }

        foreach ($guards as $guard) {
            if ($this->auth->guard($guard)->check()) {
                $user = $this->auth->guard($guard)->user();

                // Additional check for karyawan guard
                if ($guard === 'karyawan' && $user) {
                    // Check if karyawan is still active
                    if (isset($user->aktif) && $user->aktif !== 'aktif') {
                        $this->auth->guard($guard)->logout();
                        $request->session()->invalidate();
                        $request->session()->regenerateToken();

                        if ($request->expectsJson()) {
                            throw new \Illuminate\Auth\AuthenticationException(
                                'Akun Anda telah dinonaktifkan.',
                                [$guard],
                                $this->redirectTo($request)
                            );
                        }

                        return redirect()->route('karyawan.login')
                            ->withErrors(['error' => 'Akun Anda telah dinonaktifkan. Hubungi administrator.']);
                    }
                }

                return $this->auth->shouldUse($guard);
            }
        }

        $this->unauthenticated($request, $guards);
    }
}