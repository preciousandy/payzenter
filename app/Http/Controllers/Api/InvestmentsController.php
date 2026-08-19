<?php

namespace App\Http\Controllers\Api;

use App\Helpers\SettingHelper;
use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use App\Models\CryptoCurrency;
use App\Models\ExchangeRate;
use App\Models\Investment;
use App\Models\StockPrice;
use App\Models\UserTransaction;
use App\Traits\ApiResponseTrait;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class InvestmentsController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        try {
            $data['total_asset'] = auth()->user()->investments()->active()->sum('amount_invested');

            $data['investments'] = auth()->user()->investments()->active()->latest()->get()->each(function ($investment) {
                if ($investment->asset_type == 'stock') {
                    $investment->current_rate = StockPrice::where('symbol', $investment->asset_symbol)->first()->change_percent;
                } elseif ($investment->asset_type == 'crypto') {
                    $investment->current_rate = CryptoCurrency::where('symbol', $investment->asset_symbol)->first()->change_percentage;
                }

                $investment->current_earnings = calculatePercentageChange($investment->current_rate, $investment->amount_invested);
            });

            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }


    public function assets(Request $request)
    {
        try {
            $data['naira_to_dollar_rate'] = ExchangeRate::firstWhere('code', 'USD')->rate;

            $data['stocks'] = StockPrice::all();

            $data['crypto_currencies'] = CryptoCurrency::all();

            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }


    public function stockDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'stock_symbol' => 'required|string',
        ]);


        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $res = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'X-RapidAPI-Key' => ApiKey::getApiKey(ApiKey::RAPID_API_KEY),
                'X-RapidAPI-Host' => 'real-time-finance-data.p.rapidapi.com'
            ])->get(env("STOCKS_API_URL") . "/stock-overview", [
                'symbol' => $request->stock_symbol,
            ]);

            $data = $res->json();


            if ($res->successful()) {


                if (count($data['data']) == 0) {
                    throw new \Exception("Stock not found", 1);
                }

                return $this->success($data, 'Stock fetched successfully');
            } else {

                throw new \Exception($data['message'], 1);
            }
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage());
        }
    }

    public function cryptoDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'crypto_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $data['naira_to_dollar_rate'] = ExchangeRate::firstWhere('code', 'USD')->rate;

            $data['crypto'] = CryptoCurrency::find($request->crypto_id);

            $data['price_changes_dates'] = $this->getLastNDates(count($data['crypto']->changes));

            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    private function getLastNDates($days)
    {
        $dates = [];
        $today = new DateTime();

        for ($i = 1; $i <= $days; $i++) {
            $date = clone $today;
            $date->modify("-$i day");
            $dates[] = $date->format('Y-m-d'); // or any other format you prefer
        }

        return array_reverse($dates);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|in:stock,crypto',
            'type_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $data['naira_to_dollar_rate'] = ExchangeRate::firstWhere('code', 'USD')->rate;

            $data['fixed_fee'] = (float) SettingHelper::get(SettingHelper::INVESTMENT_FEE);
            $data['percentage_fee'] = (float) SettingHelper::get(SettingHelper::INVESTMENT_PERCENTAGE_FEE);

            if ($request->type == 'stock') {
                $data['stock'] = StockPrice::find($request->type_id);
            } else {
                $data['crypto']  = CryptoCurrency::find($request->type_id);
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
            'asset_type' => 'required|string|in:stock,crypto',
            'asset_symbol' => 'required|string',
            'asset_icon_url' => 'nullable|string',
            'amount_invested' => 'required|numeric',
            'total_with_fees' => 'required|numeric',
            'end_date' => 'required|date',
            'change_percentage' => 'required|numeric',
        ]);


        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        if ($request->total_with_fees > auth()->user()->wallet_balance) {
            $validator->errors()->add('cost', 'Insufficient funds');
            return $this->validationError($validator);
        }


        try {
            DB::beginTransaction();

            $investment = auth()->user()->investments()->create([
                'asset_name' => $request->asset_name,
                'asset_type' => $request->asset_type,
                'asset_symbol' => $request->asset_symbol,
                'asset_icon_url' => $request->asset_icon_url,
                'change_percentage' => $request->change_percentage,
                'amount_invested' => $request->amount_invested,
                'end_date' => $request->end_date,
                'status' => Investment::STATUS_ACTIVE
            ]);

            auth()->user()->decrement('wallet_balance', $request->total_with_fees);

            auth()->user()->transactions()->create([
                'type' => UserTransaction::TYPE_DEBIT,
                'amount' => $request->total_with_fees,
                'status' => UserTransaction::STATUS_SUCCESSFUL,
                'message' => 'Invested in ' . $request->asset_name .  ' with ₦' . $request->amount_invested,
                'source' => UserTransaction::SOURCE_INVESTMENTS,
                'fee' => $request->total_with_fees - $request->amount_invested
            ]);

            DB::commit();

            return $this->success($investment, 'Investment created successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function cryptoInvestment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'investment_id' => 'required|exists:investments,id'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {

            $data['investment'] = Investment::find($request->investment_id);

            $data['cryptocurrency'] = CryptoCurrency::where('symbol', $data['investment']->asset_symbol)->first();

            $data['naira_to_dollar_rate'] = ExchangeRate::firstWhere('code', 'USD')->rate;

            $data['current_rate'] = $data['cryptocurrency']->change_percentage;

            $data['possible_earnings'] = calculatePercentageChange($data['current_rate'], $data['investment']->amount_invested);

            $data['earnings'] = $data['investment']->status == 'active' ? $data['possible_earnings'] : $data['investment']->amount_earned;

            $data['historical_dates'] = $this->getLastNDates(count($data['cryptocurrency']->changes));

            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function stockInvestment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'investment_id' => 'required|exists:investments,id'
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $investment = Investment::find($request->investment_id);

            $data['investment'] = $investment;

            $data['stock'] = StockPrice::where('symbol', $investment->asset_symbol)->first();

            $data['naira_to_dollar_rate'] = ExchangeRate::firstWhere('code', 'USD')->rate;

            $data['current_rate'] = StockPrice::where('symbol', $investment->asset_symbol)->first()->change_percent;

            $data['possible_earnings'] = calculatePercentageChange($data['current_rate'], $data['investment']->amount_invested);

            $data['earnings'] = $data['investment']->status == 'active' ? $data['possible_earnings'] : $data['investment']->amount_earned;


            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
    public function history(Request $request)
    {

        try {
            $data['completed_investments'] = auth()->user()->investments()->completed()->latest()->get();

            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
