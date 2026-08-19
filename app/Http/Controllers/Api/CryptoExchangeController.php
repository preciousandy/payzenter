<?php

namespace App\Http\Controllers\Api;

use App\Helpers\SettingHelper;
use App\Http\Controllers\Controller;
use App\Models\BuyCryptoOrder;
use App\Models\CryptoAddress;
use App\Models\CryptoCurrency;
use App\Models\SellCryptoOrder;
use App\Models\UserTransaction;
use App\Services\CryptoService;
use App\Services\UserService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CryptoExchangeController extends Controller
{
    use ApiResponseTrait;

    public function buyPage(Request $request)
    {
        try {
            $allCryptoCurrencies = CryptoCurrency::all();

            $quidaxSupportedCurrencies = getQuidaxSupportedCryptos();

            $cryptoCurrencies = [];

            foreach ($allCryptoCurrencies as $currency) {
                if (in_array($currency->symbol, $quidaxSupportedCurrencies)) {
                    $cryptoCurrencies[] = $currency;
                }
            }

            $res = (new CryptoService)->getCurrencySwapRate(strtolower('usdt'));

            $dollarRate = round($res['data']['quoted_price'], 2);

            $history = BuyCryptoOrder::whereUserId(auth()->id())->latest()->get();

            $data = [
                'dollarRate' => (float) $dollarRate,
                'currencies' => $cryptoCurrencies,
                'history' => $history,
            ];

            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function buyCryptoPage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'crypto_id' => 'required|exists:crypto_currencies,id',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $currency = CryptoCurrency::find($request->crypto_id);

            $data['currency'] = $currency;

            $data['swiftpay_fee'] = (float) SettingHelper::get(SettingHelper::BUY_CRYPTO_FEE, 0);

            $data['quidax_wallet_address'] = CryptoAddress::where('user_id', auth()->id())->where('currency_code', strtolower($currency->symbol))->first();

            if (!auth()->user()->quidax_id) {
                return $this->error('You do not have a crypto wallet account. Pls create one first');
            }

            $response = (new UserService)->getQuidaxCurrencyWallet(auth()->user()->quidax_id, strtolower($currency->symbol));

            if ($response['status'] == 'error') {
                return $this->error('quidax wallet error-' . $response['message']);
            }

            $res = (new CryptoService)->getCurrencySwapRate(strtolower($currency->symbol));

            if ($res['status'] == 'error') {
                return $this->error('swap rate error-' . $res['message']);
            }


            $data['naira_rate'] = (float) round($res['data']['quoted_price'], 2);


            $data['quidax_wallet'] = $response['data'];


            $data['networks'] = collect($data['quidax_wallet']['networks'])->map(function ($net) use ($currency) {
                $address = CryptoAddress::where('user_id', auth()->id())
                    ->where('currency_code', strtolower($currency->symbol))
                    ->where('network', $net['id'])
                    ->first();

                $net['payment_address'] = $address ? $address->address : null;
                $net['destination_tag'] = $address ? $address->destination_tag : null;

                return $net;
            })->toArray();

            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }


    public function generatePaymentAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'network' => 'required|string',
            'currency_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $response = (new UserService)->getQuidaxCurrencyPaymentAddress(auth()->user()->quidax_id, $request->network, strtolower($request->currency_code));


            if ($response['status'] == 'error') {
                throw new \Exception($response['message']);
            }

            return $this->success([], "Payment address is being generated. Please check back in a few seconds");
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function createBuyOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'crypto_amount' => 'required|numeric',
            'amount_in_naira' => 'required|numeric',
            // 'swiftpay_fee' => 'required|numeric',
            'currency_code' => 'required|string',
            'crypto_network' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {

            $swiftpay_fee = SettingHelper::get(SettingHelper::BUY_CRYPTO_FEE, 0);

            $data['fee_amount'] = getPercentageAmount($request->amount_in_naira, $swiftpay_fee);

            $data['sub_total'] = $request->amount_in_naira + $data['fee_amount'];

            $res = (new CryptoService)->getCryptoFees(strtolower($request->currency_code), $request->crypto_network, $request->crypto_amount);

            if ($res['status'] == 'error') {
                return $this->error($res['message']);
            }

            $data['crypto_fee'] = $res['data'];

            return $this->success($data, "Crypto fees fetched successfully");
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }


    public function submitBuyOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'crypto_amount' => 'required|numeric',
            'amount_in_naira' => 'required|numeric',
            'crypto_fee_in_naira' => 'required|numeric',
            'total' => 'required|numeric',
            'currency_id' => 'required|integer|exists:crypto_currencies,id',
            'crypto_network' => 'required|string',
            'wallet_address' => 'required|string',
            'destination_tag' => 'nullable|string',
            'crypto_fee' => 'required|numeric',
            'crypto_rate' => 'required|numeric',
        ]);

        // validation error
        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $swiftpay_fee = SettingHelper::get(SettingHelper::BUY_CRYPTO_FEE, 0);

        $total = $request->amount_in_naira + $request->crypto_fee_in_naira + getPercentageAmount($request->amount_in_naira, $swiftpay_fee);

        if ($total > auth()->user()->wallet_balance) {
            return $this->error('Insufficient funds');
        }



        try {
            $cryptoService = new CryptoService;

            $cryptoCurrency = CryptoCurrency::find($request->currency_id);



            $swapQuotation = $cryptoService->generateSwapQuotation(strtolower($cryptoCurrency->symbol), $request->amount_in_naira + $request->crypto_fee_in_naira  + BuyCryptoOrder::ALLOWANCE_AMOUNT);

            if ($swapQuotation['status'] == 'error') {
                return $this->error($swapQuotation['message']);
            }

            $quotationId = $swapQuotation['data']['id'];


            $swapOrder = $cryptoService->confirmSwapOrder($quotationId);

            if ($swapOrder['status'] == 'error') {
                return $this->error($swapOrder['message']);
            }


            DB::beginTransaction();

            $crypto_order =   BuyCryptoOrder::create(
                [
                    'user_id' => auth()->id(),
                    'crypto_name' => $cryptoCurrency->name,
                    'crypto_symbol' => strtolower($cryptoCurrency->symbol),
                    'amount_in_naira' => (int) floor($request->amount_in_naira + $request->crypto_fee_in_naira),
                    'amount_in_crypto' => $request->crypto_amount,
                    'wallet_address' => $request->wallet_address,
                    'crypto_network' => $request->crypto_network,
                    'quotation_id' => $quotationId,
                    'destination_tag' => $request->destination_tag,
                    'status' => BuyCryptoOrder::STATUS_PENDING,
                    'crypto_fee' => $request->crypto_fee,
                    'crypto_rate' => $request->crypto_rate,
                ]
            );

            auth()->user()->decrement('wallet_balance', $total);

            auth()->user()->transactions()->create([
                'type' => UserTransaction::TYPE_DEBIT,
                'amount' => $total,
                'message' => "Bought {$request->crypto_amount} worth of {$cryptoCurrency->symbol} for {$request->amount_in_naira} NGN",
                'source' => UserTransaction::SOURCE_BUY_CRYPTO,
                'status' => UserTransaction::STATUS_SUCCESSFUL,
                'fee' => $swiftpay_fee
            ]);

            DB::commit();

            return $this->success($crypto_order, "Crypto order placed successfully");
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function sellPage(Request $request)
    {
        try {
            $allCryptoCurrencies = CryptoCurrency::all();

            $quidaxSupportedCurrencies = getQuidaxSupportedCryptos();

            $cryptoCurrencies = [];

            foreach ($allCryptoCurrencies as $currency) {
                if (in_array($currency->symbol, $quidaxSupportedCurrencies)) {
                    $cryptoCurrencies[] = $currency;
                }
            }

            $res = (new CryptoService)->getCurrencySwapRate(strtolower('usdt'));

            $dollarRate = round($res['data']['quoted_price'], 2);

            $history = SellCryptoOrder::whereUserId(auth()->id())->latest()->get();

            $data = [
                'dollarRate' => (float) $dollarRate,
                'currencies' => $cryptoCurrencies,
                'history' => $history,
            ];

            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function sellCryptoPage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'crypto_id' => 'required|exists:crypto_currencies,id',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $currency = CryptoCurrency::find($request->crypto_id);

            $data['currency'] = $currency;

            $data['swiftpay_fee'] = (float) SettingHelper::get(SettingHelper::SELL_CRYPTO_FEE, 0);

            $data['quidax_wallet_address'] = CryptoAddress::where('user_id', auth()->id())->where('currency_code', strtolower($currency->symbol))->first();

            $response = (new UserService)->getQuidaxCurrencyWallet(auth()->user()->quidax_id, strtolower($currency->symbol));

            if ($response['status'] == 'error') {
                return $this->error($response['message']);
            }

            $res = (new CryptoService)->getCurrencySwapRate('ngn', strtolower($currency->symbol));

            if ($res['status'] == 'error') {
                return $this->error($res['message']);
            }


            $data['naira_rate'] = (float) round($res['data']['quoted_price'], 2);


            $data['quidax_wallet'] = $response['data'];


            $data['networks'] = collect($data['quidax_wallet']['networks'])->map(function ($net) use ($currency) {
                $address = CryptoAddress::where('user_id', auth()->id())
                    ->where('currency_code', strtolower($currency->symbol))
                    ->where('network', $net['id'])
                    ->first();

                $net['payment_address'] = $address ? $address->address : null;
                $net['destination_tag'] = $address ? $address->destination_tag : null;

                return $net;
            })->toArray();

            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function submitSellOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'crypto_amount' => 'required|numeric',
            'amount_in_naira' => 'required|numeric',
            'total' => 'required|numeric',
            'currency_id' => 'required|integer|exists:crypto_currencies,id',
            'crypto_rate' => 'required|numeric',
        ]);


        if ($request->total > auth()->user()->wallet_balance) {
            $validator->errors()->add('total', 'Insufficient funds');
            return $this->validationError($validator);
        }


        try {
            $cryptoService = new CryptoService;

            $cryptoCurrency = CryptoCurrency::find($request->currency_id);


            // $swapQuotation = $cryptoService->generateUserSwapQuotation(auth()->user()->quidax_id, strtolower($cryptoCurrency->symbol), $request->crypto_amount);

            // if ($swapQuotation['status'] == 'error') {
            //     return $this->error($swapQuotation['message']);
            // }

            // $quotationId = $swapQuotation['data']['id'];

            // $swapOrder = $cryptoService->confirmUserSwapOrder(auth()->user()->quidax_id, $quotationId);

            // if ($swapOrder['status'] == 'error') {
            //     return $this->error($swapOrder['message']);
            // }

            $uniqueId = 'SELL_CRYPTO' . uniqid();

            $res = $cryptoService->getCryptoFromUser(auth()->user()->quidax_id, strtolower($cryptoCurrency->symbol), $request->crypto_amount, $uniqueId);

            if ($res['status'] == 'error') {
                return $this->error($res['message']);
            }


            $order =  SellCryptoOrder::create([
                'user_id' => auth()->id(),
                'crypto_name' => $cryptoCurrency->name,
                'crypto_symbol' => strtolower($cryptoCurrency->symbol),
                'amount_in_naira' => $request->total,
                'amount_in_crypto' => $request->crypto_amount,
                'quotation_id' => $uniqueId,
                'status' => SellCryptoOrder::STATUS_PENDING,
                'crypto_rate' => $request->crypto_rate,
            ]);

            return $this->success($order, "Sell order placed successfully");
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
