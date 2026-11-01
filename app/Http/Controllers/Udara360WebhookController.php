<?php

namespace App\Http\Controllers;

use App\Helpers\SettingHelper;
use App\Models\GatewayTransaction;
use App\Models\User;
use App\Models\UserTransaction;
use App\Models\UserVirtualAccount;
use App\Models\VirtualAccount;
use App\Services\GatewayWebhookService;
use App\Services\Udara360Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Udara360WebhookController extends Controller
{
    public function handle(Request $request, Udara360Service $udaraService, GatewayWebhookService $webhooks)
    {
        $raw = $request->all();

        Log::info('Udara360 webhook incoming', [
            'payload' => $raw,
            'headers' => $request->headers->all(),
        ]);

        // Optional basic auth check if configured in settings/env
        $expectedWebhookSecret = (string) SettingHelper::get('udara360_webhook_secret', env('UDARA360_WEBHOOK_SECRET', ''));
        if ($expectedWebhookSecret !== '') {
            $authHeader = (string) ($request->header('Authorization') ?? $request->header('x-webhook-secret') ?? '');
            if ($authHeader !== '' && !str_contains($authHeader, $expectedWebhookSecret)) {
                Log::warning('Udara360 webhook unauthorized signature/secret mismatch');
                return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
            }
        }

        // Notification structure: {"notificationType": 5, "payload": {...}, "requestReference": "..."}
        // or direct payload: {"event": "...", "data": {...}}
        $notificationType = $raw['notificationType'] ?? null;
        $payload = $raw['payload'] ?? $raw['data'] ?? $raw;

        if (is_string($payload)) {
            $decoded = json_decode($payload, true);
            if (is_array($decoded)) {
                $payload = $decoded;
            }
        }

        // Determine if this is an inward credit transaction
        // Udara360 event 5 = TRANSACTION SUCCESSFUL / PostingsCompleted
        $recordType = strtoupper(trim((string) ($payload['RecordType'] ?? $payload['recordType'] ?? '')));
        $isCredit = ($recordType === 'CREDIT' || $recordType === '' || (int) $notificationType === 5);

        // Check if ChannelTransactionIdentifier indicates Inward
        $channelIdent = $payload['ChannelTransactionIdentifier'] ?? $payload['channelTransactionIdentifier'] ?? null;
        if (is_string($channelIdent)) {
            $parsedChannel = json_decode($channelIdent, true);
            $txType = strtoupper(trim((string) ($parsedChannel['TransactionType'] ?? $parsedChannel['transactionType'] ?? '')));
            if ($txType !== '' && $txType !== 'INWARD') {
                Log::info('Udara360 webhook ignored non-inward transaction', ['tx_type' => $txType]);
                return response()->json(['status' => 'success', 'message' => 'Non-inward event ignored'], 200);
            }
        }

        if (!$isCredit) {
            Log::info('Udara360 webhook ignored non-credit record', ['record_type' => $recordType]);
            return response()->json(['status' => 'success', 'message' => 'Debit/non-credit event ignored'], 200);
        }

        // Extract virtual account number
        $vaNo = $payload['AccountNumber'] 
            ?? $payload['RecipientAccountNumber'] 
            ?? $payload['accountNumber'] 
            ?? $payload['recipientAccountNumber'] 
            ?? $payload['account_number'] 
            ?? $raw['account_number'] 
            ?? null;

        if (!is_string($vaNo) || trim($vaNo) === '') {
            Log::warning('Udara360 webhook missing account number', ['payload' => $payload]);
            return response()->json(['status' => 'error', 'message' => 'Account number not found'], 400);
        }

        $vaNo = trim($vaNo);

        // Extract Amount
        // Doc note: "All amounts supplied must be in their base currency. E.g. NGN 5000.50 should be passed as 500050 kobo"
        // Also has "RealAmount" field in Naira.
        $realAmount = $payload['RealAmount'] ?? $payload['realAmount'] ?? null;
        $rawAmount = $payload['Amount'] ?? $payload['amount'] ?? 0;

        if ($realAmount !== null && (float) $realAmount > 0) {
            $amountNgn = (float) $realAmount;
        } else {
            $numericAmount = (float) $rawAmount;
            // If amount has no decimal and is >= 100, check if in kobo
            if (is_int($rawAmount) || (is_numeric($rawAmount) && floor($numericAmount) == $numericAmount && $numericAmount >= 100)) {
                $amountNgn = $numericAmount / 100;
            } else {
                $amountNgn = $numericAmount;
            }
        }

        if ($amountNgn <= 0) {
            Log::warning('Udara360 webhook invalid amount', ['amount' => $amountNgn]);
            return response()->json(['status' => 'error', 'message' => 'Invalid transaction amount'], 400);
        }

        $amountKobo = (int) round($amountNgn * 100);

        // Extract reference & sender details
        $reference = $payload['ReferenceNumber'] 
            ?? $payload['InstrumentNumber'] 
            ?? $payload['reference'] 
            ?? $raw['requestReference'] 
            ?? $payload['Identifier'] 
            ?? ('UDARA_' . time() . '_' . Str::random(6));

        $senderName = $payload['SenderName'] ?? $payload['senderName'] ?? ($payload['AccountName'] ?? 'Bank Transfer Customer');
        $senderAccount = $payload['SenderAccountNumber'] ?? $payload['senderAccountNumber'] ?? null;
        $senderBank = $payload['SenderBank'] ?? $payload['senderBank'] ?? null;

        // Find matching Dedicated VirtualAccount
        $virtualAccount = VirtualAccount::where('account_number', $vaNo)->first();
        $userVirtualAccount = null;

        if (!$virtualAccount) {
            $userVirtualAccount = UserVirtualAccount::where('account_number', $vaNo)->first();
        }

        if (!$virtualAccount && !$userVirtualAccount) {
            Log::warning('Udara360 webhook no virtual account found for account number', [
                'account_number' => $vaNo,
                'reference' => $reference,
            ]);
            return response()->json(['status' => 'error', 'message' => 'Virtual account not found'], 404);
        }

        $user = $virtualAccount ? $virtualAccount->user : $userVirtualAccount->user;

        if (!$user instanceof User) {
            Log::error('Udara360 webhook user not associated with virtual account', ['account_number' => $vaNo]);
            return response()->json(['status' => 'error', 'message' => 'Account owner not found'], 404);
        }

        // Avoid duplicate fulfillment
        $txRef = 'VA_TX_' . $reference;
        $existingTx = GatewayTransaction::where('reference', $txRef)
            ->orWhere('provider_reference', $reference)
            ->first();

        if ($existingTx && $existingTx->status === 'success') {
            Log::info('Udara360 webhook transaction already fulfilled', ['reference' => $reference]);
            return response()->json(['status' => 'success', 'message' => 'Transaction already processed'], 200);
        }

        DB::beginTransaction();
        try {
            // Calculate fees
            $fixedFee = (float) SettingHelper::get(SettingHelper::VIRTUAL_ACCOUNT_FIXED_FEE, 0);
            $percentageFee = (float) SettingHelper::get(SettingHelper::VIRTUAL_ACCOUNT_PERCENTAGE_FEE, 0);
            $chargeNgn = getTotalFee($amountNgn, $fixedFee, $percentageFee);
            $chargeKobo = (int) round($chargeNgn * 100);

            // Create or update GatewayTransaction
            if (!$existingTx) {
                $tx = GatewayTransaction::create([
                    'merchant_id' => $user->id,
                    'reference' => $txRef,
                    'amount' => $amountKobo,
                    'fee' => $chargeKobo,
                    'currency' => 'NGN',
                    'status' => 'success',
                    'paid_at' => now(),
                    'method' => 'transfer',
                    'provider' => 'udara360',
                    'provider_reference' => (string) $reference,
                    'bank_transfer_account' => $vaNo,
                    'bank_transfer_bank' => $virtualAccount?->bank_name ?? $userVirtualAccount?->bank_name ?? 'Providus Bank',
                    'bank_transfer_name' => $virtualAccount?->account_name ?? $userVirtualAccount?->account_name ?? ($user->first_name . ' ' . $user->last_name),
                    'customer_email' => $virtualAccount?->customer_email ?? $user->email,
                    'customer_phone' => $virtualAccount?->customer_phone ?? $user->phone ?? $user->contact,
                    'customer_name' => $senderName,
                    'raw_provider_webhook' => $raw,
                ]);
            } else {
                $tx = $existingTx;
                $tx->update([
                    'status' => 'success',
                    'paid_at' => $tx->paid_at ?? now(),
                    'fee' => $chargeKobo,
                    'raw_provider_webhook' => $raw,
                ]);
            }

            // Credit user wallet
            $netAmountNgn = max(0, $amountNgn - $chargeNgn);
            $user->increment('wallet_balance', $amountNgn);

            // Record transaction logs
            $user->transactions()->create([
                'amount' => $amountNgn,
                'type' => UserTransaction::TYPE_CREDIT,
                'status' => UserTransaction::STATUS_SUCCESSFUL,
                'source' => UserTransaction::SOURCE_BANK_TRANSFER,
                'message' => "₦" . number_format($amountNgn, 2) . " received via Udara360 virtual account from {$senderName}",
                'reference' => $txRef,
            ]);

            if ($chargeNgn > 0) {
                $user->decrement('wallet_balance', $chargeNgn);

                $user->transactions()->create([
                    'amount' => $chargeNgn,
                    'type' => UserTransaction::TYPE_DEBIT,
                    'status' => UserTransaction::STATUS_SUCCESSFUL,
                    'source' => UserTransaction::SOURCE_BANK_TRANSFER,
                    'message' => "₦" . number_format($chargeNgn, 2) . " virtual account charge deducted",
                    'reference' => $txRef . '_fee',
                    'fee' => $chargeNgn,
                ]);
            }

            DB::commit();

            // Dispatch 3rd party merchant webhooks
            if ($virtualAccount) {
                $fundingData = [
                    'amount' => $amountNgn,
                    'currency' => 'NGN',
                    'reference' => $txRef,
                    'sessionId' => (string) $reference,
                    'sourceAccountName' => $senderName,
                    'sourceAccountNumber' => $senderAccount,
                    'sourceBankName' => $senderBank,
                ];

                $webhooks->dispatchVirtualAccountFunding($virtualAccount, $fundingData);
                $webhooks->dispatch($tx->fresh(), 'payment.success');
            }

            Log::info('Udara360 webhook fulfilled successfully', [
                'user_id' => $user->id,
                'account_number' => $vaNo,
                'amount' => $amountNgn,
                'charge' => $chargeNgn,
                'reference' => $reference,
            ]);

            return response()->json(['status' => 'success', 'message' => 'Processed successfully'], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Udara360 webhook fulfillment failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
