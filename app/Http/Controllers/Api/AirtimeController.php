<?php

namespace App\Http\Controllers\Api;

use App\Helpers\SettingHelper;
use App\Http\Controllers\Controller;
use App\Models\AirtimeDataHistory;
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

class AirtimeController extends Controller
{
    use ApiResponseTrait;

    public function airtimeDataPage(Request $request)
    {

        try {
            $data['airtime_cashback_fixed'] = SettingHelper::get(SettingHelper::AIRTIME_CASHBACK_FIXED);
            $data['airtime_cashback_percentage'] = SettingHelper::get(SettingHelper::AIRTIME_CASHBACK_PERCENTAGE);

            $data['data_cashback_fixed'] = SettingHelper::get(SettingHelper::DATA_CARD_CASHBACK_FIXED);
            $data['data_cashback_percentage'] = SettingHelper::get(SettingHelper::DATA_CARD_CASHBACK_PERCENTAGE);

            $data['airtime_bundles'] = airtimeBundles();
            $data['networks'] = Network::with('dataPlans')->get();

            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function airtimeDataHistory(Request $request)
    {

        try {
            $data = AirtimeDataHistory::where([
                'user_id' => auth()->id(),
            ])->when(request('type'), function ($query) {
                return $query->where('type', request('type'));
            })->orderBy('updated_at', 'desc')->get();

            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function buyAirtime(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'airtime_amount' => 'required|numeric|min:100',
            'cost' => 'required|numeric',
            'phone' => 'required|numeric|digits_between:10,11',
            'network_code' => 'required|string',
        ]);


        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        if ($request->cost > auth()->user()->wallet_balance) {
            return $this->error('Insufficient funds');
        }



        try {
            $billsService = new BillsService();

            $response = $billsService->purchaseAirtime($request->network_code, $request->airtime_amount, $request->phone);



            if ($response['status'] == 'success') {

                DB::beginTransaction();

                auth()->user()->decrement('wallet_balance', $request->cost);

                // record user transaction
                auth()->user()->transactions()->create([
                    'amount' => $request->cost,
                    'type' => UserTransaction::TYPE_DEBIT,
                    'message' => 'You bought ' . $request->cost . ' airtime for ' . $request->phone,
                    'status' => UserTransaction::STATUS_SUCCESSFUL,
                    'source' => UserTransaction::SOURCE_AIRTIME,
                    'reference' => $response['reference']

                ]);

                $history = AirtimeDataHistory::where([
                    'user_id' => $request->user()->id,
                    'phone_number' => $request->phone,
                    'type' => AirtimeDataHistory::TYPE_AIRTIME
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
                        'type' => AirtimeDataHistory::TYPE_AIRTIME
                    ]);
                }

                DB::commit();

                return $this->success($response['data'], 'Airtime purchased successfully', 200);
            } else {

                throw new \Exception("API error " . $response['message']);
            }
        } catch (\Exception $e) {

            return $this->error($e->getMessage());
        }
    }
}
