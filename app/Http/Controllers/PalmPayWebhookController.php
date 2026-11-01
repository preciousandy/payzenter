<?php

namespace App\Http\Controllers;

use App\Models\GatewayTransaction;
use App\Models\Transfer;
use App\Models\UserTransaction;
use App\Models\UserVirtualAccount;
use App\Models\VirtualAccount;
use App\Services\GatewayWebhookService;
use App\Services\PalmPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PalmPayWebhookController extends Controller
{
    public function handle(Request $request, PalmPayService $palmPay, GatewayWebhookService $webhooks)
    {
        $payload = $request->all();

        Log::info('PalmPay webhook incoming', [
            'payload' => $payload,
            'headers' => $request->headers->all(),
        ]);

        $signature = $payload['sign'] 
            ?? $payload['signature'] 
            ?? $payload['signMsg'] 
            ?? $request->header('Signature') 
            ?? $request->header('sign') 
            ?? $request->header('X-Palmpay-Signature') 
            ?? null;

        unset($payload['sign'], $payload['signature'], $payload['signMsg']);

        $publicKey = (string) env('PALMPAY_PLATFORM_PUBLIC_KEY', '');
        if ($signature && $publicKey !== '') {
            try {
                $ok = $palmPay->verifyWebhook($payload, (string) $signature);
                if (!$ok) {
                    Log::warning('PalmPay webhook signature verification returned false', [
                        'payload' => $payload,
                        'signature' => $signature,
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning('PalmPay webhook signature verification exception (proceeding with transaction fulfillment): ' . $e->getMessage());
            }
        } else {
            Log::info('PalmPay webhook signature check skipped (no public key or signature header provided)');
        }

        $orderId = $payload['orderId'] ?? $payload['merchantTxRef'] ?? $payload['order_id'] ?? null;
        $orderNo = $payload['orderNo'] ?? $payload['palmpayOrderNo'] ?? $payload['order_no'] ?? null;
        $accountRef = $payload['accountReference'] ?? $payload['accountRef'] ?? $payload['reference'] ?? null;
        $vaNo = $payload['virtualAccountNo'] ?? $payload['accountNo'] ?? $payload['account_number'] ?? $payload['virtualAccount'] ?? null;
        $transId = $payload['transId'] ?? $payload['transactionId'] ?? $payload['provider_reference'] ?? null;

        // Search for specific order transaction first (do NOT match generic bank_transfer_account here because dedicated VAs handle multiple transfers)
        $query = GatewayTransaction::query();
        $query->where(function ($q) use ($orderId, $orderNo, $accountRef, $transId) {
            $hasCondition = false;
            if ($orderId) {
                $q->orWhere('palmpay_order_id', $orderId)
                  ->orWhere('reference', $orderId);
                $hasCondition = true;
            }
            if ($orderNo) {
                $q->orWhere('palmpay_order_no', $orderNo)
                  ->orWhere('reference', 'VA_TX_' . $orderNo);
                $hasCondition = true;
            }
            if ($accountRef) {
                $q->orWhere('reference', $accountRef)
                  ->orWhere('provider_reference', $accountRef);
                if (preg_match('/^(GTW|TX)(\d+)_/i', (string) $accountRef, $matches)) {
                    $q->orWhere('id', $matches[2]);
                }
                $hasCondition = true;
            }
            if ($transId) {
                $q->orWhere('provider_reference', $transId)
                  ->orWhere('palmpay_order_no', $transId);
                $hasCondition = true;
            }

            if (!$hasCondition) {
                $q->whereRaw('1 = 0');
            }
        });

        $tx = $query->first();

        $rawStatus = $payload['orderStatus'] ?? $payload['paymentStatus'] ?? $payload['status'] ?? $payload['respCode'] ?? null;
        $isCashier = ($tx !== null);
        $status = $this->mapPalmPayStatus($rawStatus, $isCashier);

        Log::info('PalmPay webhook extracted values', [
            'orderId' => $orderId,
            'orderNo' => $orderNo,
            'accountRef' => $accountRef,
            'vaNo' => $vaNo,
            'transId' => $transId,
            'rawStatus' => $rawStatus,
            'mappedStatus' => $status,
            'isCashier' => $isCashier,
        ]);

        // If no existing transaction matches this specific order, check if incoming deposit belongs to a Dedicated Virtual Account
        if (!$tx) {
            $vaObj = null;
            if ($vaNo) {
                $vaObj = VirtualAccount::where('account_number', $vaNo)->first();
            }
            if (!$vaObj && $accountRef) {
                $vaObj = VirtualAccount::where('account_reference', $accountRef)
                    ->orWhere('merchant_ref', $accountRef)
                    ->first();
            }
            if (!$vaObj && $vaNo) {
                $userVa = UserVirtualAccount::where('account_number', $vaNo)->first();
                if ($userVa) {
                    $vaObj = (object) [
                        'user_id' => $userVa->user_id,
                        'account_number' => $userVa->account_number,
                        'bank_name' => $userVa->bank_name,
                        'bank_code' => $userVa->bank_code,
                        'account_name' => $userVa->account_name,
                        'customer_email' => $userVa->user?->email,
                        'customer_phone' => $userVa->user?->phone ?? $userVa->user?->contact,
                        'provider' => 'palmpay',
                    ];
                }
            }

            if ($vaObj) {
                Log::info('PalmPay webhook matched Dedicated VirtualAccount', [
                    'account_number' => $vaObj->account_number,
                    'user_id' => $vaObj->user_id,
                ]);

                $orderAmountRaw = $payload['orderAmount'] ?? $payload['amount'] ?? $payload['transAmount'] ?? 0;
                $amountKobo = (int) round(((float) $orderAmountRaw));

                $txRef = 'VA_TX_' . ($orderNo ?? ($orderId ?? (time() . '_' . Str::lower(Str::random(6)))));

                $existingTx = GatewayTransaction::where('reference', $txRef)
                    ->orWhere(function ($q) use ($orderNo) {
                        if (is_string($orderNo) && $orderNo !== '') {
                            $q->where('palmpay_order_no', $orderNo);
                        } else {
                            $q->whereRaw('1 = 0');
                        }
                    })
                    ->first();

                if (!$existingTx) {
                    $tx = GatewayTransaction::create([
                        'merchant_id' => $vaObj->user_id,
                        'reference' => $txRef,
                        'amount' => $amountKobo,
                        'fee' => 0,
                        'currency' => 'NGN',
                        'status' => 'pending', // Will be updated to success below to trigger wallet credit & webhook
                        'method' => 'transfer',
                        'provider' => 'palmpay',
                        'provider_reference' => $orderNo ?? $accountRef ?? $vaNo,
                        'bank_transfer_account' => $vaObj->account_number,
                        'bank_transfer_bank' => $vaObj->bank_name,
                        'bank_transfer_name' => $vaObj->account_name,
                        'customer_email' => $vaObj->customer_email,
                        'customer_phone' => $vaObj->customer_phone,
                        'customer_name' => $vaObj->account_name,
                        'palmpay_order_id' => is_string($orderId) && $orderId !== '' ? $orderId : null,
                        'palmpay_order_no' => is_string($orderNo) && $orderNo !== '' ? $orderNo : null,
                        'raw_provider_webhook' => $request->all(),
                    ]);
                } else {
                    $tx = $existingTx;
                }
            }
        }

        if ($tx) {
            Log::info('PalmPay webhook processing GatewayTransaction', ['tx_id' => $tx->id, 'tx_ref' => $tx->reference]);
            $previousStatus = $tx->status;
            $tx->update([
                'status' => $status !== 'unknown' ? $status : $tx->status,
                'paid_at' => ($status === 'success') ? ($tx->paid_at ?? now()) : $tx->paid_at,
                'palmpay_order_id' => is_string($orderId) && $orderId !== '' ? $orderId : $tx->palmpay_order_id,
                'palmpay_order_no' => is_string($orderNo) && $orderNo !== '' ? $orderNo : $tx->palmpay_order_no,
                'raw_provider_webhook' => $request->all(),
            ]);

            if ($status === 'success' && $previousStatus !== 'success') {
                DB::transaction(function () use ($tx) {
                    $amountNgn = $tx->amount / 100;
                    $chargeKobo = $tx->fee;
                    $chargeNgn = $chargeKobo / 100;

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
                });

                $webhooks->dispatch($tx->fresh(), 'payment.success');
                Log::info('PalmPay webhook successfully credited merchant balance & dispatched webhook', ['tx_ref' => $tx->reference]);
            }
        } else {
            Log::warning('PalmPay webhook no matching GatewayTransaction or VirtualAccount found', ['payload' => $payload]);
        }

        $transferQuery = Transfer::query();
        if (is_string($orderId) && $orderId !== '') {
            $transferQuery->where('reference', $orderId);
        }
        if (is_string($orderNo) && $orderNo !== '') {
            if (is_string($orderId) && $orderId !== '') {
                $transferQuery->orWhere('transfer_id', $orderNo);
            } else {
                $transferQuery->where('transfer_id', $orderNo);
            }
        }

        $transfer = $transferQuery->first();

        if ($transfer) {
            $this->applyTransferStatus($transfer, $this->mapPalmPayTransferStatus($rawStatus), $request->all());
        }

        return response('success', 200);
    }

    private function mapPalmPayStatus($orderStatus, bool $isCashier = false): string
    {
        if ($orderStatus === null) {
            return 'unknown';
        }

        $statusStr = strtoupper(trim((string) $orderStatus));

        // In PalmPay Virtual Account System (vbas_virtual_bank_account), orderStatus 1 or 2 represents successful pay-in!
        $successStatuses = ['2', '00', '00000000', 'SUCCESS', 'SUCCESSFUL', 'PAY_SUCCESS', 'FINISHED', 'COMPLETED', 'PASSED'];
        if (!$isCashier) {
            $successStatuses[] = '1';
        }

        if (in_array($statusStr, $successStatuses, true)) {
            return 'success';
        }

        if (in_array($statusStr, ['0', 'UNPAID', 'INIT'], true)) {
            return 'unpaid';
        }

        if ($isCashier && $statusStr === '1') {
            return 'paying';
        }

        if (in_array($statusStr, ['3', 'FAILED', 'REJECTED'], true)) {
            return 'failed';
        }

        if (in_array($statusStr, ['4', 'CLOSED', 'EXPIRED', 'CANCELED', 'CANCELLED'], true)) {
            return 'closed';
        }

        return 'unknown';
    }

    private function mapPalmPayTransferStatus($orderStatus): string
    {
        if ($orderStatus === null) {
            return Transfer::STATUS_PENDING;
        }

        $statusStr = strtoupper(trim((string) $orderStatus));

        if (in_array($statusStr, ['1', '2', '00', '00000000', 'SUCCESS', 'SUCCESSFUL', 'FINISHED', 'COMPLETED'], true)) {
            return Transfer::STATUS_SUCCESS;
        }

        if (in_array($statusStr, ['3', '4', 'FAILED', 'REJECTED', 'CLOSED', 'EXPIRED'], true)) {
            return Transfer::STATUS_FAILED;
        }

        return Transfer::STATUS_PENDING;
    }

    private function applyTransferStatus(Transfer $transfer, string $status, array $payload): void
    {
        $previousStatus = $transfer->status;

        DB::transaction(function () use ($transfer, $status, $payload, $previousStatus) {
            $transfer->update([
                'status' => $status,
                'last_status_check_at' => now(),
                'response_data' => array_merge($transfer->response_data ?? [], [
                    'provider' => 'palmpay',
                    'webhook' => $payload,
                ]),
            ]);

            $transaction = UserTransaction::query()
                ->where('user_id', $transfer->user_id)
                ->where('reference', $transfer->reference)
                ->where('source', UserTransaction::SOURCE_BANK_TRANSFER)
                ->latest('id')
                ->first();

            if ($transaction) {
                $mappedStatus = match ($status) {
                    Transfer::STATUS_SUCCESS => UserTransaction::STATUS_SUCCESSFUL,
                    Transfer::STATUS_FAILED => UserTransaction::STATUS_FAILED,
                    default => UserTransaction::STATUS_PENDING,
                };

                if ($transaction->status !== $mappedStatus) {
                    $transaction->update(['status' => $mappedStatus]);
                }
            }

            if ($status === Transfer::STATUS_FAILED && $previousStatus !== Transfer::STATUS_FAILED) {
                $totalRefund = $transfer->amount + $transfer->fee;
                $transfer->user->increment('wallet_balance', $totalRefund);

                UserTransaction::create([
                    'user_id' => $transfer->user_id,
                    'type' => UserTransaction::TYPE_CREDIT,
                    'amount' => $totalRefund,
                    'source' => UserTransaction::SOURCE_BANK_TRANSFER,
                    'status' => UserTransaction::STATUS_SUCCESSFUL,
                    'message' => "Refunded N{$totalRefund} to your account due to failed transfer",
                ]);
            }
        });
    }
}
