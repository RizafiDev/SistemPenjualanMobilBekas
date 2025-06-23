<?php
// app/Exceptions/Handler.php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Convert an authentication exception into a response.
     */
    protected function unauthenticated($request, AuthenticationException $exception): Response
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Get the guards that failed authentication
        $guards = $exception->guards();

        // Determine redirect based on the guard
        if (in_array('karyawan', $guards) || $request->is('karyawan*')) {
            // Store intended URL for karyawan routes
            if ($request->route() && !$request->routeIs('*.login*')) {
                session(['url.intended' => $request->url()]);
            }

            return redirect()->guest(route('karyawan.login'))
                ->with('warning', 'Silakan login terlebih dahulu untuk mengakses halaman ini.');
        }

        // Default redirect for web guard
        return redirect()->guest(route('login'));
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e): Response
    {
        // Handle 404 errors
        if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            if ($request->is('karyawan*')) {
                return response()->view('errors.karyawan.404', [], 404);
            }
            return response()->view('errors.404', [], 404);
        }

        // Handle 403 errors
        if ($e instanceof \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException) {
            if ($request->is('karyawan*')) {
                return response()->view('errors.karyawan.403', [], 403);
            }
            return response()->view('errors.403', [], 403);
        }

        return parent::render($request, $e);
    }
}