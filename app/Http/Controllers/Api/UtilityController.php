<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CryptoCurrency;
use App\Models\ExchangeRate;
use App\Models\User;
use App\Services\HashIdService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use KingFlamez\Rave\Facades\Rave as Flutterwave;


class UtilityController extends Controller
{
    use ApiResponseTrait;

    public function currencyConversion(Request $request)
    {

        try {
            $exchangeRates = ExchangeRate::all();

            $rates = [];

            $rates['NGN'] = [
                'rate' => 1,
                'logo' => asset('nigeria-flag.png')
            ];

            $exchangeRates->each(function ($item) use (&$rates) {
                $rates[$item->code] = [
                    'rate' => (float) $item->rate,
                    'logo' => $item->logo_url
                ];
            });


            return $this->success($rates, 'Exchange rates fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }


    public function getRates(Request $request)
    {

        try {
            $exchangeRates = ExchangeRate::all();

            $rates = [];

            $exchangeRates->each(function ($item) use (&$rates) {
                $rates[$item->code] = [
                    'rate' => (float) $item->rate,
                    'logo' => $item->logo_url
                ];
            });

            $cryptos = [];

            $cryptocurrencies = CryptoCurrency::limit(5)->get();

            foreach ($cryptocurrencies as $crypto) {
                $cryptos[$crypto->symbol] = [
                    'price_in_usd' => (float) $crypto->price_in_usd,
                    'icon' => $crypto->icon,
                    'name' => $crypto->name,
                ];
            }

            $data['fiats'] = $rates;
            $data['crypto_currencies'] = $cryptos;


            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }


    public function accountLevels(Request $request)
    {

        try {

            $data = [];


            $data['user_current_level'] = auth()->user()->level;


            $data['levels'] =  [
                "green" => [
                    "min" => 0,
                    "max" => 100000
                ],
                "blue" => [
                    "min" => 100000,
                    "max" => 1000000
                ],
                "black" => [
                    "min" => 1000000,
                    "max" => 10000000
                ],
                "gold" => [
                    "min" => 10000000,
                    "max" => null
                ]
            ];


            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function liveChat(Request $request)
    {
        $request->validate([
            'user_hash_id' => 'required|string',
        ]);

        $user = (new HashIdService)->getRecord($request->user_hash_id, User::class);

        Auth::login($user);


        return redirect()->route('live-chat-welcome');
    }

    public function fundWallet(Request $request)
    {

        $request->validate([
            'user_hash_id' => 'required|string',
            'amount' => 'required|numeric|min:100',
        ]);

        $user = (new HashIdService)->getRecord($request->user_hash_id, User::class);

        if (!$user) {
            throw new \Exception('User not found', 404);
        }

        Auth::login($user);

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
            return redirect()->route('home')->with('error', 'Something went wrong, please try again');
        }

        return redirect($payment['data']['link']);
    }
}
