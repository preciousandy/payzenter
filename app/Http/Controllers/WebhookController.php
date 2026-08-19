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
}
