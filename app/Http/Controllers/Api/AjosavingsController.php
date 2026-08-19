<?php

namespace App\Http\Controllers\Api;

use App\Helpers\SettingHelper;
use App\Http\Controllers\Controller;
use App\Models\AjoSavings;
use App\Models\User;
use App\Models\UserTransaction;
use App\Services\HashIdService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AjosavingsController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {

        try {

            $data['ajo_savings_balance'] = (float) auth()->user()->ajoSavings()->active()->sum('balance');
            $data['referral_code'] = auth()->user()->hash_id;

            $data['active_ajo_savings'] = auth()->user()->ajoSavings()->active()->latest()->get();

            $data['savings_history'] = auth()->user()->ajoSavings()->closed()->latest()->get()->take(5);

            return $this->success($data, 'Ajo savings fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function create(Request $request)
    {
        try {

            $data['ajo_savings_types'] = [
                'Daily' => 'Daily Ajo Savings',
                'Weekly' => 'Weekly Ajo Savings',
                'Monthly' => 'Monthly Ajo Savings',
                'Yearly' => 'Yearly Ajo Savings',
            ];

            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:Daily,Weekly,Monthly,Yearly',
            'amount' => 'required|numeric|min:100',
            'end_date' => 'required|date',
            'payment_method' => 'required|in:wallet,card',
            'referral_code' => 'nullable|string'
        ]);


        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        if ($request->amount > auth()->user()->wallet_balance) {
            $validator->errors()->add('cost', 'Insufficient funds');
            return $this->validationError($validator);
        }

        try {

            $data = $validator->validated();

            if ($request->referral_code) {
                $referred_by = (new HashIdService)->getRecord($request->referral_code, User::class);

                if ($referred_by->id == auth()->id()) {
                    return $this->error('You cannot refer yourself');
                }

                $data['referred_by'] = $referred_by->id;
            }

            unset($data['referral_code']);



            DB::beginTransaction();

            // check if user has enough balance
            if ($request->payment_method == 'wallet') {


                $amount = $request->amount;

                $data['balance'] = $amount;

                auth()->user()->decrement('wallet_balance', $amount);

                auth()->user()->transactions()->create([
                    'amount' => $amount,
                    'source' => UserTransaction::SOURCE_AJO_SAVINGS,
                    'status' => UserTransaction::STATUS_SUCCESSFUL,
                    'message' => "Created and a {$request->type} Ajo Savings and deposited N{$amount}",
                    'type' => UserTransaction::TYPE_DEBIT,
                    'fee' => 0
                ]);
            } else {
                $data['balance'] = 0;
            }


            $data['status'] = AjoSavings::STATUS_ACTIVE;

            $savings =  auth()->user()->ajoSavings()->create($data);

            $savings->transactions()->create([
                'amount' => $data['amount'],
                'source' => 'wallet',
                'status' => 'success'
            ]);

            DB::commit();

            return $this->success($savings, 'Ajo Savings created successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ajo_savings_id' => 'required|exists:ajo_savings,id'
        ]);


        if ($validator->fails()) {
            return $this->validationError($validator);
        }
        try {

            $ajoSavings = AjoSavings::find($request->ajo_savings_id);

            $data['early_close_fee'] = (float) SettingHelper::get(SettingHelper::AJO_SAVINGS_EARLY_CLOSE_FEE);

            $data['ajo_savings'] = $ajoSavings->makeHidden('transactions');


            $data['transactions_chart_data'] = $ajoSavings->transactions->map(function ($transaction) {
                if ($transaction->status == UserTransaction::STATUS_SUCCESSFUL) {
                    return $transaction->amount;
                } else {
                    return 0;
                }
            });

            $dates =  $ajoSavings->transactions->pluck('created_at');

            $data['transactions_chart_labels'] =  $dates->map(function ($date) {
                return $date->format('Y-m-d');
            });



            return $this->success($data, 'Ajo savings details fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function transactions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ajo_savings_id' => 'required|exists:ajo_savings,id'
        ]);


        if ($validator->fails()) {
            return $this->validationError($validator);
        }
        try {

            $ajoSavings = AjoSavings::find($request->ajo_savings_id);

            $data = $ajoSavings->transactions()->latest()->get();

            return $this->success($data, 'Ajo savings transactions fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }


    public function endAjoSavings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ajo_savings_id' => 'required|exists:ajo_savings,id'
        ]);


        if ($validator->fails()) {
            return $this->validationError($validator);
        }
        try {

            $ajoSavings = AjoSavings::find($request->ajo_savings_id);

            if ($ajoSavings->status == AjoSavings::STATUS_CLOSED) {
                return $this->error('Ajo savings already closed');
            }

            DB::beginTransaction();

            $totalFee = getTotalFee($ajoSavings->balance, 0,  SettingHelper::get(
                SettingHelper::AJO_SAVINGS_EARLY_CLOSE_FEE
            ));

            $referrer_bonus = 0;

            if ($ajoSavings->referred_by) {
                $referrer_bonus =
                    getTotalFee($ajoSavings->balance, 0,  SettingHelper::get(
                        SettingHelper::AJO_SAVINGS_REFERRAL_BONUS
                    ));

                $ajoSavings->referrer->increment('wallet_balance', $referrer_bonus);

                $ajoSavings->referrer->transactions()->create([
                    'amount' => $referrer_bonus,
                    'type' => UserTransaction::TYPE_CREDIT,
                    'message' => 'You earned referral bonus for ' . $ajoSavings->user->name . ' Ajo savings account',
                    'status' => UserTransaction::STATUS_SUCCESSFUL,
                    'source' => UserTransaction::SOURCE_AJO_SAVINGS,
                    'fee' => 0
                ]);
            }

            $amount = $ajoSavings->balance - $totalFee;

            $ajoSavings->user->increment('wallet_balance', $amount);

            $ajoSavings->user->transactions()->create([
                'amount' => $amount,
                'type' => UserTransaction::TYPE_CREDIT,
                'message' => 'You closed ' . $ajoSavings->type . ' Ajo savings account   before the end date',
                'status' => UserTransaction::STATUS_SUCCESSFUL,
                'source' => UserTransaction::SOURCE_AJO_SAVINGS,
                'fee' => $totalFee
            ]);

            $ajoSavings->update([
                'status' => AjoSavings::STATUS_CLOSED,
                'amount_earned' => $amount,
                'end_date' => now(),
            ]);

            DB::commit();

            return $this->success($ajoSavings->makeHidden('user'), 'Ajo savings closed successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function history(Request $request)
    {
        try {
            $data = auth()->user()->ajoSavings()->closed()->latest()->get();

            return $this->success($data, 'Ajo savings history fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function payback(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ajo_savings_id' => 'required|exists:ajo_savings,id'
        ]);


        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $ajoSavings = AjoSavings::find($request->ajo_savings_id);


        if ($ajoSavings->pay_back_amount > auth()->user()->wallet_balance) {
            return $this->error("Insufficient funds");
        }


        try {


            if ($ajoSavings->status == AjoSavings::STATUS_CLOSED) {
                return $this->error('Ajo savings already closed');
            }

            DB::beginTransaction();

            $amount = $ajoSavings->pay_back_amount;

            auth()->user()->decrement('wallet_balance', $amount);

            UserTransaction::create([
                'user_id' => auth()->id(),
                'amount' => $amount,
                'type' => UserTransaction::TYPE_DEBIT,
                'message' => 'You paid back the outstanding balance of your ' . $ajoSavings->type . ' Ajo savings',
                'status' => UserTransaction::STATUS_SUCCESSFUL,
                'source' => UserTransaction::SOURCE_AJO_SAVINGS,
                'fee' => 0
            ]);

            $ajoSavings->increment('balance', $amount);

            $ajoSavings->transactions()->create([
                'amount' => $amount,
                'message' => 'Payback amount deposited',
                'status' => UserTransaction::STATUS_SUCCESSFUL,
                'source' => 'wallet'
            ]);

            $ajoSavings->transactions()->where('status', UserTransaction::STATUS_FAILED)->update([
                'status' => UserTransaction::STATUS_SUCCESSFUL
            ]);

            DB::commit();

            return $this->success($ajoSavings->makeHidden('user'), 'Ajo savings payback successful');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
