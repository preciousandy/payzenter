<?php

namespace App\Http\Controllers\Api;

use App\Helpers\SettingHelper;
use App\Http\Controllers\Controller;
use App\Jobs\TransferToBank;
use App\Models\MultipleTransfer;
use App\Models\Transfer;
use App\Models\User;
use App\Models\UserTransaction;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use KingFlamez\Rave\Facades\Rave as Flutterwave;


class MultipleTransferController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        try {
            $data['fixed_multiple_transfer_fee'] = (int) SettingHelper::get(SettingHelper::MULTIPLE_TRANSFER_FIXED_FEE);
            // $data['percentage_bank_transfer_fee'] = (float) SettingHelper::get(SettingHelper::TRANSFER_FEE);
            $data['banks'] = cache()->remember('banks', 60 * 60 * 24 * 2, function () {
                return getDotBanks();
            });

            return $this->success($data, 'Bank transfer data retrieved successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function handleMultipleSwiftPayTransfer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'total_amount' => 'required|numeric|min:100',
            'swiftpay_accounts' => 'required|array|min:1',
            'swiftpay_accounts.*.id' => 'required|numeric',
            'swiftpay_accounts.*.name' => 'required|string',
            'swiftpay_accounts.*.amount' => 'required|numeric|min:100',
            'swiftpay_accounts.*.description' => 'required|string',
            'source_link' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        if ($request->total_amount > auth()->user()->wallet_balance) {
            $validator->errors()->add('total_amount', 'Insufficient funds');
            return $this->validationError($validator);
        }


        try {
            DB::beginTransaction();

            foreach ($request->swiftpay_accounts as $account) {
                $user = User::findOrFail($account['id']);
                $user->increment('wallet_balance', $account['amount']);
                $user->save();

                $user->transactions()->create([
                    'amount' => $account['amount'],
                    'message' => "Received " . $account['amount'] . " from " . auth()->user()->name,
                    'type' => UserTransaction::TYPE_CREDIT,
                    'status' => UserTransaction::STATUS_SUCCESSFUL,
                    'source' => UserTransaction::SOURCE_SWIFTPAY_TRANSFER,
                    'fee' => 0
                ]);
            }

            auth()->user()->decrement('wallet_balance', $request->total_amount);

            auth()->user()->transactions()->create([
                'amount' => $request->total_amount,
                'message' => "Transferred N{$request->total_amount} to " . $request->swiftpay_accounts[0]['name'] . " and " . count($request->swiftpay_accounts) - 1 .  " other accounts",
                'type' => UserTransaction::TYPE_DEBIT,
                'status' => UserTransaction::STATUS_SUCCESSFUL,
                'source' => UserTransaction::SOURCE_MULTIPLE_SWIFTPAY_TRANSFER,
                'fee' => 0
            ]);

            $transaction = MultipleTransfer::create([
                'accounts' => implode(',', array_column($request->swiftpay_accounts, 'name')),
                'type' => MultipleTransfer::TYPE_SWIFTPAY,
                'total_amount' => $request->total_amount,
                'user_id' => auth()->id(),
                'status' => MultipleTransfer::STATUS_SUCCESSFUL,
                'reference' => 'SWPYMUL-' . time(),
                'source_link' => $request->source_link ?? null
            ]);

            DB::commit();

            return $this->success($transaction, 'Multiple Transfer successful');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function handleMultipleBankTransfer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'total_amount' => 'required|numeric|min:100',
            'bank_accounts' => 'required|array|min:2',
            'bank_accounts.*.account_name' => 'required|string',
            'bank_accounts.*.account_number' => 'required|numeric',
            'bank_accounts.*.bank_name' => 'required|string',
            'bank_accounts.*.bank_code' => 'required|string',
            'bank_accounts.*.amount' => 'required|numeric|min:100',
            'bank_accounts.*.description' => 'nullable|string',
            'bank_accounts.*.fee' => 'required|numeric',
            'source_link' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        if ($request->total_amount > auth()->user()->wallet_balance) {
            $validator->errors()->add('total_amount', 'Insufficient funds');
            return $this->validationError($validator);
        }



        try {

            $bulk_data = collect($request->bank_accounts)->map(function ($account, $index) {
                return [
                    'amount' => $account['amount'],
                    'description' => 'SWPY/' . $account['description'],
                    'bank_code' => $account['bank_code'],
                    'account_number' => $account['account_number'],
                    'reference' => "BKMTPL{$index}" . time(),
                    // 'currency' => 'NGN',
                    'bank_name' => $account['bank_name'],
                    'account_name' => $account['account_name'],
                    'swiftpay_fee' => $account['fee'],
                ];
            })->toArray();


            // $flutterwaveResponse = Flutterwave::transfers()->bulk([
            //     'title' => 'Bulk Transfer',
            //     'bulk_data' => $bulk_data
            // ]);

            // if ($flutterwaveResponse['status'] !== 'success') {
            //     throw new \Exception($flutterwaveResponse['message']);
            // }

            $bulk_transfer_id = uniqid();

            DB::beginTransaction();

            foreach ($bulk_data as $bank) {
                $transfer = Transfer::create([
                    'user_id' => auth()->id(),
                    'reference' => $bank['reference'],
                    'status' => Transfer::STATUS_PENDING,
                    'amount' => $bank['amount'],
                    'fee' => $bank['swiftpay_fee'],
                    'description' => $bank['description'],
                    'type' => MultipleTransfer::TYPE_BANK,
                    'account_name' => $bank['account_name'],
                    'account_number' => $bank['account_number'],
                    'bank_name' => $bank['bank_name'],
                    'bank_code' => $bank['bank_code'],
                    'bulk_transfer_id' => $bulk_transfer_id,
                ]);
            }

            auth()->user()->decrement('wallet_balance', $request->total_amount);

            $totalFee = collect($request->bank_accounts)->sum('fee');

            auth()->user()->transactions()->create([
                'amount' => $request->total_amount,
                'message' => "Transferred N{$request->total_amount} to " . $request->bank_accounts[0]['account_name'] . " and " . count($request->bank_accounts) - 1 .  " other accounts",
                'type' => UserTransaction::TYPE_DEBIT,
                'status' => UserTransaction::STATUS_SUCCESSFUL,
                'source' => UserTransaction::SOURCE_MULTIPLE_BANK_TRANSFER,
                'fee' => $totalFee
            ]);

            $transaction = MultipleTransfer::create([
                'user_id' => auth()->id(),
                'accounts' => implode(',', array_column($request->bank_accounts, 'account_name')),
                'type' => MultipleTransfer::TYPE_BANK,
                'total_amount' => $request->total_amount,
                'status' => MultipleTransfer::STATUS_SUCCESSFUL,
                'reference' => "BKMTPL" . time(),
                'source_link' => $request->source_link ?? null
            ]);

            DB::commit();

            foreach ($bulk_data as $bank) {
                TransferToBank::dispatch($bank, $request->user());
            }

            return $this->success($transaction, 'Multiple Bank Transfer successful');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
