<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ApiKey;

class FilamentApiKeyMiddleware
{
    /**
     * Handle an incoming request for Filament API Service.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Debug log
        \Log::info('FilamentApiKeyMiddleware: Request URI = ' . $request->getRequestUri());

        // Skip authentication for non-API routes
        if (!$request->is('api/*')) {
            \Log::info('FilamentApiKeyMiddleware: Skipping non-API route');
            return $next($request);
        }

        \Log::info('FilamentApiKeyMiddleware: Processing API route');

        // Always require API key for API routes
        $apiKey = $this->getApiKeyFromRequest($request);

        if (!$apiKey) {
            \Log::warning('FilamentApiKeyMiddleware: No API key provided');
            return response()->json([
                'success' => false,
                'message' => 'API key is required. Please provide a valid API key in the Authorization header (Bearer token) or X-API-Key header.'
            ], 401);
        }

        $apiKeyModel = ApiKey::verify($apiKey);

        if (!$apiKeyModel) {
            \Log::warning('FilamentApiKeyMiddleware: Invalid API key');
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired API key.'
            ], 401);
        }

        if (!$apiKeyModel->isValid()) {
            \Log::warning('FilamentApiKeyMiddleware: API key inactive or expired');
            return response()->json([
                'success' => false,
                'message' => 'API key is inactive or expired.'
            ], 401);
        }

        \Log::info('FilamentApiKeyMiddleware: API key valid, proceeding');

        // Add API key info to request
        $request->merge(['api_key_id' => $apiKeyModel->id]);

        return $next($request);
    }

    /**
     * Extract API key from request
     */
    private function getApiKeyFromRequest(Request $request): ?string
    {
        // Check Authorization header (Bearer token)
        $authHeader = $request->header('Authorization');
        if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
            return substr($authHeader, 7);
        }

        // Check X-API-Key header
        $apiKeyHeader = $request->header('X-API-Key');
        if ($apiKeyHeader) {
            return $apiKeyHeader;
        }

        // Check query parameter (less secure, but sometimes needed)
        return $request->query('api_key');
    }
}
