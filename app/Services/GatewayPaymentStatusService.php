<?php

namespace App\Services;

use App\Models\GatewayTransaction;
use App\Models\UserTransaction;
use App\Services\PalmPayService;
use App\Services\GatewayWebhookService;
use KingBes\PhpFlutterwave\Flutterwave;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GatewayPaymentStatusService
{
    /**
     * Check and refresh live status with the underlying payment provider in real-time.
     */
    public function checkAndRefreshStatus(GatewayTransaction $tx): array
    {
        if ($tx->status === 'success') {
            return [
                'status' => 'success',
                'message' => 'Transaction already confirmed successful.',
                'changed' => false,
            ];
        }

        $provider = strtolower((string) ($tx->provider ?? ''));

        try {
            if ($provider === 'palmpay') {
                return $this->refreshPalmPayStatus($tx);
            }

            if ($provider === 'flutterwave') {
                return $this->refreshFlutterwaveStatus($tx);
            }

            if ($provider === 'paystack') {
                return $this->refreshPaystackStatus($tx);
            }

            // Generic / Virtual Account / Udara360 check
            if ($tx->expires_at && $tx->expires_at->isPast()) {
                $tx->update(['status' => 'failed']);
                return [
                    'status' => 'failed',
                    'message' => 'Transaction has expired without payment.',
                    'changed' => true,
                ];
            }

            if ($tx->created_at && $tx->created_at->lt(now()->subHours(2))) {
                $tx->update(['status' => 'failed']);
                return [
                    'status' => 'failed',
                    'message' => 'Payment session expired without completion.',
                    'changed' => true,
                ];
            }

            $currentStatus = in_array($tx->status, ['paying', 'processing']) ? 'processing' : $tx->status;
            if ($tx->status === 'paying') {
                $tx->update(['status' => 'processing']);
            }

            return [
                'status' => $currentStatus,
                'message' => 'Transaction is currently processing.',
                'changed' => false,
            ];
        } catch (\Throwable $e) {
            Log::warning('GatewayPaymentStatusService error for tx ' . $tx->reference . ': ' . $e->getMessage());

            if ($tx->expires_at && $tx->expires_at->isPast()) {
                $tx->update(['status' => 'failed']);
                return [
                    'status' => 'failed',
                    'message' => 'Payment expired.',
                    'changed' => true,
                ];
            }

            return [
                'status' => in_array($tx->status, ['paying', 'processing']) ? 'processing' : $tx->status,
                'message' => 'Could not verify status with provider: ' . $e->getMessage(),
                'changed' => false,
            ];
        }
    }

    private function refreshPalmPayStatus(GatewayTransaction $tx): array
    {
        $palmPay = new PalmPayService();
        $orderId = $tx->palmpay_order_id;
        $orderNo = $tx->palmpay_order_no ?: $tx->provider_reference;

        if (!$orderId && !$orderNo) {
            if ($tx->expires_at && $tx->expires_at->isPast()) {
                $tx->update(['status' => 'failed']);
                return ['status' => 'failed', 'message' => 'Order expired with no provider reference.', 'changed' => true];
            }
            if ($tx->status === 'paying') {
                $tx->update(['status' => 'processing']);
            }
            return ['status' => 'processing', 'message' => 'Awaiting customer payment confirmation.', 'changed' => false];
        }

        $res = $palmPay->queryOrderStatus(array_filter([
            'orderId' => $orderId,
            'orderNo' => $orderNo,
        ]));

        $data = $res['data'] ?? [];
        $rawStatus = strtoupper(trim((string) ($data['orderStatus'] ?? ($data['status'] ?? ''))));

        if (in_array($rawStatus, ['2', '00', '00000000', 'SUCCESS', 'SUCCESSFUL', 'PAY_SUCCESS', 'FINISHED', 'COMPLETED'])) {
            $this->markAsSuccess($tx, $data);
            return ['status' => 'success', 'message' => 'PalmPay verified: Payment successful! Merchant credited.', 'changed' => true];
        }

        if (in_array($rawStatus, ['3', '4', '5', 'FAILED', 'FAIL', 'CLOSED', 'EXPIRED', 'CANCELED', 'CANCELLED', 'REJECTED'])) {
            $tx->update([
                'status' => 'failed',
                'raw_provider_response' => array_merge($tx->raw_provider_response ?? [], ['query' => $data]),
            ]);
            return ['status' => 'failed', 'message' => 'PalmPay verified: Transaction failed or expired.', 'changed' => true];
        }

        if ($tx->expires_at && $tx->expires_at->isPast()) {
            $tx->update([
                'status' => 'failed',
                'raw_provider_response' => array_merge($tx->raw_provider_response ?? [], ['query' => $data]),
            ]);
            return ['status' => 'failed', 'message' => 'Payment session expired without completion. Marked as Failed.', 'changed' => true];
        }

        $tx->update([
            'status' => 'processing',
            'raw_provider_response' => array_merge($tx->raw_provider_response ?? [], ['query' => $data]),
        ]);

        return ['status' => 'processing', 'message' => 'PalmPay verified: Payment is currently processing / awaiting transfer.', 'changed' => ($tx->status !== 'processing')];
    }

    private function refreshFlutterwaveStatus(GatewayTransaction $tx): array
    {
        $flwRef = $tx->provider_transaction_id ?: $tx->provider_reference;
        if (!$flwRef) {
            return ['status' => 'processing', 'message' => 'No Flutterwave reference found.', 'changed' => false];
        }

        $data = Flutterwave::verifyTransaction($flwRef);
        $status = strtolower((string) ($data['data']['status'] ?? ''));

        if ($status === 'successful') {
            $this->markAsSuccess($tx, $data);
            return ['status' => 'success', 'message' => 'Flutterwave verified: Payment successful! Merchant credited.', 'changed' => true];
        }

        if (in_array($status, ['failed', 'cancelled', 'canceled'])) {
            $tx->update(['status' => 'failed', 'raw_provider_response' => $data]);
            return ['status' => 'failed', 'message' => 'Flutterwave verified: Payment failed.', 'changed' => true];
        }

        if ($tx->expires_at && $tx->expires_at->isPast()) {
            $tx->update(['status' => 'failed', 'raw_provider_response' => $data]);
            return ['status' => 'failed', 'message' => 'Session expired without completion. Marked as Failed.', 'changed' => true];
        }

        $tx->update(['status' => 'processing', 'raw_provider_response' => $data]);
        return ['status' => 'processing', 'message' => 'Transaction is processing on Flutterwave.', 'changed' => false];
    }

    private function refreshPaystackStatus(GatewayTransaction $tx): array
    {
        $ref = $tx->provider_reference ?: $tx->reference;
        $paystackSecret = config('services.paystack.secret') ?: env('PAYSTACK_SECRET_KEY');
        if (!$paystackSecret) {
            return ['status' => 'processing', 'message' => 'Paystack credentials not configured.', 'changed' => false];
        }

        $res = Http::withHeaders([
            'Authorization' => 'Bearer ' . $paystackSecret,
        ])->get('https://api.paystack.co/transaction/verify/' . urlencode($ref));

        $json = $res->json();
        $status = strtolower((string) ($json['data']['status'] ?? ''));

        if ($status === 'success') {
            $this->markAsSuccess($tx, $json);
            return ['status' => 'success', 'message' => 'Paystack verified: Payment successful! Merchant credited.', 'changed' => true];
        }

        if (in_array($status, ['failed', 'abandoned'])) {
            $tx->update(['status' => 'failed', 'raw_provider_response' => $json]);
            return ['status' => 'failed', 'message' => 'Paystack verified: Payment ' . ucfirst($status) . '.', 'changed' => true];
        }

        if ($tx->expires_at && $tx->expires_at->isPast()) {
            $tx->update(['status' => 'failed', 'raw_provider_response' => $json]);
            return ['status' => 'failed', 'message' => 'Payment session expired without completion. Marked as Failed.', 'changed' => true];
        }

        $tx->update(['status' => 'processing', 'raw_provider_response' => $json]);
        return ['status' => 'processing', 'message' => 'Transaction is processing on Paystack.', 'changed' => false];
    }

    public function markAsSuccess(GatewayTransaction $tx, array $rawResponse = []): void
    {
        if ($tx->status === 'success') {
            return;
        }

        DB::transaction(function () use ($tx, $rawResponse) {
            $amountNgn = $tx->amount / 100;
            $chargeKobo = $tx->fee;
            $chargeNgn = $chargeKobo / 100;

            $tx->update([
                'status' => 'success',
                'paid_at' => $tx->paid_at ?? now(),
                'raw_provider_response' => !empty($rawResponse) ? array_merge($tx->raw_provider_response ?? [], ['verified' => $rawResponse]) : $tx->raw_provider_response,
            ]);

            $merchant = $tx->merchant;
            if ($merchant) {
                $merchant->wallet_balance += $amountNgn;
                if ($tx->charge_mode === 'merchant') {
                    $merchant->wallet_balance -= $chargeNgn;
                }
                $merchant->save();

                $merchant->transactions()->create([
                    'amount' => $amountNgn,
                    'type' => UserTransaction::TYPE_CREDIT,
                    'status' => UserTransaction::STATUS_SUCCESSFUL,
                    'source' => UserTransaction::SOURCE_BANK_TRANSFER,
                    'message' => 'Gateway payment received: ₦' . number_format($amountNgn, 2),
                    'reference' => $tx->reference,
                ]);

                if ($chargeNgn > 0 && $tx->charge_mode === 'merchant') {
                    $merchant->transactions()->create([
                        'amount' => $chargeNgn,
                        'type' => UserTransaction::TYPE_DEBIT,
                        'status' => UserTransaction::STATUS_SUCCESSFUL,
                        'source' => UserTransaction::SOURCE_BANK_TRANSFER,
                        'message' => 'Gateway payment charge: ₦' . number_format($chargeNgn, 2),
                        'reference' => $tx->reference,
                        'fee' => $chargeNgn,
                    ]);
                }
            }

            (new GatewayWebhookService())->dispatch($tx, 'payment.success');
        });
    }
}

