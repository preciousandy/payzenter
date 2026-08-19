<?php

namespace App\Http\Controllers;

use App\Models\BuyCryptoOrder;
use App\Models\CryptoAddress;
use App\Models\SellCryptoOrder;
use App\Models\User;
use App\Models\UserTransaction;
use App\Notifications\BuyCryptoOrderCompleted;
use App\Notifications\SellCryptoOrderCompleted;
use App\Services\CryptoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CryptoWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->all();
        $event = $payload['event'];

        // Log::channel('my-logs')->info($request);

        switch ($event) {
            case 'wallet.address.generated':
                $this->handleTransactionCreated($payload['data']);
                break;

            case 'swap_transaction.completed':
                $this->handleSwapTransactionCompleted($payload['data']);
                break;

            case 'withdraw.successful':
                $this->handleWithdrawalTransactionCompleted($payload['data']);
                break;

            case 'withdraw.failed':
                $this->handleWithdrawalFailed($payload['data']);
                break;

            default:
                return response()->json(['message' => 'Webhook received'], 200);
        }

        // return response()->json(['message' => 'Webhook received'], 200);
    }

    public function handleTransactionCreated($data)
    {
        $userEmail = $data['user']['email'];

        $user = User::where('quidax_email', $userEmail)->first();

        CryptoAddress::create([
            'user_id' => $user->id,
            'currency_code' => $data['currency'],
            'address' => $data['address'],
            'network' => $data['network'],
            'destination_tag' => $data['destination_tag'] ?? null,
        ]);
    }

    public function handleSwapTransactionCompleted($data)
    {
        if ($data['from_currency'] == 'NGN') {
            $this->handleBuyCrypto($data);
            return;
        }


        if ($data['to_currency'] == 'NGN') {
            $this->handleSellCrypto($data);
            return;
        }
    }

    public function handleWithdrawalTransactionCompleted($data)
    {

        $quotationId = $data['transaction_note'];
        $transaction_type = $data['narration'];



        if ($transaction_type == 'buy-crypto') {
            $this->handleBuyCryptoSuccess($quotationId);
            return;
        }

        if ($transaction_type == 'sell-crypto') {
            $this->handleSellCryptoSuccess($quotationId);
            return;
        }
    }

    public function handleWithdrawalFailed($data)
    {

        $quotationId = $data['transaction_note'];
        $transaction_type = $data['narration'];

        if ($transaction_type == 'buy-crypto') {
            $order = BuyCryptoOrder::where('quotation_id', $quotationId)->first();

            DB::beginTransaction();

            $order->update(['status' => BuyCryptoOrder::STATUS_FAILED]);

            $order->user->update([
                'wallet_balance' => $order->user->wallet_balance + $order->amount_in_naira
            ]);

            $order->user->transactions()->create([
                'amount' => $order->amount_in_naira,
                'type' => UserTransaction::TYPE_CREDIT,
                'message' => 'Buy crypto failed ',
                'status' => UserTransaction::STATUS_SUCCESSFUL,
                'source' => UserTransaction::SOURCE_BUY_CRYPTO
            ]);

            DB::commit();

            $this->handleSwapBackCryptotoNGN($order->crypto_symbol, $order->amount_in_crypto    + $order->crypto_fee);
        }
    }



    public function handleBuyCryptoSuccess($quotationId)
    {
        $order = BuyCryptoOrder::where('quotation_id', $quotationId)->first();

        Log::channel('my-logs')->info($order);


        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        if ($order->status !== BuyCryptoOrder::STATUS_PROCESSING) {
            return response()->json(['message' => 'Order already completed'], 400);
        }

        $order->update(['status' => BuyCryptoOrder::STATUS_COMPLETED]);

        $order->user->notify(new BuyCryptoOrderCompleted);

        Log::channel('my-logs')->info('Order completed');

        return response()->json([], 200);
    }


    public function handleBuyCrypto($data)
    {
        $receivedAmount = $data['received_amount'];


        $swapQuotation = $data['swap_quotation'];

        $quotationId = $swapQuotation['id'];

        $order = BuyCryptoOrder::where('quotation_id', $quotationId)->first();

        $amountToSend = $order->amount_in_crypto;

        Log::channel('my-logs')->info($amountToSend);

        // Log::channel('my-logs')->info($order);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $cryptoService = new CryptoService();

        $res = $cryptoService->sendCryptoToUser(strtolower($order->crypto_symbol), $amountToSend, $order->wallet_address, $order->crypto_network, $quotationId, $order->destination_tag);

        if ($res['status'] == 'error') {
            Log::channel('my-logs')->error($res['message']);

            DB::beginTransaction();

            $order->update(['status' => BuyCryptoOrder::STATUS_FAILED]);

            $order->user->update([
                'wallet_balance' => $order->user->wallet_balance + $order->amount_in_naira
            ]);

            $order->user->transactions()->create([
                'amount' => $order->amount_in_naira,
                'type' => UserTransaction::TYPE_CREDIT,
                'message' => 'Buy crypto failed and the amount has been refunded to your wallet',
                'status' => UserTransaction::STATUS_SUCCESSFUL,
                'source' => UserTransaction::SOURCE_BUY_CRYPTO
            ]);

            DB::commit();

            $this->handleSwapBackCryptotoNGN($order->crypto_symbol, $amountToSend + $order->crypto_fee);

            return response()->json(['message' => $res['message']], 300);
        }

        $order->update(['status' => BuyCryptoOrder::STATUS_PROCESSING]);
    }

    public function handleSwapBackCryptotoNGN($currency_symbol, $amount)
    {
        // note: "me" is for main account.
        $cryptoService = new CryptoService;

        $swapQuotation = $cryptoService->generateUserSwapQuotation("me", strtolower($currency_symbol), $amount);

        if ($swapQuotation['status'] == 'error') {
            Log::channel('my-logs')->error('Swap back error: ' . $swapQuotation['message']);
        }

        $quotationId = $swapQuotation['data']['id'];

        $swapOrder = $cryptoService->confirmUserSwapOrder("me", $quotationId);

        if ($swapOrder['status'] == 'error') {
            Log::channel('my-logs')->error('Confirm Swap back order error: ' . $swapOrder['message']);
            // return response()->json(['message' => $swapOrder['message']], 300);
        }
    }

    public function handleSellCrypto($data)
    {
        $receivedAmount = $data['received_amount'];


        $swapQuotation = $data['swap_quotation'];

        $quotationId = $swapQuotation['id'];

        $order = SellCryptoOrder::where('quotation_id', $quotationId)->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $cryptoService = new CryptoService();

        $res = $cryptoService->getCryptoFromUser($order->user->quidax_id, 'ngn', $receivedAmount, $quotationId);

        if ($res['status'] == 'error') {
            Log::channel('my-logs')->error($res['message']);
            return response()->json(['message' => $res['message']], 300);
        }

        $order->update(['status' => SellCryptoOrder::STATUS_PROCESSING]);

        Log::channel('my-logs')->info('order processing');
    }

    public function handleSellCryptoSuccess($quotationId)
    {
        $order = SellCryptoOrder::where('quotation_id', $quotationId)->first();

        // Log::channel('my-logs')->info($order);


        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        if ($order->status !== SellCryptoOrder::STATUS_PENDING) {
            return response()->json(['message' => 'Order already completed'], 400);
        }

        Log::channel('my-logs')->info('Processing order');


        try {
            DB::beginTransaction();

            $order->update(['status' => SellCryptoOrder::STATUS_COMPLETED]);

            $order->user->increment('wallet_balance', $order->amount_in_naira);

            $actualAmount = $order->crypto_rate ? $order->crypto_rate * $order->amount_in_crypto : 0;

            $fee = $actualAmount ? $actualAmount - $order->amount_in_naira : 0;

            UserTransaction::create([
                'user_id' => $order->user_id,
                'amount' => $order->amount_in_naira,
                'type' => UserTransaction::TYPE_CREDIT,
                'message' => 'Crypto sale of ' . $order->crypto_name . ' completed',
                'status' => UserTransaction::STATUS_SUCCESSFUL,
                'source' => UserTransaction::SOURCE_SELL_CRYPTO,
                'fee' => $fee
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('my-logs')->error('Sell crypto success error: ' . $e->getMessage());
            return response()->json(['message' => 'Error processing sell crypto order'], 500);
        }


        $order->user->notify(new SellCryptoOrderCompleted($order->amount_in_naira));

        Log::channel('my-logs')->info('Done and dusted');

        return response()->json([], 200);
    }
}
