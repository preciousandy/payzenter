<?php

namespace App\Http\Controllers;

use App\Helpers\SettingHelper;
use App\Models\BuyFxOrder;
use App\Models\ExchangeRate;
use App\Models\ForeignExchangeSetting;
use App\Models\FxFormField;
use App\Models\SellFxOrder;
use App\Models\UserTransaction;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class BureauDeChangeController extends Controller
{
    use ApiResponseTrait;

    public function buyPage(Request $request)
    {
        try {
            $data['currencies'] = ExchangeRate::orderByStatusOpenFirst()->get();

            $data['history'] = BuyFxOrder::where('user_id', auth()->id())->latest()->get();

            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }


    public function buyCurrencyPage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'currency_id' => 'required|exists:exchange_rates,id',
        ]);


        if ($validator->fails()) {
            return $this->validationError($validator);
        }


        try {
            $currency = ExchangeRate::find($request->currency_id);

            $data['fee'] = (float) SettingHelper::get(SettingHelper::BUREAU_DA_CHANGE_BUY_FEE, 0);

            $data['currency'] = $currency;

            // Get fields specific to this currency for buy form
            $data['form_fields'] = FxFormField::forFormType('buy')
                ->forCurrency($currency->code)
                ->select('id', 'label', 'type', 'required')
                ->get()
                ->toArray();

            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }


    public function submitBuyFxOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'currency_id' => 'required|exists:exchange_rates,id',
            'currency_amount' => 'required|numeric|min:1',
            'data' => 'required|array',
        ]);


        if ($validator->fails()) {
            return $this->validationError($validator);
        }


        $currency = ExchangeRate::find($request->currency_id);

        $naira_amount = $currency->price * $request->currency_amount;

        $fee = (float) SettingHelper::get(SettingHelper::BUREAU_DA_CHANGE_BUY_FEE, 0);

        $fee_amount = getPercentageAmount($naira_amount, $fee);

        $total_amount = $naira_amount + $fee_amount;

        if ($total_amount > auth()->user()->wallet_balance) {
            return $this->error('Insufficient balance');
        }

        try {

            DB::beginTransaction();

            $order_number = $currency->code . now()->format('YmdHis');

            // Process the order
            $order = BuyFxOrder::create([
                'user_id' => auth()->id(),
                'currency_name' => $currency->currency,
                'currency_code' => $currency->code,
                'amount' => $request->currency_amount,
                'naira_amount' => $total_amount,
                'status' => BuyFxOrder::STATUS_PENDING,
                'order_number' => $order_number,
                'data' => $request->data
            ]);

            // Get form fields for this currency
            $form_fields = FxFormField::forFormType('buy')
                ->forCurrency($currency->code)
                ->get()
                ->toArray();

            $form_fields_data = $request->data;

            foreach ($form_fields as $field) {
                if ($field['type'] == 'file' && isset($form_fields_data[$field['label']]) && $form_fields_data[$field['label']]) {
                    $path = $form_fields_data[$field['label']]->store('foreign Exchange');

                    $form_fields_data[$field['label']] = $path;
                }
            }
            $order->data = $form_fields_data;

            $order->save();

            auth()->user()->decrement('wallet_balance', $total_amount);

            $formattedAmount = number_format($request->currency_amount, 2);
            $formattedTotalAmount = number_format($total_amount, 2);

            $transaction_message = "You bought {$formattedAmount} {$currency->currency} for N{$formattedTotalAmount}";

            if ($request->data && count($request->data) > 0 && isset($request->data['Account Name']) && isset($request->data['Account Number']) && isset($request->data['Bank Name'])) {
                $transaction_message .= " (Account Name: {$request->data['Account Name']}, Account Number: {$request->data['Account Number']}, Bank: {$request->data['Bank Name']})";
            }

            auth()->user()->transactions()->create([
                'type' => UserTransaction::TYPE_DEBIT,
                'amount' => $total_amount,
                'message' => $transaction_message,
                'source' => UserTransaction::SOURCE_BUREAU_DA_CHANGE,
                'status' => UserTransaction::STATUS_SUCCESSFUL
            ]);

            DB::commit();

            return $this->success($order, 'Order submitted successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function sellPage(Request $request)
    {
        try {
            $data['currencies'] = ExchangeRate::orderBySellStatusOpenFirst()->get();

            $data['history'] = SellFxOrder::where('user_id', auth()->id())->latest()->get();

            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function sellCurrencyPage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'currency_id' => 'required|exists:exchange_rates,id',
        ]);


        if ($validator->fails()) {
            return $this->validationError($validator);
        }


        try {
            $currency = ExchangeRate::find($request->currency_id);

            $data['fee'] = (float) SettingHelper::get(SettingHelper::BUREAU_DA_CHANGE_SELL_FEE, 0);

            $data['currency'] = $currency->makeHidden('banks');

            // Get fields specific to this currency for sell form
            $data['form_fields'] = FxFormField::forFormType('sell')
                ->forCurrency($currency->code)
                ->select('id', 'label', 'type', 'required')
                ->get()
                ->toArray();

            $data['banks'] = $currency->banks;

            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function submitSellFxOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'currency_id' => 'required|exists:exchange_rates,id',
            'currency_amount' => 'required|numeric|min:1',
            'data' => 'required|array',
        ]);


        if ($validator->fails()) {
            return $this->validationError($validator);
        }



        $currency = ExchangeRate::find($request->currency_id);

        $naira_amount = $currency->price * $request->currency_amount;

        $fee = (float) SettingHelper::get(SettingHelper::BUREAU_DA_CHANGE_SELL_FEE, 0);

        $fee_amount = getPercentageAmount($naira_amount, $fee);

        $total_amount = $naira_amount - $fee_amount;



        try {

            DB::beginTransaction();

            $order_number = $currency->code . now()->format('YmdHis');

            // Process the order
            $order = SellFxOrder::create([
                'user_id' => auth()->id(),
                'currency_name' => $currency->currency,
                'currency_code' => $currency->code,
                'amount' => $request->currency_amount,
                'naira_amount' => $total_amount,
                'status' => BuyFxOrder::STATUS_PENDING,
                'order_number' => $order_number,
                'data' => $request->data
            ]);

            // Get form fields for this currency
            $form_fields = FxFormField::forFormType('sell')
                ->forCurrency($currency->code)
                ->get()
                ->toArray();

            $form_fields_data = $request->data;

            foreach ($form_fields as $field) {
                if ($field['type'] == 'file') {
                    if (isset($form_fields_data[$field['label']]) && $form_fields_data[$field['label']]) {
                        $path = $form_fields_data[$field['label']]->store('foreign Exchange');

                        $form_fields_data[$field['label']] = $path;
                        $order->data = $form_fields_data;
                    }
                }
            }

            $order->save();

            DB::commit();

            return $this->success($order, 'Sell Currency order submitted successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
