<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PalmPayService
{
    public function createPayout(array $payload): array
    {
        $baseUrl = rtrim((string) env('PALMPAY_BASE_URL', ''), '/');
        if ($baseUrl === '') {
            throw new \Exception('PalmPay base URL not configured in .env (PALMPAY_BASE_URL)');
        }

        $appId = (string) env('PALMPAY_APP_ID', '');
        if ($appId === '') {
            throw new \Exception('PalmPay App ID not configured in .env (PALMPAY_APP_ID)');
        }

        $body = array_merge([
            'requestTime' => (int) floor(microtime(true) * 1000),
            'version' => 'V1.1',
            'nonceStr' => Str::random(32),
        ], $payload);

        $signature = $this->signBody($body);
        $this->logRequestOrigin('createPayout', $baseUrl . '/api/v2/merchant/payment/payout');

        Log::info('PalmPay createPayout request', [
            'url' => $baseUrl . '/api/v2/merchant/payment/payout',
            'payload_keys' => array_keys($body),
            'orderId' => $body['orderId'] ?? null,
            'payeeBankCode' => $body['payeeBankCode'] ?? null,
            'amount' => $body['amount'] ?? null,
        ]);

        $res = Http::withHeaders([
            'Accept' => 'application/json, text/plain, */*',
            'Content-Type' => 'application/json',
            'CountryCode' => (string) env('PALMPAY_COUNTRY_CODE', 'NG'),
            'Authorization' => 'Bearer ' . $appId,
            'Signature' => $signature,
        ])->post($baseUrl . '/api/v2/merchant/payment/payout', $body);

        $json = $res->json();

        Log::info('PalmPay createPayout response', [
            'status' => $res->status(),
            'successful' => $res->successful(),
            'json' => $json,
        ]);

        if (!$res->successful()) {
            throw new \Exception($json['respMsg'] ?? ('HTTP ' . $res->status() . ': PalmPay payout request failed'));
        }

        if (($json['respCode'] ?? null) !== '00000000' && ($json['respCode'] ?? null) !== '00') {
            $msg = $json['respMsg'] ?? ($json['data']['errorMsg'] ?? ($json['message'] ?? 'PalmPay payout error'));
            throw new \Exception('PalmPay error (' . ($json['respCode'] ?? 'unknown') . '): ' . $msg);
        }

        return $json;
    }

    public function createOrder(array $payload): array
    {
        $baseUrl = rtrim((string) env('PALMPAY_BASE_URL', ''), '/');
        if ($baseUrl === '') {
            throw new \Exception('PalmPay base URL not configured');
        }

        $appId = (string) env('PALMPAY_APP_ID', '');
        if ($appId === '') {
            throw new \Exception('PalmPay app id not configured');
        }

        $body = array_merge([
            'requestTime' => (int) floor(microtime(true) * 1000),
            'version' => 'V1.1',
            'nonceStr' => Str::random(32),
        ], $payload);

        $signature = $this->signBody($body);
        $this->logRequestOrigin('createOrder', $baseUrl . '/api/v2/payment/merchant/createorder');

        Log::info('PalmPay createOrder request', [
            'url' => $baseUrl . '/api/v2/payment/merchant/createorder',
            'body_keys' => array_keys($body),
            'body' => $body,
        ]);

        $res = Http::withHeaders([
            'Accept' => 'application/json, text/plain, */*',
            'Content-Type' => 'application/json',
            'CountryCode' => (string) env('PALMPAY_COUNTRY_CODE', 'NG'),
            'Authorization' => 'Bearer ' . $appId,
            'Signature' => $signature,
        ])->post($baseUrl . '/api/v2/payment/merchant/createorder', $body);

        $json = $res->json();

        Log::info('PalmPay createOrder response', [
            'status' => $res->status(),
            'successful' => $res->successful(),
            'json' => $json,
        ]);

        if (!$res->successful()) {
            throw new \Exception($json['respMsg'] ?? ('HTTP ' . $res->status() . ': PalmPay request failed'));
        }

        if (($json['respCode'] ?? null) !== '00000000' && ($json['respCode'] ?? null) !== '00') {
            throw new \Exception($json['respMsg'] ?? ('PalmPay error (respCode=' . ($json['respCode'] ?? 'unknown') . ')'));
        }

        return $json;
    }

    public function createVirtualAccount(array $payload): array
    {
        $baseUrl = rtrim((string) env('PALMPAY_BASE_URL', ''), '/');
        if ($baseUrl === '') {
            throw new \Exception('PalmPay base URL not configured');
        }

        $appId = (string) env('PALMPAY_APP_ID', '');
        if ($appId === '') {
            throw new \Exception('PalmPay app id not configured');
        }

        $body = array_merge([
            'requestTime' => (int) floor(microtime(true) * 1000),
            'version' => 'V2.0',
            'nonceStr' => Str::random(32),
        ], $payload);

        $signature = $this->signBody($body);
        $this->logRequestOrigin('createVirtualAccount', $baseUrl . '/api/v2/virtual/account/label/create');

        Log::info('PalmPay VA request', [
            'url' => $baseUrl . '/api/v2/virtual/account/label/create',
            'body_keys' => array_keys($body),
            'body' => $body,
        ]);

        $res = Http::withHeaders([
            'Accept' => 'application/json, text/plain, */*',
            'Content-Type' => 'application/json',
            'CountryCode' => (string) env('PALMPAY_COUNTRY_CODE', 'NG'),
            'Authorization' => 'Bearer ' . $appId,
            'Signature' => $signature,
        ])->post($baseUrl . '/api/v2/virtual/account/label/create', $body);

        $json = $res->json();

        Log::info('PalmPay VA response', [
            'status' => $res->status(),
            'successful' => $res->successful(),
            'json' => $json,
        ]);

        if (!$res->successful()) {
            throw new \Exception($json['respMsg'] ?? ('HTTP ' . $res->status() . ': PalmPay virtual account request failed'));
        }

        if (($json['respCode'] ?? null) !== '00000000' && ($json['respCode'] ?? null) !== '00') {
            throw new \Exception($json['respMsg'] ?? ('PalmPay error (respCode=' . ($json['respCode'] ?? 'unknown') . ')'));
        }

        return $json;
    }

    public function queryPayoutStatus(array $payload): array
    {
        $baseUrl = rtrim((string) env('PALMPAY_BASE_URL', ''), '/');
        if ($baseUrl === '') {
            throw new \Exception('PalmPay base URL not configured');
        }

        $appId = (string) env('PALMPAY_APP_ID', '');
        if ($appId === '') {
            throw new \Exception('PalmPay app id not configured');
        }

        $body = array_merge([
            'requestTime' => (int) floor(microtime(true) * 1000),
            'version' => 'V1.1',
            'nonceStr' => Str::random(32),
        ], $payload);

        $signature = $this->signBody($body);
        $this->logRequestOrigin('queryPayoutStatus', $baseUrl . '/api/v2/merchant/payment/queryPayStatus');

        $res = Http::withHeaders([
            'Accept' => 'application/json, text/plain, */*',
            'Content-Type' => 'application/json',
            'CountryCode' => (string) env('PALMPAY_COUNTRY_CODE', 'NG'),
            'Authorization' => 'Bearer ' . $appId,
            'Signature' => $signature,
        ])->post($baseUrl . '/api/v2/merchant/payment/queryPayStatus', $body);

        $json = $res->json();

        if (!$res->successful()) {
            throw new \Exception($json['respMsg'] ?? 'PalmPay payout status request failed');
        }

        if (($json['respCode'] ?? null) !== '00000000') {
            throw new \Exception($json['respMsg'] ?? ($json['data']['errorMsg'] ?? 'PalmPay payout status error'));
        }

        return $json;
    }

    public function queryOrderStatus(array $payload): array
    {
        $baseUrl = rtrim((string) env('PALMPAY_BASE_URL', ''), '/');
        if ($baseUrl === '') {
            throw new \Exception('PalmPay base URL not configured');
        }

        $appId = (string) env('PALMPAY_APP_ID', '');
        if ($appId === '') {
            throw new \Exception('PalmPay app id not configured');
        }

        $body = array_merge([
            'requestTime' => (int) floor(microtime(true) * 1000),
            'version' => 'V1.1',
            'nonceStr' => Str::random(32),
        ], $payload);

        $signature = $this->signBody($body);
        $this->logRequestOrigin('queryOrderStatus', $baseUrl . '/api/v2/payment/merchant/order/queryStatus');

        $res = Http::withHeaders([
            'Accept' => 'application/json, text/plain, */*',
            'Content-Type' => 'application/json',
            'CountryCode' => (string) env('PALMPAY_COUNTRY_CODE', 'NG'),
            'Authorization' => 'Bearer ' . $appId,
            'Signature' => $signature,
        ])->post($baseUrl . '/api/v2/payment/merchant/order/queryStatus', $body);

        $json = $res->json();

        if (!$res->successful()) {
            throw new \Exception($json['respMsg'] ?? 'PalmPay request failed');
        }

        if (($json['respCode'] ?? null) !== '00000000') {
            throw new \Exception($json['respMsg'] ?? 'PalmPay error');
        }

        return $json;
    }

    private function getSigningString(array $params): string
    {
        $filtered = [];
        foreach ($params as $key => $value) {
            if ($value === null) {
                continue;
            }

            if (is_string($value)) {
                $trimmed = trim($value);
                if ($trimmed === '') {
                    continue;
                }
                $filtered[$key] = $trimmed;
                continue;
            }

            if (is_bool($value)) {
                $filtered[$key] = $value ? 'true' : 'false';
                continue;
            }

            if (is_array($value)) {
                if ($value === []) {
                    continue;
                }
                $filtered[$key] = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                continue;
            }

            $filtered[$key] = $value;
        }

        ksort($filtered, SORT_STRING);

        $pairs = [];
        foreach ($filtered as $key => $value) {
            $pairs[] = $key . '=' . $value;
        }

        return implode('&', $pairs);
    }

    private function md5DigestString(array $params): string
    {
        return strtoupper(md5($this->getSigningString($params)));
    }

    public function verifyWebhook(array $params, string $signature): bool
    {
        $publicKey = (string) env('PALMPAY_PLATFORM_PUBLIC_KEY', '');
        if ($publicKey === '') {
            return false;
        }

        try {
            $normalizedSignature = urldecode($signature);
            $md5Str = $this->md5DigestString($params);

            $pem = $this->normalizePublicKeyToPem($publicKey);
            $publicKeyResource = @openssl_pkey_get_public($pem);
            if ($publicKeyResource === false) {
                Log::warning('PalmPay verifyWebhook: OpenSSL could not parse PALMPAY_PLATFORM_PUBLIC_KEY');
                return false;
            }

            $decoded = base64_decode($normalizedSignature, true);
            if ($decoded === false) {
                return false;
            }

            $result = @openssl_verify($md5Str, $decoded, $publicKeyResource, OPENSSL_ALGO_SHA1);
            return $result === 1;
        } catch (\Throwable $th) {
            Log::warning('PalmPay verifyWebhook exception: ' . $th->getMessage());
            return false;
        }
    }

    private function signBody(array $params): string
    {
        $privateKey = (string) env('PALMPAY_MERCHANT_PRIVATE_KEY_BASE64', '');
        if ($privateKey === '') {
            throw new \Exception('PalmPay merchant private key not configured');
        }

        $md5Str = $this->md5DigestString($params);

        $privateKeyResource = openssl_pkey_get_private($this->normalizePrivateKeyToPem($privateKey));
        if ($privateKeyResource === false) {
            throw new \Exception('Invalid PalmPay merchant private key');
        }

        $signature = '';
        $ok = openssl_sign($md5Str, $signature, $privateKeyResource, OPENSSL_ALGO_SHA1);
        if (!$ok) {
            throw new \Exception('Failed to sign PalmPay request');
        }

        return base64_encode($signature);
    }

    private function logRequestOrigin(string $action, string $url): void
    {
        try {
            $publicIp = null;

            $ipifyResponse = Http::timeout(10)->acceptJson()->get('https://api.ipify.org', [
                'format' => 'json',
            ]);

            if ($ipifyResponse->successful()) {
                $publicIp = $ipifyResponse->json('ip');
            }

            Log::info('PalmPay outbound request debug', [
                'action' => $action,
                'url' => $url,
                'public_ip' => $publicIp,
                'app_url' => config('app.url'),
                'host' => gethostname(),
            ]);
        } catch (\Throwable $th) {
            Log::warning('PalmPay outbound request debug failed', [
                'action' => $action,
                'url' => $url,
                'message' => $th->getMessage(),
                'host' => gethostname(),
            ]);
        }
    }

    private function normalizePrivateKeyToPem(string $key): string
    {
        $trimmed = trim($key);
        if (str_contains($trimmed, 'BEGIN')) {
            return $trimmed;
        }

        $body = preg_replace('/\s+/', '', $trimmed);
        $mod = strlen($body) % 4;
        if ($mod !== 0) {
            $body .= str_repeat('=', 4 - $mod);
        }

        return "-----BEGIN PRIVATE KEY-----\n" .
            wordwrap($body, 64, "\n", true) .
            "\n-----END PRIVATE KEY-----\n";
    }

    private function normalizePublicKeyToPem(string $key): string
    {
        $trimmed = trim($key);
        if (str_contains($trimmed, 'BEGIN')) {
            return $trimmed;
        }

        $body = preg_replace('/\s+/', '', $trimmed);
        $mod = strlen($body) % 4;
        if ($mod !== 0) {
            $body .= str_repeat('=', 4 - $mod);
        }

        $formatted = wordwrap($body, 64, "\n", true);

        $pem1 = "-----BEGIN PUBLIC KEY-----\n" . $formatted . "\n-----END PUBLIC KEY-----\n";
        if (@openssl_pkey_get_public($pem1) !== false) {
            return $pem1;
        }

        $pem2 = "-----BEGIN RSA PUBLIC KEY-----\n" . $formatted . "\n-----END RSA PUBLIC KEY-----\n";
        if (@openssl_pkey_get_public($pem2) !== false) {
            return $pem2;
        }

        return $pem1;
    }

    public static function mapToPalmPayBankCode(string $code): string
    {
        $clean = trim($code);
        $clean3 = str_pad(ltrim($clean, '0'), 3, '0', STR_PAD_LEFT);

        $map = [
            '044' => '000005', // Access Bank
            '058' => '000013', // GTBank
            '011' => '000016', // First Bank of Nigeria
            '057' => '000015', // Zenith Bank
            '033' => '000004', // United Bank for Africa (UBA)
            '032' => '000001', // Union Bank
            '035' => '000002', // Wema Bank
            '050' => '000007', // Ecobank
            '070' => '000008', // Fidelity Bank
            '014' => '000009', // Mainstreet / Polaris
            '076' => '000010', // Sterling Bank
            '221' => '000011', // Stanbic IBTC
            '068' => '000012', // Standard Chartered
            '215' => '000014', // Unity Bank
            '214' => '000003', // First City Monument Bank (FCMB)
            '030' => '000018', // Heritage Bank
            '082' => '000019', // Keystone Bank
            '101' => '000020', // Providus Bank
            '100' => '000021', // SunTrust Bank
            '102' => '000022', // Titan Trust Bank
            '103' => '000023', // Globus Bank
            '090267' => '100025', // Kuda Bank
            '090551' => '100026', // Moniepoint MFB
            '999032' => '100004', // OPay
            '100004' => '100004', // OPay
            '100033' => '100033', // PalmPay
        ];

        if (isset($map[$clean])) {
            return $map[$clean];
        }

        if (isset($map[$clean3])) {
            return $map[$clean3];
        }

        return $clean;
    }
}
