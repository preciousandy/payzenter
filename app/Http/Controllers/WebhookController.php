<?php

namespace App\Http\Controllers;

use App\Models\InternationalTransfer;
use App\Models\Transfer;
use App\Models\User;
use App\Models\UserTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use KingFlamez\Rave\Facades\Rave as Flutterwave;


class WebhookController extends Controller
{
    public function handleFlutterwave(Request $request)
    {
        // Log::channel('ajo-savings')->info('here');
        $verified = Flutterwave::verifyWebhook();



        // if it is a charge event, verify and confirm it is a successful transaction
        if ($verified && $request->event == 'charge.completed') {
            $transaction = $request->toArray();

            // Log::channel('ajo-savings')->info($request);

            if ($transaction['event.type'] == 'BANK_TRANSFER_TRANSACTION') {

                $userEmail = $transaction['data']['customer']['email'];

                $amount = $transaction['data']['amount'];


                $user = User::where('email', $userEmail)->first();

                if (!$user) {
                    return response()->json(['status' => 'success'], 200);
                }

                $user->increment('wallet_balance', $amount);

                $user->transactions()->create([
                    'type' => UserTransaction::TYPE_CREDIT,
                    'amount' => $amount,
                    'source' => UserTransaction::SOURCE_BANK_TRANSFER,
                    'message' => "You transaferred ₦{$amount} into your wallet",
                    'status' => UserTransaction::STATUS_SUCCESSFUL,
                ]);
            }
        }

        // if it is a transfer event, verify and confirm it is a successful transfer
        if ($verified && $request->event == 'transfer.completed') {

            // $transfer = Flutterwave::transfers()->fetch($request->data['id']);

            // Log::channel('ajo-savings')->info($request);

            $transfer = $request->toArray();

            if ($transfer['data']['currency'] == "NGN") {
                $this->processNairaTransfers($transfer);
            } else {
                $this->processInternationalTransfers($transfer);
            }
        }

        return response()->json(['status' => 'success'], 200);
    }

    public function processNairaTransfers($transfer)
    {

        if ($transfer['data']['status'] === 'SUCCESSFUL') {
            $transferReference = $transfer['data']['reference'];

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
        } else if ($transfer['data']['status'] === 'FAILED') {
            $transferReference = $transfer['data']['reference'];

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
                    'status' => Transfer::STATUS_FAILED
                ]);

