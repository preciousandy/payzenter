<?php

namespace App\Http\Controllers;

use App\Helpers\SettingHelper;
use App\Models\PaymentTransaction;
use App\Models\Product;
use App\Models\UserTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use KingFlamez\Rave\Facades\Rave as Flutterwave;

use App\Services\PaystackService;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function redirectToPaystackCard(Request $request, PaystackService $paystack)
    {
        $isCardEnabled = (bool) SettingHelper::get(SettingHelper::ENABLE_PAYSTACK_CARD, '1');
        if (!$isCardEnabled) {
            return redirect()->route('user.fund-wallet')->with('error', 'Card payment via Paystack is currently disabled by administrator. Please use Bank Transfer.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:100'
        ]);

        $user = auth()->user();
        $reference = generateIncomingReference();
        $callbackUrl = route('user.paystack.callback');

        Log::info('PaymentController: Initializing Paystack card redirect', [
            'user_id'   => $user->id,
            'email'     => $user->email,
            'amount'    => $request->amount,
            'reference' => $reference,
        ]);

        $result = $paystack->initializeTransaction(
            (float) $request->amount,
            $user->email,
            $reference,
            $callbackUrl,
            ['user_id' => $user->id, 'type' => 'wallet_funding']
        );

        if (!empty($result['status']) && isset($result['data']['authorization_url'])) {
            Log::info('PaymentController: Paystack authorization URL obtained', [
                'user_id'           => $user->id,
                'reference'         => $reference,
                'authorization_url' => $result['data']['authorization_url'],
            ]);
            return redirect($result['data']['authorization_url']);
        }

        Log::error('PaymentController: Paystack initialization failed', [
            'user_id'   => $user->id,
            'reference' => $reference,
            'result'    => $result,
        ]);

        return redirect()->route('user.fund-wallet')->with('error', $result['message'] ?? 'Failed to initialize Paystack Card Payment.');
    }

    public function handlePaystackCallback(Request $request, PaystackService $paystack)
    {
        $reference = $request->query('reference') ?? $request->query('trxref');

        Log::info('PaymentController: Paystack callback received', [
            'reference' => $reference,
            'all_query' => $request->query(),
        ]);

        if (!$reference) {
            Log::warning('PaymentController: Paystack callback missing reference');
            return redirect()->route('user.fund-wallet')->with('error', 'No reference provided for payment verification.');
        }

        $result = $paystack->verifyTransaction($reference);

        if (!empty($result['status']) && isset($result['data']['status']) && $result['data']['status'] === 'success') {
            $data = $result['data'];
            $amount = $data['amount'] / 100; // kobo to NGN

            DB::beginTransaction();
            try {
                // Prevent double crediting
                $existingTx = PaymentTransaction::where('transaction_reference', $reference)->first();
                if (!$existingTx) {
                    $user = auth()->user();
                    
                    PaymentTransaction::create([
                        'transaction_id' => $data['id'],
                        'transaction_reference' => $reference,
                        'user_id' => $user->id,
                        'amount' => $amount,
                        'currency' => $data['currency'] ?? 'NGN',
                        'charge_fee' => ($data['fees'] ?? 0) / 100,
                        'payment_method' => 'paystack_card',
                    ]);

                    $user->lockForUpdate();
                    $user->wallet_balance += $amount;
                    $user->save();

                    $user->transactions()->create([
                        'amount' => $amount,
                        'type' => UserTransaction::TYPE_CREDIT,
                        'message' => "Funded ₦" . number_format($amount, 2) . " via Paystack Card",
                        'status' => UserTransaction::STATUS_SUCCESSFUL,
                        'source' => UserTransaction::SOURCE_WALLET_FUNDED,
                        'reference' => $reference,
                    ]);
                }

                DB::commit();
                Log::info('PaymentController: Paystack card payment verified & wallet funded', [
                    'reference' => $reference,
                    'amount'    => $amount,
                ]);
                return redirect()->route('user.fund-wallet')->with('success', '₦' . number_format($amount, 2) . ' added to your wallet via Paystack Card!');
            } catch (\Throwable $e) {
                DB::rollBack();
                Log::error('PaymentController: Paystack callback error processing payment', [
                    'reference' => $reference,
                    'error'     => $e->getMessage(),
                ]);
                return redirect()->route('user.fund-wallet')->with('error', 'Error processing payment: ' . $e->getMessage());
            }
        }

        Log::warning('PaymentController: Paystack callback transaction verification failed or unsuccessful', [
            'reference' => $reference,
            'result'    => $result,
        ]);

        return redirect()->route('user.fund-wallet')->with('error', 'Paystack Card payment was not successful or was cancelled.');
    }

    public function redirectToGateway()
    {
        request()->validate([
            'amount' => 'required|integer|min:100'
        ]);


        //This generates a payment reference
        $reference = generateIncomingReference();

        // Enter the details of the payment
        $data = [
            'payment_options' => 'card,banktransfer',
            'amount' => request()->amount,
            'email' => auth()->user()->email,
            'tx_ref' => $reference,
            'currency' => "NGN",
            'redirect_url' => route('user.add-money.callback'),
            'customer' => [
                'email' => auth()->user()->email,
                "phone_number" => auth()->user()->phone,
                "name" => auth()->user()->name,
            ],
            "customizations" => [
                "title" => config('app.name'),
                "description" => "Add funds to your wallet"
            ]
        ];

        $payment = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . env('FLW_LIVE_SECRET_KEY')
        ])->post(env('FLW_API_URL') . "/payments", $data);





        if ($payment['status'] !== 'success') {
            // notify something went wrong
            return redirect()->route('profile')->with('error', 'Something went wrong, please try again');
        }

        return redirect($payment['data']['link']);
    }

    public function handleGatewayCallback(Request $request)
    {

        $status = $request->status;

        //if payment is successful
        if ($status ==  'successful') {

            $transactionID = Flutterwave::getTransactionIDFromCallback();
            $data = Flutterwave::verifyTransaction($transactionID);


            if ($data['status'] == 'success') {


                DB::beginTransaction();

                $amount = $data['data']['amount'];

                $transaction = PaymentTransaction::create([
                    'transaction_id' => $data['data']['id'],
                    'transaction_reference' => $data['data']['tx_ref'],
                    'user_id' => auth()->id(),
                    'amount' => $amount,
                    'currency' => $data['data']['currency'],
                    'charge_fee' => $data['data']['app_fee'],
                    'payment_method' => $data['data']['payment_type'],
                ]);

                $user = auth()->user()->lockForUpdate()->first();

                $user->wallet_balance += $amount;

                $user->save();

                // record user transaction
                $user->transactions()->create([
                    'amount' => $amount,
                    'type' => UserTransaction::TYPE_CREDIT,
                    'message' => "You added " . $amount . " to your wallet",
                    'status' => UserTransaction::STATUS_SUCCESSFUL,
                    'source' => UserTransaction::SOURCE_WALLET_FUNDED
                ]);

                DB::commit();   // this will save both records to the database

                return redirect()->route('profile')->with('success', 'Money added to wallet successfully');
            }
        } elseif ($status ==  'cancelled') {
            return redirect()->route('welcome');
        } else {
            //Put desired action/code after transaction has failed here

            return redirect()->route('home')->with('error', "Something went wrong, please try again");
        }
    }

    public function handleFlutterwaveWebhook()
    {
        //
    }
}

