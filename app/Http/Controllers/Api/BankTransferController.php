<?php

namespace App\Http\Controllers\Api;

use App\Helpers\SettingHelper;
use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use App\Models\Transfer;
use App\Models\UserTransaction;
use App\Services\TransferService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use KingFlamez\Rave\Facades\Rave as Flutterwave;


class BankTransferController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        try {
            $data['fixed_transfer_fee'] = (int) SettingHelper::get(SettingHelper::FIXED_TRANSFER_FEE);
            $data['percentage_transfer_fee'] = (float) SettingHelper::get(SettingHelper::TRANSFER_FEE);
            $data['recent_tranfers'] = auth()->user()->bankTransfers()->where('type', Transfer::TYPE_BANK)->latest()->limit(5)->get()->unique('account_number');
            $data['banks'] = cache()->remember('banks', 60 * 60 * 24 * 2, function () {
                return getDotBanks();
            });

            return $this->success($data, 'Bank transfer data retrieved successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function verifyCustomer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bank_code' => 'required',
            'account_number' => 'required|numeric|digits:10',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $transferService = new TransferService();

            $response = $transferService->getDotAccount($request->bank_code, $request->account_number);

            if ($response['status'] == 'error') {
                throw new \Exception($response['message']);
            }

            return $this->success($response['data'], 'Customer verified successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function getBeneficiairies(Request $request)
    {
        try {
            $recentTransfers = Transfer::where('user_id', auth()->id())
                ->latest()
                ->get()
                ->unique('account_number');

            $favorites = Beneficiary::where('user_id', auth()->id())
                ->where('type', Beneficiary::TYPE_BANK)
                ->get();

            $transfers = $recentTransfers->each(function ($transfer) {
                $transfer->is_beneficiary = Beneficiary::where('user_id', auth()->id())
                    ->where('type', Beneficiary::TYPE_BANK)
                    ->where('acct_number', $transfer->account_number)
                    ->where('bank_code', $transfer->bank_code)
                    ->exists();
            })->values();

            $data['transfers'] = $transfers;

            $data['favorites'] = $favorites;


            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }


    public function toggleFavoriteBeneficiary(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transfer_id' => 'required|exists:transfers,id',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $transfer = Transfer::find($request->transfer_id);

            $beneficiary = Beneficiary::where('user_id', auth()->id())
                ->where('acct_number', $transfer->account_number)
                ->where('bank_code', $transfer->bank_code)
                ->first();

            if ($beneficiary) {
                $beneficiary->delete();
                return  $this->success($beneficiary, 'Bank details removed from favourites',);
            }

            $newBeneficiary =   Beneficiary::create([
                'user_id' => auth()->id(),
                'acct_number' => $transfer->account_number,
                'bank_code' => $transfer->bank_code,
                'type' => Beneficiary::TYPE_BANK,
                'acct_name' => $transfer->account_name,
                'bank_name' => $transfer->bank_name,
            ]);


            return $this->success($newBeneficiary, 'Bank details added to favourites',);
        } catch (\Exception $th) {
            return $this->error($th->getMessage());
        }
    }

    public function transferMoney(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bank_code' => 'required',
            'account_number' => 'required|numeric|digits:10',
            'amount' => 'required|numeric|min:100',
            'fee_amount' => 'required|numeric',
            'total_amount' => 'required|numeric|min:100',
            'description' => 'nullable|string',
            'account_name' => 'required|string',
            'bank_name' => 'required|string',
            'source_link' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $reference = generateOutgoingReference();

        $transferService = new TransferService();

        try {
            $transferReference = null;
            $provider = $transferService->resolveTransferProvider();
            $transferStatus = Transfer::STATUS_PENDING;

            $description = $request->description ?? "";

            if ($provider === 'palmpay') {
                $transferReference = $transferService->initiatePalmPayTransfer(
                    $request->bank_code,
                    $request->account_number,
                    $request->account_name,
                    $request->amount,
                    $reference,
                    $description
                );

                if (($transferReference['state'] ?? null) === 'FAILED') {
                    Log::warning("PalmPay API transfer failed (" . ($transferReference['message'] ?? '') . "). Attempting Flutterwave fallback...");
                    if ($transferService->canUseFlutterwaveTransfer()) {
                        $transferReference = $transferService->initiateFlutterwaveTransfer(
                            $request->bank_code,
                            $request->account_number,
                            $request->account_name,
                            $request->amount,
                            $reference,
                            $description
                        );
                        $provider = 'flutterwave';
                    }
                }
            } elseif ($provider === 'dot') {
                $transferReference = $transferService->initaiteDotTransfer($request->bank_code, $request->account_number, $request->account_name, $request->amount, $reference, $description,  $request->bank_name);
            } else {
                $transferReference = $transferService->initiateFlutterwaveTransfer($request->bank_code, $request->account_number, $request->account_name, $request->amount, $reference, $description);
            }

            $providerState = is_array($transferReference) ? ($transferReference['state'] ?? null) : null;
            if ($providerState === null) {
                $message = is_array($transferReference) ? ($transferReference['message'] ?? null) : null;
                throw new \Exception($message ?: 'Transfer failed. Please try again later.');
            }

            if ($provider === 'dot') {
                if ($providerState === Transfer::DOT_TRANSFER_STATUS_SUCCEEDED) {
                    $transferStatus = Transfer::STATUS_SUCCESS;
                } elseif ($providerState === Transfer::DOT_TRANSFER_STATUS_FAILED || $providerState === Transfer::STATUS_FAILED) {
                    $transferStatus = Transfer::STATUS_FAILED;
                } else {
                    $transferStatus = Transfer::STATUS_PENDING;
                }
            } else {
                if ($providerState === Transfer::STATUS_SUCCESS || $providerState === 'SUCCESSFUL') {
                    $transferStatus = Transfer::STATUS_SUCCESS;
                } elseif ($providerState === Transfer::STATUS_FAILED || $providerState === 'FAILED') {
                    $transferStatus = Transfer::STATUS_FAILED;
                } else {
                    $transferStatus = Transfer::STATUS_PENDING;
                }
            }

            if ($transferStatus !== Transfer::STATUS_FAILED) {

                DB::beginTransaction();

                $user = auth()->user()->lockForUpdate()->first();
                if ($request->total_amount > $user->wallet_balance) {
                    DB::rollBack();
                    return $this->error('Insufficient funds');
                }

                $transferReferenceValue = (string) ($transferReference['receiptNumber'] ?? $reference);

                $transfer = Transfer::create([
                    'user_id' => auth()->id(),
                    'reference' => $transferReferenceValue,
                    'status' =>  $transferStatus,
                    'amount' => $request->amount,
                    'fee' => $request->fee_amount,
                    'description' => $request->description,
                    'type' => Transfer::TYPE_BANK,
                    'account_name' => $request->account_name,
                    'account_number' => $request->account_number,
                    'bank_name' => $request->bank_name,
                    'bank_code' => $request->bank_code,
                    'transfer_id' =>  $transferReference['requestId'],
                    'response_data' => [
                        'provider' => $provider,
                        'raw' => $transferReference['raw'] ?? null,
                    ],
                    'source_link' => $request->source_link ?? null,
                ]);


                $user->decrement('wallet_balance', $request->total_amount);

                $user->transactions()->create([
                    'reference' => $transferReferenceValue,
                    'type' => UserTransaction::TYPE_DEBIT,
                    'amount' => $request->total_amount,
                    'source' => UserTransaction::SOURCE_BANK_TRANSFER,
                    'status' => $transferStatus === Transfer::STATUS_SUCCESS
                        ? UserTransaction::STATUS_SUCCESSFUL
                        : UserTransaction::STATUS_PENDING,
                    'message' => "Transfered {$request->amount} to {$request->account_name} {$request->account_number}",
                    'fee' => $request->fee_amount
                ]);


                DB::commit();

                return $this->success($transfer, 'Transfer initiated successfully');
            } else {
                $error_message = $transferReference['message'] ?? 'Transfer failed. Please try again later.';

                throw new \Exception($error_message);
            }
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
