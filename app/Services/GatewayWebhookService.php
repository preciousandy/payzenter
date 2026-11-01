<?php

namespace App\Services;

use App\Models\GatewayTransaction;
use App\Models\GatewayWebhookDelivery;
use App\Models\User;
use App\Models\VirtualAccount;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GatewayWebhookService
{
    public function dispatch(GatewayTransaction $tx, ?string $event = null): ?GatewayWebhookDelivery
    {
        $event = $event ?: 'payment.success';
        $merchant = $tx->relationLoaded('merchant') ? $tx->merchant : $tx->merchant()->first();
        $url = $this->resolveWebhookUrl(
            $tx->merchant_webhook_url,
            $merchant?->merchant_webhook_url
        );

        if ($url === '') {
            return null;
        }

        $payload = $this->buildTransactionPayload($tx, $event);

        return $this->storeAndAttempt([
            'gateway_transaction_id' => $tx->id,
            'merchant_id' => $tx->merchant_id,
            'reference' => $tx->reference,
            'url' => $url,
            'event' => $event,
            'payload' => $payload,
            'attempts' => 0,
        ]);
    }

    public function attempt(GatewayWebhookDelivery $delivery): GatewayWebhookDelivery
    {
        $delivery->forceFill([
            'attempts' => (int) $delivery->attempts + 1,
            'last_attempt_at' => now(),
            'error_message' => null,
        ])->save();

        try {
            $payloadArray = is_array($delivery->payload) ? $delivery->payload : json_decode($delivery->payload, true);
            $jsonPayload = json_encode($payloadArray ?: [], JSON_UNESCAPED_SLASHES);

            $secretKey = $this->resolveSecretKey($delivery->merchant_id);
            $signature = hash_hmac('sha256', $jsonPayload, $secretKey);

            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'User-Agent' => 'Payzenter-Webhook/1.0',
                'X-Payzenter-Signature' => $signature,
                'Payzenter-Signature' => $signature,
                'X-Internal-Signature' => $signature,
                'Paymentpoint-Signature' => $signature,
            ];

            $res = Http::timeout(30)
                ->withHeaders($headers)
                ->withBody($jsonPayload, 'application/json')
                ->post($delivery->url);

            $status = $res->status();
            $body = $res->body();

            // Evaluate delivery success: even on HTTP 200, check if the recipient reported an application-level rejection
            $isDelivered = ($status >= 200 && $status < 300);
            $errorMessage = null;

            $bodyLower = strtolower($body);
            $rejectionIndicators = [
                'account not found',
                'user not found',
                'virtual account not found',
                'transaction not successful',
                'invalid signature',
                'invalid amount',
                'failed to forward',
                'transaction failed',
                '"status":false',
                '"status":"error"',
                '"status":"failed"',
            ];

            foreach ($rejectionIndicators as $indicator) {
                if (strpos($bodyLower, $indicator) !== false) {
                    $isDelivered = false;
                    $errorMessage = "Target returned HTTP {$status} but reported: " . Str::limit(trim(strip_tags($body)), 180);
                    break;
                }
            }

            $delivery->forceFill([
                'response_status' => $status,
                'response_body' => $body,
                'error_message' => $errorMessage,
                'delivered_at' => $isDelivered ? now() : null,
            ])->save();
        } catch (\Throwable $e) {
            $delivery->forceFill([
                'response_status' => null,
                'response_body' => null,
                'error_message' => mb_substr($e->getMessage(), 0, 500),
                'delivered_at' => null,
            ])->save();
        }

        return $delivery->fresh();
    }

    public function dispatchVirtualAccountFunding(VirtualAccount $virtualAccount, array $data, ?string $event = null): ?GatewayWebhookDelivery
    {
        $merchant = $virtualAccount->relationLoaded('user') ? $virtualAccount->user : $virtualAccount->user()->first();
        if (!$merchant instanceof User) {
            return null;
        }

        $url = $this->resolveWebhookUrl($merchant->merchant_webhook_url);
        if ($url === '') {
            return null;
        }

        $event = $event ?: 'virtual_account.funding.success';
        $reference = $this->extractFundingReference($data);
        $payload = [
            'event' => $event,
            'type' => 'virtual_account_funding',
            'reference' => $reference,
            'status' => 'success',
            'amount' => $data['amount'] ?? null,
            'currency' => $data['currency'] ?? 'NGN',
            'funded_at' => now()->toISOString(),
            'provider' => $virtualAccount->provider,
            'provider_reference' => $data['sessionId'] ?? $data['reference'] ?? null,
            'virtual_account' => [
                'account_number' => $virtualAccount->account_number,
                'account_name' => $virtualAccount->account_name,
                'bank_name' => $virtualAccount->bank_name,
                'bank_code' => $virtualAccount->bank_code,
                'customer_phone' => $virtualAccount->customer_phone,
                'customer_email' => $virtualAccount->customer_email,
                'merchant_ref' => $virtualAccount->merchant_ref,
                'account_reference' => $virtualAccount->account_reference,
            ],
            'source' => [
                'account_name' => $data['sourceAccountName'] ?? null,
                'account_number' => $data['sourceAccountNumber'] ?? null,
                'bank_name' => $data['sourceBankName'] ?? null,
            ],
        ];

        return $this->storeAndAttempt([
            'gateway_transaction_id' => null,
            'merchant_id' => $merchant->id,
            'reference' => $reference,
            'url' => $url,
            'event' => $event,
            'payload' => $payload,
            'attempts' => 0,
        ]);
    }

    public function notifyMerchant(GatewayTransaction $tx, ?string $event = null): ?GatewayWebhookDelivery
    {
        return $this->dispatch($tx, $event);
    }

    public function buildTransactionPayload(GatewayTransaction $tx, ?string $event = null, array $customOverrides = []): array
    {
        $event = $event ?: ($tx->status === 'success' ? 'payment.success' : 'payment.' . ($tx->status ?: 'updated'));
        $merchant = $tx->relationLoaded('merchant') ? $tx->merchant : $tx->merchant()->first();

        // 1. Resolve Account Number
        $accountNo = $customOverrides['account_number'] ?? null;
        if (empty($accountNo)) {
            $accountNo = $tx->bank_transfer_account;
        }
        if (empty($accountNo) && !empty($tx->raw_provider_webhook)) {
            $raw = is_array($tx->raw_provider_webhook) ? $tx->raw_provider_webhook : json_decode($tx->raw_provider_webhook, true);
            $accountNo = data_get($raw, 'AccountNumber')
                ?? data_get($raw, 'recipientAccountNumber')
                ?? data_get($raw, 'account_number')
                ?? data_get($raw, 'virtual_account.account_number')
                ?? data_get($raw, 'data.account_number');
        }

        // Lookup matching virtual account in Payzenter DB if available
        $va = null;
        if (!empty($accountNo)) {
            $va = VirtualAccount::where('account_number', $accountNo)->first();
        }
        if (!$va && $merchant) {
            $va = VirtualAccount::where('user_id', $merchant->id)->first();
        }
        if (!$va && !empty($tx->customer_email)) {
            $va = VirtualAccount::where('customer_email', $tx->customer_email)->first();
        }
        if (!$va && !empty($tx->customer_phone)) {
            $va = VirtualAccount::where('customer_phone', $tx->customer_phone)->first();
        }

        if (empty($accountNo) && $va) {
            $accountNo = $va->account_number;
        }

        // 2. Resolve Bank and Customer details
        $bankName = $customOverrides['bank_name'] ?? ($tx->bank_transfer_bank ?: ($va?->bank_name ?? 'PalmPay'));
        $bankCode = $va?->bank_code ?? '20946';

        $customerName = $customOverrides['customer_name'] ?? null;
        if (empty($customerName)) {
            $customerName = $tx->customer_name ?: ($va?->account_name ?: ($merchant ? trim($merchant->first_name . ' ' . $merchant->last_name) : 'Customer'));
        }

        $customerEmail = $customOverrides['customer_email'] ?? null;
        if (empty($customerEmail)) {
            $customerEmail = $tx->customer_email ?: ($va?->customer_email ?: ($merchant?->email ?? ''));
        }

        $customerPhone = $customOverrides['customer_phone'] ?? null;
        if (empty($customerPhone)) {
            $customerPhone = $tx->customer_phone ?: ($va?->customer_phone ?: ($merchant?->phone ?? ''));
        }

        // 3. Resolve Amounts (Minor units kobo + Major units Naira)
        $amountKobo = isset($customOverrides['amount']) && is_numeric($customOverrides['amount'])
            ? (int) round(((float) $customOverrides['amount']) * 100)
            : (int) ($tx->amount ?? 0);

        $amountNaira = ((float) $amountKobo) / 100;
        $feeNaira = ((float) ($tx->fee ?? 0)) / 100;
        $chargedNaira = ((float) ($tx->charged_amount ?? $tx->amount ?? 0)) / 100;

        $accountRef = $va?->account_reference ?? ($tx->provider_reference ?: $tx->reference);
        $merchantRef = $va?->merchant_ref ?? $accountNo;

        // 4. Construct Full Universal Payload
        $payload = [
            'event' => $event,
            'type' => !empty($accountNo) ? 'virtual_account_funding' : ($tx->method === 'card' ? 'card_payment' : 'payment'),
            'reference' => $tx->reference,
            'transaction_id' => $tx->reference,
            'transactionOrderNo' => $tx->reference,
            'status' => $tx->status ?: 'success',
            'transaction_status' => $tx->status ?: 'success',
            'notification_status' => ($tx->status === 'success' || empty($tx->status)) ? 'payment_successful' : 'payment_failed',
            'paid_at' => optional($tx->paid_at ?: now())->toISOString(),
            'amount' => $amountKobo,
            'amount_in_kobo' => $amountKobo,
            'amount_in_naira' => $amountNaira,
            'settlement_amount' => $amountNaira,
            'orderAmount' => $amountNaira,
            'charged_amount' => $chargedNaira,
            'fee' => $feeNaira,
            'charge_mode' => $tx->charge_mode ?: 'customer',
            'currency' => $tx->currency ?: 'NGN',
            'method' => $tx->method ?: 'transfer',
            'provider' => $tx->provider ?: 'palmpay',
            'provider_reference' => $tx->provider_reference ?: $tx->reference,
            'provider_transaction_id' => $tx->provider_transaction_id ?: $tx->reference,
            'timestamp' => now()->format('Y-m-d H:i:s'),

            // Flat recipient and customer fields
            'account_number' => $accountNo ?: '',
            'accountNumber' => $accountNo ?: '',
            'account_no' => $accountNo ?: '',
            'destination_account_number' => $accountNo ?: '',
            'bank_name' => $bankName,
            'bank_code' => $bankCode,
            'customer_name' => $customerName,
            'customer_email' => $customerEmail,
            'customer_phone' => $customerPhone,
            'email' => $customerEmail,
            'phone' => $customerPhone,

            // Structured sub-objects
            'customer' => [
                'name' => $customerName,
                'email' => $customerEmail,
                'phone' => $customerPhone,
                'user_id' => $merchant?->id,
            ],
            'receiver' => [
                'account_number' => $accountNo ?: '',
                'account_name' => $customerName,
                'bank_name' => $bankName,
            ],
            'sender' => [
                'name' => $customerName,
                'account_number' => null,
                'bank_name' => null,
            ],
            'virtual_account' => [
                'account_number' => $accountNo ?: '',
                'accountNumber' => $accountNo ?: '',
                'account_name' => $customerName,
                'bank_name' => $bankName,
                'bank_code' => $bankCode,
                'customer_phone' => $customerPhone,
                'customer_email' => $customerEmail,
                'merchant_ref' => $merchantRef ?: $accountNo,
                'account_reference' => $accountRef ?: $accountNo,
            ],
            'source' => [
                'account_name' => $customerName,
                'account_number' => null,
                'bank_name' => null,
            ],
        ];

        // Wrapped in `data` subkey for Paystack, Monietude, and VTULoaded handlers
        $payload['data'] = $payload;

        return $payload;
    }

    public function repushForTransaction(GatewayTransaction $tx, ?string $overrideUrl = null, array $customOverrides = []): array
    {
        $merchant = $tx->relationLoaded('merchant') ? $tx->merchant : $tx->merchant()->first();

        $url = $overrideUrl
            ? trim($overrideUrl)
            : $this->resolveWebhookUrl($tx->merchant_webhook_url, $merchant?->merchant_webhook_url);

        if ($url === '') {
            return [
                'success' => false,
                'delivery' => null,
                'message' => "Cannot repush webhook: Merchant #{$tx->merchant_id} (" . ($merchant?->name ?? 'N/A') . ") has not configured a webhook URL. Please specify a Target Webhook Endpoint.",
            ];
        }

        $event = $tx->status === 'success' ? 'payment.success' : 'payment.' . ($tx->status ?: 'updated');
        $payload = $this->buildTransactionPayload($tx, $event, $customOverrides);

        // Find existing delivery or create new one
        $delivery = GatewayWebhookDelivery::query()
            ->where(function ($q) use ($tx) {
                $q->where('gateway_transaction_id', $tx->id);
                if (!empty($tx->reference)) {
                    $q->orWhere('reference', $tx->reference);
                }
            })
            ->latest('id')
            ->first();

        if ($delivery) {
            $delivery->forceFill([
                'url' => $url,
                'event' => $event,
                'payload' => $payload,
                'gateway_transaction_id' => $tx->id,
            ])->save();
        } else {
            $delivery = GatewayWebhookDelivery::create([
                'gateway_transaction_id' => $tx->id,
                'merchant_id' => $tx->merchant_id,
                'reference' => $tx->reference,
                'url' => $url,
                'event' => $event,
                'payload' => $payload,
                'attempts' => 0,
            ]);
        }

        $delivery = $this->attempt($delivery);

        $bodySnippet = $delivery->response_body ? Str::limit(trim(strip_tags($delivery->response_body)), 140) : '';

        if ($delivery->delivered_at) {
            $msg = "Webhook repushed successfully! Target endpoint answered HTTP {$delivery->response_status}";
            if ($bodySnippet) {
                $msg .= ": \"{$bodySnippet}\"";
            }
            return [
                'success' => true,
                'delivery' => $delivery,
                'message' => $msg,
            ];
        } elseif ($delivery->response_status) {
            $msg = "Webhook reached {$url} (HTTP {$delivery->response_status})";
            if ($delivery->error_message) {
                $msg .= " — {$delivery->error_message}";
            } elseif ($bodySnippet) {
                $msg .= ": \"{$bodySnippet}\"";
            }
            return [
                'success' => false,
                'delivery' => $delivery,
                'message' => $msg,
            ];
        } else {
            return [
                'success' => false,
                'delivery' => $delivery,
                'message' => "Webhook delivery to {$url} failed: " . ($delivery->error_message ?: 'Connection error or timeout.'),
            ];
        }
    }

    public function resolveSecretKey(?int $merchantId): string
    {
        if ($merchantId) {
            $merchant = User::find($merchantId);
            $apiKey = $merchant?->merchantApiKeys()->latest('id')->first();
            if ($apiKey && !empty($apiKey->secret_key)) {
                return $apiKey->secret_key;
            }
        }

        return config('services.gateway.webhook_secret')
            ?: (env('PAYZENTER_WEBHOOK_SECRET')
            ?: 'ea656c67837206e75979c6b853232dcf3289a4792c474063f8b0b3ea7220de9cb8ff7c398f3a4a2dffb7dfb85a5d98dc8e8aba820420582d797c618c');
    }

    private function resolveWebhookUrl(?string ...$candidates): string
    {
        foreach ($candidates as $candidate) {
            if (is_string($candidate) && trim($candidate) !== '') {
                return trim($candidate);
            }
        }

        return '';
    }

    private function storeAndAttempt(array $attributes): GatewayWebhookDelivery
    {
        $delivery = GatewayWebhookDelivery::create($attributes);
        $this->attempt($delivery);

        return $delivery->fresh();
    }

    private function extractFundingReference(array $data): string
    {
        $reference = $data['reference'] ?? $data['transactionReference'] ?? $data['sessionId'] ?? null;

        if (is_string($reference) && trim($reference) !== '') {
            return trim($reference);
        }

        return (string) Str::uuid();
    }
}
