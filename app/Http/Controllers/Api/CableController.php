<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cable;
use App\Models\CableHistory;
use App\Models\CablePackage;
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
use Illuminate\Support\Facades\Validator;

class CableController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {

        try {
            $data['providers'] = Cable::with('packages')->get();

            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function history(Request $request)
    {

        try {
            $data = CableHistory::where([
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
            "card_number" => "required|numeric",
            "cable_provider_code" => "required|string"
        ]);


        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {

            $billsService = new BillsService();

            $response = $billsService->fetchEbillsCustomer($request->card_number, $request->cable_provider_code);

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

    public function buyCable(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'nullable|numeric|digits:11',
            "provider_code" => "required|string",
            "card_number" => "nullable|numeric",
            "package_id" => "required|integer|exists:cable_packages,id",
            'customer_name' => 'nullable|string',
        ]);


        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        if (!$request->card_number && !$request->phone) {
            return $this->error('Please provide either a Smart Card number or a Phone number', Response::HTTP_BAD_REQUEST);
        }


        $package = CablePackage::findOrFail($request->package_id);

        if ($package->price > $request->user()->wallet_balance) {
            return $this->error('Insufficient funds', Response::HTTP_BAD_REQUEST);
        }


        try {

            $billsService = new BillsService();

            $customer_id = $request->card_number ?: $request->phone;

            $response = $billsService->purchaseTvCable(
                $customer_id,
                $request->provider_code,
                $package->package_code,

            );

            if ($response['status'] !== 'success') {
                throw new \Exception($response['message']);
            }

            $orderStatus = $response['data']['status']; // completed-api, processing-api, refunded

            $token = null;

            if ($request->provider_code == 'showmax' && $orderStatus == 'completed-api') {
                $token = $response['data']['voucher'] ?? null;
            }

            // Only deduct wallet if order completed
            if ($orderStatus == 'refunded') {
                throw new \Exception("Cable purchase failed. Transaction was refunded.");
            }

            $statusMessage = match ($orderStatus) {
                'completed-api' => "Cable TV purchase was  successfully",
                'processing-api' => "Cable TV purchase is being processed and may take a while to reflect",
                default => "Cable TV purchase failed. Please try again later.",
            };

            $transaction_message = "You purchased a " . ucfirst($request->provider_code) . " subscription of " . $package->name . " for " . ($request->card_number ?? $request->phone);

            if ($request->provider_code == 'showmax' && $token) {
                $transaction_message .= ". Your token is: " . $token;
            }


            DB::beginTransaction();

            auth()->user()->wallet_balance -= $package->price;
            auth()->user()->save();

            // record user transaction
            auth()->user()->transactions()->create([
                'amount' => $package->price,
                'type' => UserTransaction::TYPE_DEBIT,
                'message' => $transaction_message,
                'status' => $orderStatus == 'completed-api' ? UserTransaction::STATUS_SUCCESSFUL : UserTransaction::STATUS_PENDING,
                'source' => UserTransaction::SOURCE_CABLE,
                'reference' => $response['reference'],
                'fee' => 0 // No fee for cable subscriptions
            ]);

            $history = CableHistory::where([
                'user_id' => auth()->id(),
                'card_number' => $request->card_number,
                'provider_code' => $request->provider_code,
            ])->first();

            if ($history) {
                $history->update([
                    'updated_at' => now(),
                ]);
            } else {
                CableHistory::create([
                    'user_id' => auth()->id(),
                    'card_number' => $request->card_number ?? null,
                    'phone' => $request->phone ?? null,
                    'provider_code' => $request->provider_code,
                    'package_code' => $package->package_code,
                    'customer_name' => $request->customer['customer_name'] ?? null,
                ]);
            }

            DB::commit();

            // add token to response if available
            $response['data']['token'] = $token;

            return $this->success($response['data'], $statusMessage);
        } catch (Exception $ex) {
            return $this->error($ex->getMessage());
        }
    }
}
