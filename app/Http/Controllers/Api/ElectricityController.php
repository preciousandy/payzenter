<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ElectricityPurchaseNotification;
use App\Models\ElectricityHistory;
use App\Models\ElectricProvider;
use App\Models\UserTransaction;
use App\Services\BillsService;
use App\Traits\ApiResponseTrait;
use App\Utils\ResponseMessage;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ElectricityController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {

        try {
            $data['providers'] = ElectricProvider::orderBy('name')->get();
            $data['bundles'] = [1000, 2000, 5000, 10000, 15000, 20000];

            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }


    public function history(Request $request)
    {

        try {
            $data = ElectricityHistory::where([
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
            "meter_number" => "required|numeric",
            "provider_code" => "required|string",
            "package" => "required|string",
        ]);


        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {


            $billsService = new BillsService();

            $response = $billsService->fetchEbillsCustomer($request->meter_number, $request->provider_code, $request->package);


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



    public function buyElectricity(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "meter_number" => "required|numeric",
            "provider_code" => "required|string",
            "package" => "required|string",
            'phone' => 'required|numeric|digits:11',
            "amount" => "required|numeric",
            'customer_name' => 'required|string',
            'customer_address' => 'required|string',
        ]);


        if ($validator->fails()) {
            return $this->validationError($validator);
        }


        if ($request->amount > auth()->user()->wallet_balance) {
            return $this->error('Insufficient funds');
        }

        $provider = ElectricProvider::where('code', $request->provider_code)->first();

        if (!$provider) {
            return $this->error('Provider not found', Response::HTTP_NOT_FOUND);
        }


        try {
            $billsService = new BillsService();

            $response = $billsService->purchaseElectricity(
                $request->meter_number,
                $request->provider_code,
                $request->amount,
                $request->package,
            );

            if ($response['status'] !== 'success') {
                throw new \Exception($response['message']);
            }

            $orderStatus = $response['data']['status'];
            $token = $response['data']['token'] ?? 'No token yet';


            DB::beginTransaction();

            if ($orderStatus == 'refunded') {
                throw new \Exception("Cable purchase failed. Transaction was refunded.");
            }

            auth()->user()->wallet_balance -= $request->amount;
            auth()->user()->save();
            $transactionStatus = $orderStatus == 'completed-api' ? UserTransaction::STATUS_SUCCESSFUL : UserTransaction::STATUS_PENDING;

            // record transaction
            auth()->user()->transactions()->create([
                'amount' => $request->amount,
                'type' => UserTransaction::TYPE_DEBIT,
                'message' => match ($orderStatus) {
                    'completed-api' => "Purchased electricity worth N" . number_format($request->amount) . " for {$request->meter_number}. Token: {$token}",
                    'processing-api' => "Electricity purchase (N" . number_format($request->amount) . ") for {$request->meter_number} is processing. You'll receive token shortly.",
                    'refunded' => "Electricity purchase (N" . number_format($request->amount) . ") for {$request->meter_number} was refunded.",
                    default => "Electricity purchase for {$request->meter_number} is in status: {$orderStatus}"
                },
                'status' => $transactionStatus,
                'source' => UserTransaction::SOURCE_ELECTRICITY,
                'reference' => $response['reference']
            ]);

            // update/create electricity history
            $history = ElectricityHistory::where([
                'user_id' => auth()->id(),
                'meter_number' => $request->meter_number,
                'provider_code' => $request->provider_code,
            ])->first();

            if ($history) {
                $history->update(['updated_at' => now()]);
            } else {

                ElectricityHistory::create([
                    'user_id' => auth()->id(),
                    'meter_number' => $request->meter_number,
                    'phone' => $request->phone,
                    'provider_code' => $request->provider_code,
                    'provider_name' => $provider->name,
                    'package_code' => $request->package,
                    'customer_name' => $request->customer['customer_name'],
                    'customer_address' => $request->customer['customer_address'],
                ]);
            }

            DB::commit();

            $user = auth()->user();
            if ($orderStatus == 'completed-api') {
                Mail::to($user->email)->queue(new ElectricityPurchaseNotification($user, $response['data']));
            }

            $success_message = match ($orderStatus) {
                'completed-api' => "Token: {$token}",
                'processing-api' => "Purchase is processing. You’ll receive your token soon.",
                'refunded' => "Transaction was refunded. No amount was deducted.",
                default => "Transaction status: {$orderStatus}"
            };

            return $this->success($response['data'], $success_message);
        } catch (Exception $ex) {
            return $this->error($ex->getMessage());
        }
    }
}
