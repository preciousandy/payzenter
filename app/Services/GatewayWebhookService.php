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

        $payload = [
            'event' => $event,
            'type' => 'payment',
            'reference' => $tx->reference,
            'status' => $tx->status,
            'paid_at' => optional($tx->paid_at)->toISOString(),
            'amount' => $tx->amount,
            'charged_amount' => $tx->charged_amount,
            'fee' => $tx->fee,
            'charge_mode' => $tx->charge_mode,
            'currency' => $tx->currency,
            'method' => $tx->method,
            'provider' => $tx->provider,
            'provider_reference' => $tx->provider_reference,
            'provider_transaction_id' => $tx->provider_transaction_id,
        ];

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
            $res = Http::timeout(10)
                ->acceptJson()
                ->asJson()
                ->post($delivery->url, $delivery->payload ?? []);

            $status = $res->status();
            $body = $res->body();

            $delivery->forceFill([
                'response_status' => $status,
                'response_body' => $body,
                'delivered_at' => $status >= 200 && $status < 300 ? now() : null,
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
