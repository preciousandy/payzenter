<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaystackService
{
    protected string $secretKey;
    protected string $publicKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->secretKey = config('paystack.secret_key') ?? env('PAYSTACK_SECRET_KEY', '');
        $this->publicKey = config('paystack.public_key') ?? env('PAYSTACK_PUBLIC_KEY', '');
        $this->baseUrl = rtrim(config('paystack.payment_url') ?? 'https://api.paystack.co', '/');
    }

    /**
     * Get HTTP Client with Authorization header.
     */
    protected function client()
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ]);
    }

    /**
     * Initialize a Paystack transaction restricted to Card payment only.
     */
    public function initializeTransaction(float $amountNgn, string $email, string $reference, string $callbackUrl, array $metadata = []): array
    {
        $amountKobo = (int) round($amountNgn * 100);

        $payload = [
            'amount'       => $amountKobo,
            'email'        => $email,
            'currency'     => 'NGN',
            'reference'    => $reference,
            'callback_url' => $callbackUrl,
            'metadata'     => $metadata,
        ];

        Log::info('Paystack initializeTransaction request', [
            'email' => $email,
            'reference' => $reference,
            'amount_ngn' => $amountNgn,
            'amount_kobo' => $amountKobo,
        ]);

        try {
            $response = $this->client()->post("{$this->baseUrl}/transaction/initialize", $payload);
            $resData = $response->json() ?? ['status' => false, 'message' => 'Invalid response from Paystack API'];
            Log::info('Paystack initializeTransaction response', [
                'reference' => $reference,
                'status' => $resData['status'] ?? false,
                'message' => $resData['message'] ?? null,
            ]);
            return $resData;
        } catch (\Throwable $e) {
            Log::error('Paystack initializeTransaction error: ' . $e->getMessage(), [
                'reference' => $reference,
            ]);
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Charge a card directly using Paystack Charge API.
     */
    public function chargeCard(string $email, float $amountNgn, array $card, string $reference, array $metadata = []): array
    {
        $amountKobo = (int) round($amountNgn * 100);

        $payload = [
            'email'     => $email,
            'amount'    => $amountKobo,
            'currency'  => 'NGN',
            'reference' => $reference,
            'card'      => [
                'number'       => str_replace([' ', '-'], '', $card['number'] ?? ''),
                'cvv'          => trim($card['cvv'] ?? ''),
                'expiry_month' => str_pad(trim($card['expiry_month'] ?? ''), 2, '0', STR_PAD_LEFT),
                'expiry_year'  => trim($card['expiry_year'] ?? ''),
            ],
            'metadata'  => $metadata,
        ];

        if (!empty($card['pin'])) {
            $payload['pin'] = trim($card['pin']);
            $payload['card']['pin'] = trim($card['pin']);
        }

        Log::info('Paystack chargeCard request', [
            'email' => $email,
            'reference' => $reference,
            'amount_ngn' => $amountNgn,
            'card_last4' => substr($card['number'] ?? '', -4),
        ]);

        try {
            $response = $this->client()->post("{$this->baseUrl}/charge", $payload);
            $resData = $response->json() ?? ['status' => false, 'message' => 'No response from Paystack API'];
            Log::info('Paystack chargeCard response', [
                'reference' => $reference,
                'status' => $resData['status'] ?? false,
                'data_status' => $resData['data']['status'] ?? null,
                'message' => $resData['message'] ?? null,
            ]);
            return $resData;
        } catch (\Throwable $e) {
            Log::error('Paystack chargeCard error: ' . $e->getMessage(), [
                'reference' => $reference,
            ]);
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /** 
     * Submit OTP for pending Paystack charge.
     */
    public function submitOtp(string $otp, string $reference): array
    {
        Log::info('Paystack submitOtp request', ['reference' => $reference]);
        try {
            $response = $this->client()->post("{$this->baseUrl}/charge/submit_otp", [
                'otp'       => trim($otp),
                'reference' => $reference,
            ]);
            $resData = $response->json() ?? ['status' => false, 'message' => 'No response from Paystack API'];
            Log::info('Paystack submitOtp response', [
                'reference' => $reference,
                'status' => $resData['status'] ?? false,
                'data_status' => $resData['data']['status'] ?? null,
            ]);
            return $resData;
        } catch (\Throwable $e) {
            Log::error('Paystack submitOtp error: ' . $e->getMessage(), ['reference' => $reference]);
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Submit PIN for pending Paystack charge.
     */
    public function submitPin(string $pin, string $reference): array
    {
        Log::info('Paystack submitPin request', ['reference' => $reference]);
        try {
            $response = $this->client()->post("{$this->baseUrl}/charge/submit_pin", [
                'pin'       => trim($pin),
                'reference' => $reference,
            ]);
            $resData = $response->json() ?? ['status' => false, 'message' => 'No response from Paystack API'];
            Log::info('Paystack submitPin response', [
                'reference' => $reference,
                'status' => $resData['status'] ?? false,
                'data_status' => $resData['data']['status'] ?? null,
            ]);
            return $resData;
        } catch (\Throwable $e) {
            Log::error('Paystack submitPin error: ' . $e->getMessage(), ['reference' => $reference]);
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Verify Paystack transaction by reference.
     */
    public function verifyTransaction(string $reference): array
    {
        Log::info('Paystack verifyTransaction request', ['reference' => $reference]);
        try {
            $response = $this->client()->get("{$this->baseUrl}/transaction/verify/" . rawurlencode($reference));
            $resData = $response->json() ?? ['status' => false, 'message' => 'Unable to verify transaction'];
            Log::info('Paystack verifyTransaction response', [
                'reference' => $reference,
                'status' => $resData['status'] ?? false,
                'data_status' => $resData['data']['status'] ?? null,
            ]);
            return $resData;
        } catch (\Throwable $e) {
            Log::error('Paystack verifyTransaction error: ' . $e->getMessage(), ['reference' => $reference]);
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }
}
