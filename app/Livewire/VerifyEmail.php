<?php

namespace App\Livewire;

use App\Services\OTPService;
use App\Services\SendLoginNotificationService;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class VerifyEmail extends Component
{
    use LivewireAlert;
    public $pin;

    public $remainingTime = 60; // Initial countdown time in seconds
    public $canResend = false;

    public function mount()
    {
        $otp_service = new OTPService();

        $response = $otp_service->generateOTP(auth()->user()->email);

        if ($response['status'] == 'error') {
            auth()->logout();
            return $this->flash('error', $response['message'], [], route('login'));
        }

        $this->startCountdown();
    }

    public function startCountdown()
    {
        $this->remainingTime = 60;
        $this->canResend = false;
    }

    public function resendOtp()
    {
        $otp_service = new OTPService();

        $response = $otp_service->generateOTP(auth()->user()->email);

        if ($response['status'] == 'error') {
            return $this->alert('error', $response['message']);
        }

        $this->alert('success', 'OTP sent successfully');

        // Restart the countdown
        $this->startCountdown();
    }



    public function verify()
    {
        $this->validate([
            'pin' => 'required|numeric|digits:6'
        ]);

        $otp_service = new OTPService();

        $response =   $otp_service->verifyOTP(auth()->user()->email, $this->pin);

        if ($response['status'] == 'error') {
            return $this->alert('error', $response['message']);
        }

        // Send login notification
        (new SendLoginNotificationService())->handle();

        // Mark the user as verified
        session()->put('login_verified', true);

        return redirect()->route('home', ['modal' => getRandomElement()]);
    }


    public function render()
    {
        return view('livewire.verify-email')->extends('layouts.auth')->title('Email Verification');
    }
}
