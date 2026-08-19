<?php

namespace App\Http\Controllers\Api;

use App\Helpers\SettingHelper;
use App\Http\Controllers\Controller;
use App\Models\ReloadlyCountry;
use App\Models\UserTransaction;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class GiftCardsController extends Controller
{
    use ApiResponseTrait;

    public function getCountries(Request $request)
    {
        try {

            $data = ReloadlyCountry::orderBy('name')
                ->get();

            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }


    public function getProducts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country_code' => 'required|string',
        ]);


        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $accessToken = getGiftcardsAccessToken();

            if (!$accessToken) {
                throw new \Exception('Failed to retrieve access token for gift cards. Please try again later.');
            }

            $res =   Http::withHeaders([
                'Accept' => 'application/com.reloadly.giftcards-v1+json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $accessToken
            ])->get(env("RELOADLY_API_URL") . "/countries/" . $request->country_code . "/products");

            $data = $res->json();

            if (isset($data['errorCode'])) {
                throw new \Exception($data['message']);
            }


            return $this->success($data, 'Products fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function showProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',
        ]);


        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $accessToken = getGiftcardsAccessToken();

            if (!$accessToken) {
                throw new \Exception('Failed to retrieve access token for gift cards. Please try again later.');
            }

            $res =   Http::withHeaders([
                'Accept' => 'application/com.reloadly.giftcards-v1+json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $accessToken
            ])->get(env("RELOADLY_API_URL") . "/products/" . $request->product_id);

            $product = $res->json();

            if (isset($data['errorCode'])) {
                throw new \Exception($data['message']);
            }

            $data['fee'] = (float) SettingHelper::get(SettingHelper::BUY_GIFT_CARD_FEE, 0);

            $data['product'] = $product;


            return $this->success($data, 'Product fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }


    public function checkBuyPrice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',
            'amount' => 'required|numeric',
            'quantity' => 'required|integer',
        ]);


        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $accessToken = getGiftcardsAccessToken();

            if (!$accessToken) {
                throw new \Exception('Failed to retrieve access token for gift cards. Please try again later.');
            }

            $res =   Http::withHeaders([
                'Accept' => 'application/com.reloadly.giftcards-v1+json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $accessToken
            ])->get(env("RELOADLY_API_URL") . "/products/" . $request->product_id);

            $product = $res->json();

            if (isset($data['errorCode'])) {
                throw new \Exception($data['message']);
            }

            $fee = (float) SettingHelper::get(SettingHelper::BUY_GIFT_CARD_FEE, 0);

            $price = 0;

            if ($product['denominationType'] == 'RANGE' && $request->amount < $product['minRecipientDenomination']) {
                return $this->error('Amount must be greater than ' . $product['minRecipientDenomination']);
            }

            if ($product['denominationType'] == 'RANGE' && $request->amount > $product['maxRecipientDenomination']) {
                return $this->error('Amount must be less than ' . $product['maxRecipientDenomination']);
            }

            if ($product['denominationType'] == 'FIXED') {
                $price = $product['fixedRecipientToSenderDenominationsMap'][$request->amount] * $request->quantity + $product['senderFee'];
            } else {
                $conversionRate = $product['minSenderDenomination'] / $product['minRecipientDenomination'];
                $price = $request->amount * $conversionRate * $request->quantity + $product['senderFee'];
            }


            $data['price'] = $price;

            $data['fee_amount'] = getPercentageAmount($price, $fee);

            $data['total_amount'] =  round($price + $data['fee_amount'], 2);

            return $this->success($data, 'Product price fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function buyGiftCard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',
            'amount' => 'required|numeric',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
            'fee_amount' => 'required|numeric',
            'total_amount' => 'required|numeric',
            'name' => 'required',
            'email' => 'required|email',
        ]);


        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        if ($request->total_amount > auth()->user()->wallet_balance) {
            $validator->errors()->add('total_amount', 'Insufficient funds');
            return $this->validationError($validator);
        }


        try {
            $accessToken = getGiftcardsAccessToken();

            if (!$accessToken) {
                throw new \Exception('Failed to retrieve access token for gift cards. Please try again later.');
            }
            $res =   Http::withHeaders([
                'Accept' => 'application/com.reloadly.giftcards-v1+json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $accessToken
            ])->get(env("RELOADLY_API_URL") . "/products/" . $request->product_id);

            $product = $res->json();

            $data = [
                'productId' => $request->product_id,
                'countryCode' => $product['country']['isoName'],
                'quantity' => $request->quantity,
                'unitPrice' => $request->amount,
                'currencyCode' => $product['recipientCurrencyCode'],
                'customIdentifier' => 'gift-card-' . time(),
                'senderName' => $request->name,
                'recipientEmail' => $request->email,
            ];

            $new_res = Http::withHeaders([
                'Accept' => 'application/com.reloadly.giftcards-v1+json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $accessToken
            ])->post(env("RELOADLY_API_URL") . "/orders", $data);

            $data = $new_res->json();


            if ($res->successful() && !$data['errorCode']) {
                auth()->user()->decrement('wallet_balance', $request->total_amount);

                UserTransaction::create([
                    'user_id' => auth()->id(),
                    'amount' => $request->total_amount,
                    'type' => UserTransaction::TYPE_DEBIT,
                    'message' => 'Bought ' . $request->quantity . ' of ' . $product['productName'] . ' gift card',
                    'status' => UserTransaction::STATUS_SUCCESSFUL,
                    'source' => UserTransaction::SOURCE_GIFT_CARD,
                    'fee' => $request->fee_amount
                ]);


                return $this->success($data, 'Gift card purchased successfully', 200);
            } else {
                throw new \Exception($data['message'] . ' ' . $data['errorCode']);
            }

            return $this->success($data, 'Product price fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
