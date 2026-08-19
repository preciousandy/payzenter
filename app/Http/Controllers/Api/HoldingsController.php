<?php

namespace App\Http\Controllers\Api;

use App\Helpers\SettingHelper;
use App\Http\Controllers\Controller;
use App\Models\ExchangeRate;
use App\Models\Holdings;
use App\Models\MetalRate;
use App\Models\UserTransaction;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class HoldingsController extends Controller
{
    use ApiResponseTrait;



    public function index(Request $request)
    {
        try {

            $data['total_assets'] = auth()->user()->holdings()->active()->sum('amount');

            $data['active_holdings'] = Holdings::where('user_id', auth()->id())->active()->latest()->get()->each(function ($holding) {
                if ($holding->asset_type == 'fiat') {
                    $holding->current_rate = ExchangeRate::where('code', $holding->asset_symbol)->first()->rate;
                } elseif ($holding->asset_type == 'metal') {
                    $holding->current_rate = MetalRate::where('metal', $holding->asset_symbol)->first()->price;
                }
            });

            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function assets(Request $request)
    {
        try {

            $data['fiats'] = ExchangeRate::orderBy('currency')->get();

            $data['metals'] = MetalRate::orderBy('metal')->get();

            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function showFiat(Request $request, $code)
    {
        try {

            $currency = ExchangeRate::where('code', $code)->first();

            if (!$currency) {
                return $this->error('Currency not found', 404);
            }

            $data['currency'] = $currency;

            $basePrice = floor($data['currency']->rate);

            $range = 0;

            switch (true) {
                case $basePrice <= 10:
                    $range = 5;
                    break;
                case $basePrice <= 100:
                    $range = 20;
                    break;
                case $basePrice <= 500:
                    $range = 50;
                    break;
                case $basePrice <= 1000:
                    $range = 100;
                    break;
                case $basePrice <= 10000:
                    $range = 150;
                    break;
            }

            $data['price_range']  = generatePriceRange(round($basePrice), 7, $range);

            $data['dates'] = getLastNDays(7);

            if ($request->has('holding_id')) {
                $holding = Holdings::find($request->holding_id);

                if (!$holding) {
                    return $this->error('Holding not found', 404);
                }

                $data['holding'] = $holding;

                $data['current_rate'] = ExchangeRate::where('code', $data['holding']->asset_symbol)->first()->rate;

                $data['possible_earnings'] = $data['holding']->asset_amount * $data['current_rate'];

                $data['earnings'] = $data['holding']->status == Holdings::STATUS_ACTIVE ? $data['possible_earnings'] : $data['holding']->amount_earned;
            }

            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function showMetal(Request $request, $symbol)
    {
        try {

            $metal = MetalRate::where('metal', $symbol)->first();

            if (!$metal) {
                return $this->error('Metal not found', 404);
            }

            $data['currency'] = $metal;

            $basePrice = floor($data['currency']->price);

            $range = 0;

            switch (true) {
                case $basePrice <= 100_000:
                    $range = 5_000;
                    break;
                case $basePrice <= 1_000_000:
                    $range = 50_000;
                    break;
                case $basePrice <= 10_000_000:
                    $range = 500_000;
                    break;
                case $basePrice <= 100_000_000:
                    $range = 5_000_000;
                    break;
                case $basePrice <= 1_000_000_000:
                    $range = 50_000_000;
                    break;
            }

            $data['price_range']  = generatePriceRange(round($basePrice), 7, $range);

            $data['dates'] = getLastNDays(7);

            if ($request->has('holding_id')) {
                $holding = Holdings::find($request->holding_id);

                if (!$holding) {
                    return $this->error('Holding not found', 404);
                }

                $data['holding'] = $holding;

                $data['current_rate'] = MetalRate::where('metal', $data['holding']->asset_symbol)->first()->price;

                $data['possible_earnings'] = $data['holding']->asset_amount * $data['current_rate'];

                $data['earnings'] = $data['holding']->status == Holdings::STATUS_ACTIVE ? $data['possible_earnings'] : $data['holding']->amount_earned;
            }

            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|in:fiat,metal',
            'type_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {

            $data['type'] = $request->type;
            $data['fixed_fee'] = (float) SettingHelper::get(SettingHelper::SAVE_HOLDINGS_FEE);
            $data['percentage_fee'] = (float) SettingHelper::get(SettingHelper::HOLDINGS_PERCENTAGE_FEE);

            if ($request->type == 'fiat') {
                $data['currency'] = ExchangeRate::findOrFail($request->type_id);
            }

            if ($request->type == 'metal') {
                $data['metal'] = MetalRate::findOrFail($request->type_id);
            }

            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'asset_name' => 'required|string',
            'asset_type' => 'required|string|in:fiat,metal',
            'asset_symbol' => 'required|string',
            'asset_price' => 'required|numeric',
            'asset_amount' => 'required|numeric',
            'amount_to_invest' => 'required|numeric|min:100',
            'total_with_fees' => 'required|numeric|min:100',
            'end_date' => 'required|date',
            'icon_url' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        if ($request->total_with_fees > auth()->user()->wallet_balance) {
            $validator->errors()->add('total_with_fees', 'Insufficient funds');
            return $this->validationError($validator);
        }


        try {


            DB::beginTransaction();

            $holding = Holdings::create([
                'user_id' => auth()->id(),
                'asset_name' => $request->asset_name,
                'asset_type' => $request->asset_type,
                'asset_symbol' => $request->asset_symbol,
                'naira_start_rate' => $request->asset_price,
                'asset_amount' => $request->asset_amount,
                'amount' => $request->amount_to_invest,
                'end_date' => $request->end_date,
                'icon_url' => $request->icon_url ?? null,
                'status' => Holdings::STATUS_ACTIVE
            ]);


            auth()->user()->decrement('wallet_balance', $request->total_with_fees);

            UserTransaction::create([
                'user_id' => auth()->id(),
                'amount' => $request->total_with_fees,
                'type' => UserTransaction::TYPE_DEBIT,
                'message' => 'Bought ₦' . $request->amount_to_invest . ' worth of ' . $request->asset_name . ' at $' . $request->asset_price . ' per unit',
                'status' => UserTransaction::STATUS_SUCCESSFUL,
                'source' => UserTransaction::SOURCE_HOLDINGS,
                'fee' => $request->total_with_fees - $request->amount_to_invest
            ]);

            DB::commit();

            return $this->success($holding, 'Holding  created successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function history(Request $request)
    {
        try {
            $data['completed_holdings'] = auth()->user()->holdings()->completed()->latest()->get();

            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
