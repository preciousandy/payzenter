<?php

namespace App\Http\Controllers\Api;

use App\Helpers\SettingHelper;
use App\Http\Controllers\Controller;
use App\Models\BettingProvider;
use App\Models\FundBettingHistory;
use App\Models\UserTransaction;
use App\Services\BillsService;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BettingController extends Controller
{
    use ApiResponseTrait;


    public function bettingPage(Request $request)
    {
        try {
            $fixed_fee = (int) SettingHelper::get(SettingHelper::FUND_BETTING_FIXED_FEE);
            $percentage_fee = (float) SettingHelper::get(SettingHelper::FUND_BETTING_PERCENTAGE_FEE);

            $data['fixed_fee'] = $fixed_fee;
            $data['percentage_fee'] = $percentage_fee;
            $data['providers'] = BettingProvider::orderBy('name')->get(['id', 'name']);
            $data['bundles'] = [1000, 2000, 5000, 10000, 15000, 20000];

            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }


    public function history(Request $request)
    {
        try {
            $data = FundBettingHistory::where([
                'user_id' => auth()->id(),
            ])->orderBy('updated_at', 'desc')->get();

            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function verifyCustomer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "customer_id" => "required|numeric",
            "provider" => "required|string",
        ]);


        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {


            $billsService = new BillsService();

            $response = $billsService->fetchEbillsCustomer($request->customer_id, $request->provider);


            if ($response && $response['status'] == 'success') {

                return $this->success($response['data'], 'Customer verified successfully');
            } else {
                $errorMessage = $response['message'] ?? 'Unable to verify account details';
                throw new Exception($errorMessage);
            }
        } catch (Exception $ex) {
            return $this->error($ex->getMessage());
        }
    }

    public function fundAccount(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "customer_id" => "required|numeric",
            "provider" => "required|string",
            "amount" => "required|numeric|min:100|max:100000",
            'customer' => 'required|array',
        ]);


        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $fixed_fee = (int) SettingHelper::get(SettingHelper::FUND_BETTING_FIXED_FEE);
        $percentage_fee = (float) SettingHelper::get(SettingHelper::FUND_BETTING_PERCENTAGE_FEE);

        $total_fee = getTotalFee(
            $request->amount,
            $fixed_fee,
            $percentage_fee
        );

        $total = $request->amount + $total_fee;

        if ($total > auth()->user()->wallet_balance) {
            return $this->error('Insufficient funds');
        }

        $provider = BettingProvider::where('name', $request->provider)->first();

        if (!$provider) {
            return $this->error('Provider not found', Response::HTTP_NOT_FOUND);
        }


        try {
            $billsService = new BillsService();

            $response = $billsService->fundBettingAccount(
                $request->customer_id,
                $request->provider,
                $request->amount,
            );

            if ($response['status'] !== 'success') {
                throw new \Exception($response['message']);
            }

            $orderStatus = $response['data']['status'];
            $transactionStatus = UserTransaction::STATUS_PENDING;

            DB::beginTransaction();

            if ($orderStatus == 'refunded') {
                throw new \Exception("Funding of betting account failed. Please try again later.");
            }

            // deduct from user wallet
            auth()->user()->wallet_balance -= $total;
            auth()->user()->save();

            $transactionStatus = UserTransaction::STATUS_SUCCESSFUL;

            // record transaction
            auth()->user()->transactions()->create([
                'amount' => $total,
                'type' => UserTransaction::TYPE_DEBIT,
                'message' => match ($orderStatus) {
                    'completed-api' => "Funding of betting account with N" . number_format($request->amount) . " for was successful. ",
                    'processing-api' => "Funding of betting account with N" . number_format($request->amount) . " is processing. You’ll receive a the funds soon.",
                },
                'status' => $transactionStatus,
                'source' => UserTransaction::SOURCE_BETTING,
                'reference' => $response['reference'],
                'fee' => $total_fee
            ]);

            // update/create BETTING history
            $history = FundBettingHistory::where([
                'user_id' => auth()->id(),
                'customer_id' => $request->customer_id,
                'provider' => $request->provider,
            ])->first();

            if ($history) {
                $history->update(['updated_at' => now()]);
            } else {
                FundBettingHistory::create([
                    'user_id' => auth()->id(),
                    'customer_id' => $request->customer_id,
                    'provider' => $request->provider,
                    'customer_name' => $request->customer['customer_name'] ?? null,
                    'customer_email' => $request->customer['customer_email'] ?? null,
                    'customer_address' => $request->customer['customer_address'] ?? null,
                ]);
            }

            DB::commit();

            return $this->success($response['data'], 'Betting account funded successfully. Your transaction is being processed.');
        } catch (Exception $ex) {
            return $this->error($ex->getMessage());
        }
    }
}
