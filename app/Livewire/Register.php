<?php

namespace App\Livewire;

use App\Models\Kyc;
use App\Models\User;
use App\Rules\StrongPassword;
use App\Services\OTPService;
use App\Utils\Roles;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;


class Register extends Component
{
    // Step Tracker
    public $currentStep = 1;

    // Step 1: User Info (Existing)
    public $first_name, $last_name, $email, $phone, $password, $password_confirmation;

    // Step 2: Address Info
    public $city, $state, $lga, $postal_code;


    public function mount()
    {
        
        if (request()->has('referral')) {
            $nextSevenDays = 7 * 24 * 60;
            cookie()->queue('referral', request()->referral, $nextSevenDays);
        }
    }

    private function generateUniqueMerchantId(): string
    {
        do {
            
            $merchantId = 'M' . Str::random(8);
        } while (User::where('merchant_id', $merchantId)->exists());

        return $merchantId;
    }



    public function render()
    {
        
        return view('livewire.register')->extends('layouts.auth');
    }



    private function stepOneRules()
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'phone' => ['nullable', 'string', 'digits_between:10,12', 'unique:' . User::class],
            'password' => ['required', 'confirmed', new StrongPassword],
        ];
    }

    private function stepTwoRules()
    {
        return [
            'city' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:255'],
            'lga' => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:255'],
        ];
    }

    public function firstStepSubmit()
    {
        $this->validate($this->stepOneRules());
        $this->currentStep = 2;
    }

    public function back($step)
    {
        $this->currentStep = $step;
    }


  
    public function store()
    {
        $this->validate(array_merge(
            $this->stepOneRules(),
            $this->stepTwoRules()
        ));

        $merchantId = $this->generateUniqueMerchantId();
        
        $user = User::create([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'password' => Hash::make($this->password),
            'wallet_balance' => 0.00,
            'merchant_id' => $merchantId,
            'city' => $this->city,
            'state' => $this->state,
            'lga' => $this->lga,
            'postal_code' => $this->postal_code,
            'kyc_status' => Kyc::STATUS_UNVERIFIED,
            'platform' => User::PLATFORM_WEB,
        ]);


        
        $otp_service = new OTPService();
        $response = $otp_service->generateOTP($user->email);

        if ($response['status'] == 'error') {
            
            $user->delete(); 
            return redirect()->route('register')->with('error', $response['message']);
        }

        // 5. Login and Redirect
        // $user->assignRole(Roles::CUSTOMER);
        Auth::login($user);
        session()->put('login_verified', true);

        return redirect()->route('verification');
    }
}