<?php

namespace App\Http\Controllers\Api;

use App\Helpers\SettingHelper;
use App\Http\Controllers\Controller;
use App\Models\Savings;
use App\Models\UserTransaction;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class InterestSavingsController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        try {
            $data['total_savings'] =  auth()->user()->savings()->active()->sum('balance');
            $data['savings'] = auth()->user()->savings()->active()->latest()->get();
            $data['locked_savings_interest_rate'] = SettingHelper::get(SettingHelper::LOCKED_SAVINGS_INTEREST, 0);
            $data['unlocked_savings_interest_rate'] = SettingHelper::get(SettingHelper::UNLOCKED_SAVINGS_INTEREST, 0);

            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function create(Request $request)
    {
        try {

            $data['locked_savings_interest_rate'] = SettingHelper::get(SettingHelper::LOCKED_SAVINGS_INTEREST, 0);
            $data['unlocked_savings_interest_rate'] = SettingHelper::get(SettingHelper::UNLOCKED_SAVINGS_INTEREST, 0);
            $data['create_savings_fee'] = SettingHelper::get(SettingHelper::CREATE_INTEREST_SAVINGS_FEE);
            $data['if_has_locked_savings'] = Savings::where('user_id', auth()->id())
                ->where('type', Savings::TYPE_LOCKED)
                ->where('status', Savings::STATUS_ACTIVE)
                ->exists();

            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:5|max:255',
            'type' => 'required|string',
            'amount' => 'required|numeric|min:100',
            'total_amount' => 'required|numeric|min:100',
            'end_date' => 'nullable|date',
            'if_auto_save' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }


        if ($request->if_auto_save) {
            $validator = Validator::make($request->all(), [
                'auto_save_amount' => 'required|numeric|min:100',
                'frequency' => 'required|string|in:weekly,monthly,daily',
            ]);

            if ($validator->fails()) {
                return $this->validationError($validator);
            }
        }

        try {
            $data = $request->all();

            $data['status'] = Savings::STATUS_ACTIVE;
            $data['balance'] = $request->amount;
            $data['interest_accumulated'] = 0;
            unset($data['amount']);
            unset($data['pin']);
            unset($data['total_amount']);


            if ($request->if_auto_save) {
                $data['auto_save_amount'] = $request->auto_save_amount;
                $data['frequency'] = $request->frequency;
                $data['auto_save_last_deduction'] = now();
            }


            DB::beginTransaction();

            $user = auth()->user()->lockForUpdate()->first();

            if ($request->total_amount > $user->wallet_balance) {
                DB::rollBack();
                return $this->error('Insufficient funds');
            }

            $savings = $user->savings()->create($data);


            $totalAmount = $request->total_amount;
            $createSavingsFee = SettingHelper::get(SettingHelper::CREATE_INTEREST_SAVINGS_FEE);

            $user->decrement('wallet_balance', $totalAmount);

            // record user transaction
            $user->transactions()->create([
                'amount' => $totalAmount,
                'type' => UserTransaction::TYPE_DEBIT,
                'message' => "Created a  {$request->name} savings and deposited " . formatNumber($request->amount),
                'status' => UserTransaction::STATUS_SUCCESSFUL,
                'source' => UserTransaction::SOURCE_SAVE_WITH_INTEREST,
                'fee' => $createSavingsFee
            ]);

            DB::commit();

            return $this->success($savings,  'Savings created successfully');
        } catch (\Exception $th) {
            return $this->error($th->getMessage());
        }
    }

    public function history(Request $request)
    {
        try {
            $data['locked_savings_interest_rate'] =  (float)  SettingHelper::get(SettingHelper::LOCKED_SAVINGS_INTEREST, 0);
            $data['unlocked_savings_interest_rate'] = (float) SettingHelper::get(SettingHelper::UNLOCKED_SAVINGS_INTEREST, 0);
            $data['closed_savings'] = auth()->user()->savings()->closed()->latest()->get();

            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $savings = auth()->user()->savings()->find($id);

            if (!$savings) {
                return $this->error('Savings not found', 404);
            }

            $data['locked_savings_interest_rate'] =  (float)  SettingHelper::get(SettingHelper::LOCKED_SAVINGS_INTEREST, 0);
            $data['unlocked_savings_interest_rate'] = (float) SettingHelper::get(SettingHelper::UNLOCKED_SAVINGS_INTEREST, 0);
            $data['breaking_locked_savings_fee'] = (float) SettingHelper::get(SettingHelper::BREAKING_LOCKED_SAVINGS_FEE, 0);
            $data['close_unlocked_savings_fee'] = (int) SettingHelper::get(SettingHelper::CLOSE_UNLOCKED_SAVINGS_FEE, 0);
            $data['withdrawal_sources'] = ['balance', 'interest'];
            $data['savings'] = $savings;
            $data['savings_activities'] = $savings->activities()->latest()->get();


            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:5|max:255',

        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $savings = auth()->user()->savings()->find($id);

            if (!$savings) {
                return $this->error('Savings not found', 404);
            }

            if ($savings->status !== Savings::STATUS_ACTIVE) {
                return $this->error('Savings is not active', 400);
            }

            $savings->update([
                'name' => $request->name
            ]);


            return $this->success($savings, 'Savings updated successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function addMoney(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'savings_id' => 'required|exists:savings,id',
            'amount' => 'required|numeric|min:100',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $savings = auth()->user()->savings()->find($request->savings_id);

            if (!$savings) {
                return $this->error('Savings not found', 404);
            }

            if ($savings->status !== Savings::STATUS_ACTIVE) {
                return $this->error('Savings is not active', 400);
            }

            DB::beginTransaction();

            $user = auth()->user()->lockForUpdate()->first();

            if ($request->amount > $user->wallet_balance) {
                DB::rollBack();
                return $this->error('Insufficient funds');
            }

            $savings->increment('balance', $request->amount);

            $user->decrement('wallet_balance', $request->amount);

            $savings->activities()->create([
                'message' => 'Added ₦ ' . number_format($request->amount) . ' to your savings',
                'user_id' => auth()->id()
            ]);


            // record user transaction
            $user->transactions()->create([
                'amount' => $request->amount,
                'type' => UserTransaction::TYPE_DEBIT,
                'message' => 'Added ₦ ' . formatNumber($request->amount) . ' my into ' . $savings->name . ' savings account',
                'status' => UserTransaction::STATUS_SUCCESSFUL,
                'source' => UserTransaction::SOURCE_SAVE_WITH_INTEREST,
                'fee' => 0
            ]);

            DB::commit();

            return $this->success($savings,  'Money added to savings successfully');
        } catch (\Exception $th) {
            return $this->error($th->getMessage());
        }
    }

    public function withdraw(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'savings_id' => 'required|exists:savings,id',
            'amount' => 'required|numeric|min:100',
            'source' => 'required|string|in:balance,interest',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $savings = auth()->user()->savings()->find($request->savings_id);

        if (!$savings) {
            return $this->error('Savings not found', 404);
        }

        $breaking_locked_savings_fee = SettingHelper::get(SettingHelper::BREAKING_LOCKED_SAVINGS_FEE, 0);

        $fee = getPercentageAmount($request->amount, $breaking_locked_savings_fee);

        $amount = $savings->type === Savings::TYPE_LOCKED ? $request->amount - $fee : $request->amount;


        if ($request->source == 'balance' && $request->amount > $savings->balance) {
            return $this->error("Insufficient wallet funds");
        }

        if ($request->source == 'interest' && $request->amount > $savings->interest_accumulated) {
            return $this->error("Insufficient interest funds");
        }

        try {
            DB::beginTransaction();

            if ($request->source === 'interest' && $request->amount <= $savings->interest_accumulated) {
                $savings->decrement('interest_accumulated', $request->amount);
            }

            if ($request->source === 'balance' && $request->amount <= $savings->balance) {
                $savings->decrement('balance', $request->amount);
            }

            $savings->activities()->create([
                'message' => "You withdrew ₦ {$request->amount} from your savings " . ($request->source == 'interest' ? "interest" : "balance"),
                'user_id' => auth()->id()
            ]);

            auth()->user()->increment('wallet_balance', $amount);

            auth()->user()->transactions()->create([
                'amount' => $amount,
                'type' => UserTransaction::TYPE_CREDIT,
                'message' => 'Withdrew ₦ ' . $request->amount . ' from your savings account',
                'status' => UserTransaction::STATUS_SUCCESSFUL,
                'source' => UserTransaction::SOURCE_SAVE_WITH_INTEREST,
                'fee' => $savings->type === Savings::TYPE_LOCKED ? $fee : 0
            ]);

            DB::commit();

            return $this->success($savings,  'Withdrawal successful');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function endSavings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'savings_id' => 'required|exists:savings,id',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }



        $savings = auth()->user()->savings()->find($request->savings_id);

        if (!$savings) {
            return $this->error('Savings not found', 404);
        }

        if ($savings->type === Savings::TYPE_LOCKED) {
            return $this->error('You cannot end a locked savings', 400);
        }

        try {
            DB::beginTransaction();

            $amountEarned = $savings->balance + $savings->interest_accumulated;

            $fee = SettingHelper::get(SettingHelper::CLOSE_UNLOCKED_SAVINGS_FEE);

            $total = $amountEarned - $fee;

            auth()->user()->increment('wallet_balance', $total);

            auth()->user()->transactions()->create([
                'amount' => $total,
                'type' => UserTransaction::TYPE_CREDIT,
                'message' => 'Closed my ' . $savings->name . ' savings account',
                'status' => UserTransaction::STATUS_SUCCESSFUL,
                'source' => UserTransaction::SOURCE_SAVE_WITH_INTEREST,
                'fee' => $fee
            ]);

            $savings->update([
                'status' => Savings::STATUS_CLOSED,
                'balance' => 0,
                'interest_accumulated' => 0,
                'end_date' => now()
            ]);

            $savings->activities()->create([
                'message' => "You closed your savings account",
                'user_id' => auth()->id()
            ]);

            DB::commit();

            return $this->success($savings, "Savings closed successfully");
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
