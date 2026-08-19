<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kyc;
use App\Models\User;
use App\Rules\StrongPassword;
use App\Services\OTPService;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use App\Utils\ResponseMessage;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use ApiResponseTrait;

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => 'email|required|max:255|unique:users,email',
            'password' => ['required', 'confirmed', new StrongPassword],
            'phone' => 'required|string|digits_between:10,12|unique:users,phone',
            'device_name' => 'string|required',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {

            $user = User::create([
                'email' => $request->email,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'wallet_balance' => 0.00,
                'kyc_status' => Kyc::STATUS_UNVERIFIED,
                'platform' => User::PLATFORM_MOBILE,
            ]);

            $otp_service = new OTPService();

            $response = $otp_service->generateOTP($user->email);

            if ($response['status'] == 'error') {
                $user->delete();
                return $this->error($response['message'], 500);
            }

            $token = $user->createToken($request->device_name)->plainTextToken;

            $data['token'] = $token;
            $data['user'] = $user;

            return $this->success($data, 'Registration successful', 201);
        } catch (\Exception $e) {

            return $this->error($e->getMessage());
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'email|required|max:255',
            'password' => 'required|string|min:8',
            'ignore_otp' => 'boolean|nullable',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $user = User::firstWhere('email', $request->email);

            if (!$user || !Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect']
                ]);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            // Check for incomplete steps
            $incompleteSteps = [];
            if (!$user->is_otp_verified) {
                $incompleteSteps[] = [
                    'action' => 'otp_verification',
                    'message' => 'Please verify your email'
                ];
            }
            if (is_null($user->pin)) {
                $incompleteSteps[] = [
                    'action' => 'create_pin',
                    'message' => 'Please create a transaction pin'
                ];
            }
            if (is_null($user->username)) {
                $incompleteSteps[] = [
                    'action' => 'create_swiftpay_tag',
                    'message' => 'Please create a ' . config('app.name') . ' tag'
                ];
            }

            $data['token'] = $token;
            $data['user'] = $user;
            $data['incomplete_steps'] = $incompleteSteps;
            $data['skip_otp'] = $request->ignore_otp;

            // If ignore_otp is true, skip OTP generation
            if ($request->ignore_otp) {
                return $this->success($data, 'Login successful', 200);
            }

            // send OTP
            $otp_service = new OTPService();

            $response = $otp_service->generateOTP($user->email);

            if ($response['status'] == 'error') {

                // If OTP generation fails, delete the user and return an error
                $user->tokens()->delete();
                return $this->error($response['message'], 500);
            }


            return $this->success($data, 'Login successful', 200);
        } catch (\Exception $e) {

            return $this->error($e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        try {
            //delete all tokens
            $request->user()->tokens()->delete();

            return $this->success([], 'Logout successful');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'email|required|max:255|exists:users,email',
        ]);


        if ($validator->fails()) {
            return $this->validationError($validator);
        }
        try {
            $status = Password::sendResetLink(
                [
                    'email' => $request->email
                ]
            );

            if ($status !== Password::RESET_LINK_SENT) {
                return $this->error('Failed to send password reset link');
            }


            return $this->success([], 'Password reset link sent successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
