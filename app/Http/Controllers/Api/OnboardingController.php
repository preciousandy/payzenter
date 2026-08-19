<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OTPService;
use App\Services\UserService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class OnboardingController extends Controller
{
    use ApiResponseTrait;


    public function resendOTP(Request $request)
    {
        try {
            $otp_service = new OTPService();

            $response = $otp_service->generateOTP(auth()->user()->email);

            if ($response['status'] == 'error') {
                return $this->error($response['message']);
            }

            return $this->success([], 'OTP sent successfully');
        } catch (\Exception $e) {

            return $this->error($e->getMessage());
        }
    }

    public function verifyOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp_code' => 'required|numeric|digits:6'
        ]);


        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $user = $request->user();

            if ($user->email == User::TEST_EMAIL && $request->otp_code == '123456') {
                $user->update([
                    'is_otp_verified' => true
                ]);

                $token = $user->createToken('auth_token')->plainTextToken;

                $data['token'] = $token;
                $data['user'] = $user;

                return $this->success($data, 'OTP verified successfully');
            }


            $otp_service = new OTPService();

            $response =   $otp_service->verifyOTP(auth()->user()->email, $request->otp_code);

            if ($response['status'] == 'error') {
                return $this->error($response['message']);
            }

            auth()->user()->update([
                'is_otp_verified' => true
            ]);

            $user = auth()->user();

            $token = $user->createToken('auth_token')->plainTextToken;

            $data['token'] = $token;
            $data['user'] = $user;

            return $this->success($data, 'OTP verified successfully');
        } catch (\Exception $e) {

            return $this->error($e->getMessage());
        }
    }


    public function createPin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pin' => 'required|numeric|digits:4'
        ]);


        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {

            auth()->user()->update([
                'pin' => Hash::make($request->pin)
            ]);

            return $this->success([], 'Transaction PIN set successfully');
        } catch (\Exception $e) {

            return $this->error($e->getMessage());
        }
    }

    public function createAccountNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'bvn' => 'required|digits:11',
        ]);


        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {

            $userService = new UserService();

            $data = $userService->generateAccountNumber(auth()->user(), $request->bvn);

            if ($data['status'] == 'error') {
                return $this->error($data['message']);
            }


            $account_number = $data['data']['account_number'];
            $bank_name = $data['data']['bank_name'];

            $response = $userService->generateQuidaxWallet($request->email);

            if ($response['status'] == 'error') {
                return $this->error($response['message']);
            }


            $quidax_wallet_id = $response['data']['id'];
            $quidax_sn = $response['data']['sn'];

            DB::beginTransaction();

            auth()->user()->update([
                'virtual_account_number' => $account_number,
                'virtual_bank_name' => $bank_name,
                'quidax_id' => $quidax_wallet_id,
                'quidax_sn' => $quidax_sn
            ]);

            DB::commit();

            return $this->success([], 'Virtual account number and Crypto wallet created successfully');
        } catch (\Exception $e) {

            return $this->error($e->getMessage());
        }
    }
}