                $totalAmount = $transferItem->amount + $transferItem->fee;

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
                    'message' => "₦{$totalAmount} was reversed to your account",
                    'status' => UserTransaction::STATUS_SUCCESSFUL,

                ]);

                DB::commit();
            } catch (\Throwable $th) {
                Log::channel('ajo-savings')->error($th->getMessage());
            }
        } else if ($transfer['data']['status'] === 'PENDING') {
            // update transfer status to pending in your db
        }
    }


    public function processInternationalTransfers($transfer)
    {

        if ($transfer['data']['status'] === 'SUCCESSFUL') {

            $transferReference = $transfer['data']['reference'];

            try {
                $transferItem = InternationalTransfer::firstWhere('reference', $transferReference);

                if (!$transferItem) {
                    return response()->json(['status' => 'success'], 200);
                }

                if ($transferItem->status !== InternationalTransfer::STATUS_PENDING) {
                    return response()->json(['status' => 'success'], 200);
                }

                DB::beginTransaction();

                $transferItem->update([
                    'status' => InternationalTransfer::STATUS_SUCCESS
                ]);

                DB::commit();
            } catch (\Throwable $th) {
                Log::channel('ajo-savings')->error($th->getMessage());
            }
        }

        if ($transfer['data']['status'] === 'FAILED') {
            $transferReference = $transfer['data']['reference'];

            try {
                $transferItem = InternationalTransfer::firstWhere('reference', $transferReference);

                if (!$transferItem) {
                    return response()->json(['status' => 'success'], 200);
                }

                if ($transferItem->status !== InternationalTransfer::STATUS_PENDING) {
                    return response()->json(['status' => 'success'], 200);
                }

                DB::beginTransaction();

                $transferItem->update([
                    'status' => InternationalTransfer::STATUS_FAILED
                ]);

                $totalAmount = $transferItem->amount_deducted_in_local_currency;


                $transferItem->sender()->increment('wallet_balance', $totalAmount);

                $transferItem->sender->transactions()->create([
                    'type' => UserTransaction::TYPE_CREDIT,
                    'amount' => $totalAmount,
                    'source' => UserTransaction::SOURCE_BANK_TRANSFER,
                    'message' => "₦{$totalAmount} was reversed to your account",
                    'status' => UserTransaction::STATUS_SUCCESSFUL,

                ]);

                DB::commit();
            } catch (\Throwable $th) {
                Log::channel('ajo-savings')->error($th->getMessage());
            }
        }
    }

    public function handlePaystack(Request $request)
    {
        $secretKey = config('paystack.secret_key') ?? env('PAYSTACK_SECRET_KEY');
        $signature = $request->header('x-paystack-signature');

        Log::info('WebhookController: Received Paystack webhook', [
            'event' => $request->input('event'),
            'has_signature' => !empty($signature),
        ]);

        if (!$signature || $signature !== hash_hmac('sha512', $request->getContent(), $secretKey)) {
            Log::warning('WebhookController: Paystack webhook invalid signature');
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $event = $request->input('event');

        if ($event === 'charge.success') {
            $data = $request->input('data');
            $reference = $data['reference'] ?? null;
            $email = $data['customer']['email'] ?? null;
            $amount = ($data['amount'] ?? 0) / 100;

            Log::info('WebhookController: Processing Paystack charge.success webhook', [
                'reference' => $reference,
                'email'     => $email,
                'amount'    => $amount,
            ]);

            if ($reference && $email && $amount > 0) {
                // Check if this reference belongs to a GatewayTransaction (Payment Link)
                $gwRef = $data['metadata']['gateway_reference'] ?? null;
                $gatewayTx = null;
                if ($gwRef) {
                    $gatewayTx = \App\Models\GatewayTransaction::where('reference', $gwRef)->first();
                }
                if (!$gatewayTx && $reference) {
                    $gatewayTx = \App\Models\GatewayTransaction::where('provider_reference', $reference)->orWhere('reference', $reference)->first();
                }

                if ($gatewayTx) {
                    if ($gatewayTx->status !== 'success') {
                        DB::beginTransaction();
                        try {
                            $gatewayTx->update([
                                'status' => 'success',
                                'paid_at' => now(),
                                'raw_provider_webhook' => $data,
                            ]);

                            $amountNgn = $gatewayTx->amount / 100;
                            $chargeKobo = $gatewayTx->fee;
                            $chargeNgn = $chargeKobo / 100;

                            $merchant = $gatewayTx->merchant;
                            if ($merchant) {
                                $merchant->wallet_balance += $gatewayTx->amount;
                                if ($gatewayTx->charge_mode === 'merchant') {
                                    $merchant->wallet_balance -= $chargeKobo;
                                }
                                $merchant->save();

                                $merchant->transactions()->create([
                                    'amount' => $gatewayTx->amount,
                                    'type' => UserTransaction::TYPE_CREDIT,
                                    'status' => UserTransaction::STATUS_SUCCESSFUL,
                                    'source' => UserTransaction::SOURCE_BANK_TRANSFER,
                                    'message' => 'Gateway payment received: ₦' . number_format($amountNgn, 2),
                                    'reference' => $gatewayTx->reference,
                                ]);

                                if ($chargeKobo > 0 && $gatewayTx->charge_mode === 'merchant') {
                                    $merchant->transactions()->create([
                                        'amount' => $chargeKobo,
                                        'type' => UserTransaction::TYPE_DEBIT,
                                        'status' => UserTransaction::STATUS_SUCCESSFUL,
                                        'source' => UserTransaction::SOURCE_BANK_TRANSFER,
                                        'message' => 'Gateway payment charge: ₦' . number_format($chargeNgn, 2),
                                        'reference' => $gatewayTx->reference,
                                        'fee' => $chargeKobo,
                                    ]);
                                }
                            }

                            DB::commit();
                            (new \App\Services\GatewayWebhookService())->dispatch($gatewayTx, 'payment.success');

                            Log::info('WebhookController: GatewayTransaction credited via Paystack webhook', [
                                'gateway_reference' => $gatewayTx->reference,
                                'amount' => $amount,
                            ]);
                        } catch (\Throwable $e) {
                            DB::rollBack();
                            Log::error('WebhookController Paystack Gateway error: ' . $e->getMessage());
                        }
                    }
                } else {
                    DB::beginTransaction();
                    try {
                        $existingTx = \App\Models\PaymentTransaction::where('transaction_reference', $reference)->first();
                        if (!$existingTx) {
                            $user = User::where('email', $email)->first();
                            if ($user) {
                                \App\Models\PaymentTransaction::create([
                                    'transaction_id' => $data['id'],
                                    'transaction_reference' => $reference,
                                    'user_id' => $user->id,
                                    'amount' => $amount,
                                    'currency' => $data['currency'] ?? 'NGN',
                                    'charge_fee' => ($data['fees'] ?? 0) / 100,
                                    'payment_method' => 'paystack_card',
                                ]);

                                $user->increment('wallet_balance', $amount);

                                $user->transactions()->create([
                                    'amount' => $amount,
                                    'type' => UserTransaction::TYPE_CREDIT,
                                    'message' => "Funded ₦" . number_format($amount, 2) . " via Paystack Card",
                                    'status' => UserTransaction::STATUS_SUCCESSFUL,
                                    'source' => UserTransaction::SOURCE_WALLET_FUNDED,
                                    'reference' => $reference,
                                ]);

                                Log::info('WebhookController: Wallet credited via Paystack webhook', [
                                    'user_id'   => $user->id,
                                    'reference' => $reference,
                                    'amount'    => $amount,
                                ]);
                            } else {
                                Log::warning('WebhookController: User not found for email ' . $email, ['reference' => $reference]);
                            }
                        } else {
                            Log::info('WebhookController: Duplicate Paystack webhook ignored for reference ' . $reference);
                        }
                        DB::commit();
                    } catch (\Throwable $e) {
                        DB::rollBack();
                        Log::error('Paystack webhook error: ' . $e->getMessage(), ['reference' => $reference]);
                    }
                }
            }
        }

        return response()->json(['status' => 'success'], 200);
    }
}

