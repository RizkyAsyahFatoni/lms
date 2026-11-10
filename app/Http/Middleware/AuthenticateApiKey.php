<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiKey
{
    /**
     * Handle an incoming request for external ASN Integrations.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken() 
            ?: $request->header('X-API-Key') 
            ?: $request->query('api_key');

        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized: Token API tidak ditemukan. Sertakan header [Authorization: Bearer <TOKEN>] atau [X-API-Key: <TOKEN>].',
                'documentation' => url('/admin/api-integrations'),
            ], 401);
        }

        $apiKey = ApiKey::where('token', $token)
            ->where('is_active', true)
            ->first();

        if (!$apiKey) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized: Token API tidak sah atau telah dicabut.',
            ], 401);
        }

        // Check expiration if set
        if ($apiKey->expires_at && $apiKey->expires_at->isPast()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized: Token API telah kedaluwarsa.',
            ], 401);
        }

        // Update last used timestamp
        $apiKey->update(['last_used_at' => now()]);

        // Attach the ApiKey to the request attributes for controllers if needed
        $request->attributes->set('api_key', $apiKey);

        return $next($request);
    }
}
