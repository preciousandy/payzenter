<?php

namespace App\Http\Controllers;

use App\Helpers\SettingHelper;
use App\Models\GatewayTransaction;
use App\Models\PaymentTransaction;
use App\Models\Transfer;
use App\Models\UserTransaction;
use App\Models\UserVirtualAccount;
use App\Models\VirtualAccount;
use App\Services\GatewayWebhookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransferWebhookController extends Controller
{
    private function handleGatewayPayment($data)
    {
        $txId = substr($data['externalId'], 2);
        $tx = GatewayTransaction::find($txId);

        if (!$tx) {
            return response()->json(['status' => 'error', 'message' => 'Transaction not found'], 404);
        }

        if ($tx->status === 'success') {
            return response()->json(['status' => 'success'], 200);
        }

        try {
            DB::beginTransaction();

            // Calculate the charge
            $amountNgn = $tx->amount / 100;
            $fixedFee = SettingHelper::get(SettingHelper::GATEWAY_PAYMENT_FIXED_FEE, 0);
            $percentageFee = SettingHelper::get(SettingHelper::GATEWAY_PAYMENT_PERCENTAGE_FEE, 0);
            $chargeNgn = getTotalFee($amountNgn, $fixedFee, $percentageFee);
            $chargeKobo = (int) round($chargeNgn * 100);

            $tx->update([
                'status' => 'success',
                'paid_at' => now(),
                'raw_provider_webhook' => $data,
                'fee' => $chargeKobo,
            ]);

            // Update merchant's wallet
            $merchant = $tx->merchant;
            $merchant->wallet_balance += $amountNgn;
            $merchant->wallet_balance -= $chargeNgn;
            $merchant->save();

            // Create credit transaction for merchant
            $merchant->transactions()->create([
                'amount' => $amountNgn,
                'type' => UserTransaction::TYPE_CREDIT,
                'status' => UserTransaction::STATUS_SUCCESSFUL,
                'source' => UserTransaction::SOURCE_BANK_TRANSFER,
                'message' => 'Gateway payment received: ₦' . number_format($amountNgn, 2),
                'reference' => $tx->reference,
            ]);

            // Create debit transaction for charge if charge > 0
            if ($chargeNgn > 0) {
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

            // notify merchant
            (new GatewayWebhookService())->notifyMerchant($tx);

            DB::commit();
            return response()->json(['status' => 'success'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Gateway webhook error: ' . $th->getMessage());
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
        }
    }

    public function handleTransfer(Request $request)
    {

        Log::channel('ajo-savings')->info($request);




        $transfer = $request->toArray();

        $this->processNairaTransfers($transfer);


        return response()->json(['status' => 'success'], 200);
    }

    public function processNairaTransfers($transfer)
    {
        if (isset($transfer['data']) && is_array($transfer['data'])) {
            $transfer = $transfer['data'];
        }

        if (($transfer['status'] ?? null) === 'SUCCESSFUL') {
            $transferReference = $transfer['reference'];

            try {
                $transferItem = Transfer::firstWhere('reference', $transferReference);

                if (!$transferItem) {
                    return response()->json(['status' => 'success'], 200);
                }

                if ($transferItem->status !== Transfer::STATUS_PENDING) {
                    return response()->json(['status' => 'success'], 200);
                }

                DB::beginTransaction();

                $transferItem->update([
                    'status' => Transfer::STATUS_SUCCESS
                ]);

                $debitTx = UserTransaction::query()
                    ->where('user_id', $transferItem->user_id)
                    ->where('source', UserTransaction::SOURCE_BANK_TRANSFER)
                    ->where('type', UserTransaction::TYPE_DEBIT)
                    ->where('reference', $transferReference)
                    ->latest('id')
                    ->first();

                if ($debitTx && $debitTx->status !== UserTransaction::STATUS_SUCCESSFUL) {
                    $debitTx->update(['status' => UserTransaction::STATUS_SUCCESSFUL]);
                }

                DB::commit();
            } catch (\Throwable $th) {
                Log::channel('ajo-savings')->error($th->getMessage());
            }
        } else if (($transfer['status'] ?? null) === 'FAILED') {
            $transferReference = $transfer['reference'];

            try {
                $transferItem = Transfer::firstWhere('reference', $transferReference);

                if (!$transferItem) {
                    return response()->json(['status' => 'success'], 200);
                }

                if ($transferItem->status !== Transfer::STATUS_PENDING) {
                    return response()->json(['status' => 'success'], 200);
                }

                DB::beginTransaction();

                $transferItem->update([
                    'status' => Transfer::STATUS_FAILED,
                    'response_data' => array_merge($transferItem->response_data ?? [], [
                        'webhook' => $transfer,
                    ]),
                ]);

                $totalAmount = $transferItem->amount + $transferItem->fee;

                $formattedAmount = number_format($totalAmount, 2, '.', '');

                $debitTx = UserTransaction::query()
                    ->where('user_id', $transferItem->user_id)
                    ->where('source', UserTransaction::SOURCE_BANK_TRANSFER)
                    ->where('type', UserTransaction::TYPE_DEBIT)
                    ->where('reference', $transferReference)
                    ->latest('id')
                    ->first();

                if ($debitTx && $debitTx->status !== UserTransaction::STATUS_FAILED) {
                    $debitTx->update(['status' => UserTransaction::STATUS_FAILED]);
                }

                $transferItem->user()->increment('wallet_balance', $totalAmount);

                $transferItem->user->transactions()->create([
                    'reference' => $transferReference,
                    'type' => UserTransaction::TYPE_CREDIT,
                    'amount' => $totalAmount,
                    'source' => UserTransaction::SOURCE_BANK_TRANSFER,
                    'message' => "₦{$formattedAmount} was reversed to your account due to failed transfer",
                    'status' => UserTransaction::STATUS_SUCCESSFUL,
                ]);

                DB::commit();
            } catch (\Throwable $th) {
                Log::channel('ajo-savings')->error($th->getMessage());
            }
        } else if (($transfer['status'] ?? null) === 'PENDING') {
            // update transfer status to pending in your db
        }
    }


    public function handleFundWallet(Request $request, GatewayWebhookService $webhooks)
    {
        Log::channel('ajo-savings')->info($request);

        $data = $request->toArray();

        // check for double processing
        $payment_transaction = PaymentTransaction::where('transaction_reference', $data['reference'])
            ->where('payment_method', 'bank_transfer')
            ->first();

        if ($payment_transaction) {
            return response()->json(['status' => 'success'], 200);
        }

        $externalId = $data['externalId'] ?? null;
        if (!$externalId) {
            return response()->json(['status' => 'error', 'message' => 'Missing externalId'], 400);
        }

        // Handle Gateway Transaction payments (Merchant customers)
        if (str_starts_with($externalId, 'TX')) {
            return $this->handleGatewayPayment($data);
        }

        $merchantVirtualAccount = $this->resolveMerchantVirtualAccount($data);
        if ($merchantVirtualAccount) {
            $webhooks->dispatchVirtualAccountFunding($merchantVirtualAccount, $data);

            return response()->json(['status' => 'success'], 200);
        }

        $virtual_account = UserVirtualAccount::where('external_id', $externalId)->first();


        if (!$virtual_account) {
            return response()->json(['status' => 'error', 'message' => 'Virtual account not found'], 404);
        }

        $user = $virtual_account->user;

        DB::beginTransaction();

        // Calculate virtual account charge
        $virtualAccountFixedFee = SettingHelper::get(SettingHelper::VIRTUAL_ACCOUNT_FIXED_FEE, 0);
        $virtualAccountPercentageFee = SettingHelper::get(SettingHelper::VIRTUAL_ACCOUNT_PERCENTAGE_FEE, 0);
        $charge = getTotalFee($data['amount'], $virtualAccountFixedFee, $virtualAccountPercentageFee);

        // Add the full amount to wallet
        $user->increment('wallet_balance', $data['amount']);
        
        // Deduct the charge
        $user->decrement('wallet_balance', $charge);

        $senderName = $data['sourceAccountName'] ?? 'Unknown Sender';

        $user->transactions()->create([
            'type' => UserTransaction::TYPE_CREDIT,
            'amount' => $data['amount'],
            'source' => UserTransaction::SOURCE_BANK_TRANSFER,
            'message' =>  "₦" . number_format($data['amount'], 2, '.', '') . " was credited to your wallet from {$senderName}",
            'status' => UserTransaction::STATUS_SUCCESSFUL,
        ]);

        // Record the charge
        if ($charge > 0) {
            $user->transactions()->create([
                'type' => UserTransaction::TYPE_DEBIT,
                'amount' => $charge,
                'source' => UserTransaction::SOURCE_BANK_TRANSFER,
                'message' =>  "₦" . number_format($charge, 2, '.', '') . " virtual account charge deducted",
                'status' => UserTransaction::STATUS_SUCCESSFUL,
                'fee' => $charge,
            ]);
        }

        PaymentTransaction::create([
            'user_id' => $user->id,
            'amount' => $data['amount'],
            'transaction_id' => $data['reference'],
            'transaction_reference' => $data['reference'],
            'currency' => 'NGN',
            'charge_fee' => $charge,
            'payment_method' => 'bank_transfer',
        ]);

        DB::commit();

        return response()->json(['status' => 'success'], 200);
    }

    private function resolveMerchantVirtualAccount(array $data): ?VirtualAccount
    {
        $externalId = $data['externalId'] ?? null;
        $accountNumber = $data['destinationAccountNumber'] ?? $data['accountNumber'] ?? $data['virtualAccountNumber'] ?? null;

        if ((!is_string($externalId) || trim($externalId) === '') && (!is_string($accountNumber) || trim($accountNumber) === '')) {
            return null;
        }

        return VirtualAccount::query()
            ->when(is_string($externalId) && trim($externalId) !== '', function ($query) use ($externalId) {
                $query->where(function ($innerQuery) use ($externalId) {
                    $innerQuery->where('merchant_ref', $externalId)
                        ->orWhere('account_reference', $externalId);
                });
            })
            ->when(is_string($accountNumber) && trim($accountNumber) !== '', function ($query) use ($accountNumber, $externalId) {
                $query->when(is_string($externalId) && trim($externalId) !== '', function ($nestedQuery) use ($accountNumber) {
                    $nestedQuery->orWhere('account_number', $accountNumber);
                }, function ($nestedQuery) use ($accountNumber) {
                    $nestedQuery->where('account_number', $accountNumber);
                });
            })
            ->first();
    }
}
