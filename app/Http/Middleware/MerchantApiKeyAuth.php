<?php

namespace App\Http\Middleware;

use App\Models\MerchantApiKey;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MerchantApiKeyAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $this->getTokenFromRequest($request);

        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Missing API key',
                'errors' => null,
            ], 401);
        }

        $tokenHash = hash('sha256', $token);

        $apiKey = MerchantApiKey::query()
            ->where('token_hash', $tokenHash)
            ->whereNull('revoked_at')
            ->with('user')
            ->first();

        if (!$apiKey || !$apiKey->user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid API key',
                'errors' => null,
            ], 401);
        }

        $apiKey->forceFill(['last_used_at' => now()])->save();

        Auth::setUser($apiKey->user);
        $request->attributes->set('merchant_api_key', $apiKey);

        return $next($request);
    }

    private function getTokenFromRequest(Request $request): ?string
    {
        $header = $request->header('Authorization');
        if (is_string($header) && str_starts_with($header, 'Bearer ')) {
            $bearer = trim(substr($header, 7));
            return $bearer !== '' ? $bearer : null;
        }

        $xApiKey = $request->header('X-API-KEY');
        if (is_string($xApiKey) && trim($xApiKey) !== '') {
            return trim($xApiKey);
        }

        $queryKey = $request->query('api_key');
        if (is_string($queryKey) && trim($queryKey) !== '') {
            return trim($queryKey);
        }

        return null;
    }
}

