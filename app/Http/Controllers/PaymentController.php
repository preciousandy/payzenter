<?php

namespace App\Http\Controllers;

use App\Models\PaymentTransaction;
use App\Models\Product;
use App\Models\UserTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use KingFlamez\Rave\Facades\Rave as Flutterwave;

class PaymentController extends Controller
{
    public function redirectToGateway()
    {
        request()->validate([
            'amount' => 'required|integer|min:100'
        ]);


        //This generates a payment reference
        $reference = Flutterwave::generateReference();

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
