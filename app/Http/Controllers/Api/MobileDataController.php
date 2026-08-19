<?php

namespace App\Http\Controllers\Api;

use App\Helpers\SettingHelper;
use App\Http\Controllers\Controller;
use App\Models\AirtimeDataHistory;
use App\Models\DataPlan;
use App\Models\Network;
use App\Models\UserTransaction;
use App\Services\BillsService;
use App\Traits\ApiResponseTrait;
use App\Utils\ResponseMessage;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class MobileDataController extends Controller
{
    use ApiResponseTrait;

    public function buyData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|numeric|digits:11',
            'plan_id' => 'required|integer|exists:data_plans,id',
            'network_code' => 'required|string|exists:networks,vtu_code',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }



        $plan = DataPlan::findOrFail($request->plan_id);

        $pecentageCashbackAmount = getPercentageAmount($plan->price, SettingHelper::get(SettingHelper::DATA_CARD_CASHBACK_PERCENTAGE));

        $fixedCashbackAmount = SettingHelper::get(SettingHelper::DATA_CARD_CASHBACK_FIXED);

        $cost = $plan->price - $pecentageCashbackAmount - $fixedCashbackAmount;


        if ($cost > auth()->user()->wallet_balance) {
            return $this->error('Insufficient funds');
        }

        try {

            $billsService = new BillsService();

            $response = $billsService->purchaseDataPlan($request->network_code, $plan->item_code, $request->phone);

            if ($response['status'] == 'success') {

                DB::beginTransaction();
                auth()->user()->decrement('wallet_balance', $cost);


                // record user transaction
                auth()->user()->transactions()->create([
                    'amount' => $cost,
                    'type' => UserTransaction::TYPE_DEBIT,
                    'message' => "You purchased a data plan of " . $plan->price . " for " . $request->phone,
                    "status" => UserTransaction::STATUS_SUCCESSFUL,
                    'source' => UserTransaction::SOURCE_DATA,
                    'reference' => $response['reference'],
                    'fee' => 0

                ]);

                $history = AirtimeDataHistory::where([
                    'user_id' => $request->user()->id,
                    'phone_number' => $request->phone,
                    'type' => AirtimeDataHistory::TYPE_DATA
                ])->first();

                if ($history) {
                    $history->update([
                        'updated_at' => now(),
                    ]);
                } else {

                    $network = Network::where('vtu_code', $request->network_code)->first();

                    if (!$network) {
                        throw new \Exception('Network not found');
                    }

                    AirtimeDataHistory::create([
                        'user_id' => auth()->id(),
                        'phone_number' => $request->phone,
                        'network_code' => $request->network_code,
                        'network_name' => $network->name,
                        'type' => AirtimeDataHistory::TYPE_DATA
                    ]);
                }

                DB::commit();

                return $this->success($response['data'], 'Data bundle purchased successful', 200);
            } else {
                throw new \Exception($response['message']);
            }
        } catch (\Exception $e) {

            return $this->error($e->getMessage());
        }
    }
}
