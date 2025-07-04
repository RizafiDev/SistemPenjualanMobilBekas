<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ApiKey;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $resource = null): Response
    {
        $apiKey = $this->getApiKeyFromRequest($request);

        if (!$apiKey) {
            return response()->json([
                'error' => 'API key is required',
                'message' => 'Please provide a valid API key in the Authorization header or x-api-key header'
            ], 401);
        }

        $apiKeyModel = ApiKey::verify($apiKey);

        if (!$apiKeyModel) {
            return response()->json([
                'error' => 'Invalid API key',
                'message' => 'The provided API key is invalid or expired'
            ], 401);
        }

        if (!$apiKeyModel->isValid()) {
            return response()->json([
                'error' => 'API key inactive or expired',
                'message' => 'The API key is no longer active or has expired'
            ], 401);
        }

        // Check resource-specific permissions if resource is specified
        if ($resource && !$apiKeyModel->hasResourceAccess($resource)) {
            return response()->json([
                'error' => 'Insufficient permissions',
                'message' => "Access denied for resource: {$resource}"
            ], 403);
        }

        // Add API key info to request for logging or other purposes
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
