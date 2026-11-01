<?php

namespace App\Services;

use App\Helpers\SettingHelper;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Udara360Service
{
    public function getBaseUrl(): string
    {
        $url = SettingHelper::get(SettingHelper::UDARA360_BASE_URL, config('services.udara360.base_url', env('UDARA360_BASE_URL', 'https://openapi.udara360.io')));
        return rtrim((string) $url, '/');
    }

    public function getClientId(): string
    {
        return (string) SettingHelper::get(SettingHelper::UDARA360_CLIENT_ID, config('services.udara360.client_id', env('UDARA360_CLIENT_ID', '')));
    }

    public function getClientSecret(): string
    {
        return (string) SettingHelper::get(SettingHelper::UDARA360_CLIENT_SECRET, config('services.udara360.client_secret', env('UDARA360_CLIENT_SECRET', '')));
    }

    public function getParentAccountNumber(): string
    {
        return (string) SettingHelper::get(SettingHelper::UDARA360_PARENT_ACCOUNT, config('services.udara360.parent_account', env('UDARA360_PARENT_ACCOUNT', '')));
    }

    public function getProductCode(): string
    {
        return (string) SettingHelper::get(SettingHelper::UDARA360_PRODUCT_CODE, config('services.udara360.product_code', env('UDARA360_PRODUCT_CODE', '301')));
    }

    public function getBankName(): string
    {
        return (string) SettingHelper::get(SettingHelper::UDARA360_BANK_NAME, config('services.udara360.bank_name', env('UDARA360_BANK_NAME', 'Providus Bank')));
    }

    public function getBankCode(): string
    {
        return (string) SettingHelper::get(SettingHelper::UDARA360_BANK_CODE, config('services.udara360.bank_code', env('UDARA360_BANK_CODE', '000023')));
    }

    /**
     * Check if Udara360 integration is enabled and configured.
     */
    public function isEnabled(): bool
    {
        $enabled = SettingHelper::get(SettingHelper::ENABLE_UDARA360, '1');
        return filter_var($enabled, FILTER_VALIDATE_BOOLEAN);
    }

    public function isConfigured(): bool
    {
        return $this->getBaseUrl() !== ''
            && $this->getClientId() !== ''
            && $this->getClientSecret() !== '';
    }

    /**
     * Get JWT access token from Udara360 API with caching.
     */
    public function getAccessToken(): string
    {
        $clientId = $this->getClientId();
        $clientSecret = $this->getClientSecret();
        $baseUrl = $this->getBaseUrl();

        if ($clientId === '' || $clientSecret === '') {
            throw new \Exception('Udara360 API credentials (Client ID or Client Secret) are not configured.');
        }

        $cacheKey = 'udara360_access_token_' . md5($clientId);

        $cachedToken = Cache::get($cacheKey);
        if (is_string($cachedToken) && $cachedToken !== '') {
            return $cachedToken;
        }

        $url = $baseUrl . '/api/auth/v1/authenticate';

        Log::info('Udara360 auth request', [
            'url' => $url,
            'client_id' => substr($clientId, 0, 6) . '...',
        ]);

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->timeout(20)->post($url, [
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
        ]);

        if (!$response->successful()) {
            $errorMsg = $response->json('message') 
                ?? $response->json('errors.0.errorMessage') 
                ?? ('HTTP ' . $response->status() . ': ' . $response->body());
            Log::error('Udara360 auth failed', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
            throw new \Exception('Udara360 authentication failed: ' . $errorMsg);
        }

        $json = $response->json();
        $token = $json['accessToken'] ?? $json['data']['accessToken'] ?? null;

        if (!is_string($token) || $token === '') {
            throw new \Exception('Udara360 did not return a valid accessToken.');
        }

        $validitySec = (int) ($json['tokenValiditySec'] ?? $json['data']['tokenValiditySec'] ?? 3600);
        $ttlSeconds = max(60, $validitySec - 120);

        Cache::put($cacheKey, $token, now()->addSeconds($ttlSeconds));

        return $token;
    }

    /**
     * Create a virtual account on Udara360.
     *
     * @param array $payload
     * @return array
     */
    public function createVirtualAccount(array $payload): array
    {
        $token = $this->getAccessToken();
        $baseUrl = $this->getBaseUrl();
        $url = $baseUrl . '/api/account/v1/createvirtualaccount';

        $reference = $payload['requestReference'] 
            ?? $payload['reference'] 
            ?? ('req_' . Str::random(24));
        unset($payload['requestReference'], $payload['reference']);

        // Truncate reference to <= 100 characters per Udara360 spec
        $requestRef = substr((string) $reference, 0, 100);

        Log::info('Udara360 create virtual account request', [
            'url' => $url,
            'request_reference' => $requestRef,
            'account_name' => $payload['accountName'] ?? null,
            'phone' => $payload['phoneNumber'] ?? null,
        ]);

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'bearer ' . $token,
            'request-reference' => $requestRef,
        ])->timeout(30)->post($url, $payload);

        $json = $response->json();

        Log::info('Udara360 create virtual account response', [
            'status' => $response->status(),
            'body' => $json,
        ]);

        if (!$response->successful()) {
            $errorMsg = $json['message'] 
                ?? $json['errors.0.errorMessage'] 
                ?? $json['errors'][0]['errorMessage'] 
                ?? ('HTTP ' . $response->status() . ': ' . $response->body());
            throw new \Exception('Udara360 virtual account creation failed: ' . $errorMsg);
        }

        return $json;
    }

    /**
     * Retrieve account info from Udara360 by account number.
     */
    public function getAccountByNumber(string $accountNumber): array
    {
        $token = $this->getAccessToken();
        $baseUrl = $this->getBaseUrl();
        $url = $baseUrl . '/api/account/v1/getbyaccountnumber';

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'bearer ' . $token,
            'request-reference' => 'query_' . Str::random(20),
        ])->timeout(20)->get($url, [
            'AccountNumber' => $accountNumber,
        ]);

        return $response->json() ?? [];
    }
}
