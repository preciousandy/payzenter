<?php

namespace App\Livewire\Users;

use App\Services\OTPService;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Verification extends Component
{
    use LivewireAlert;
    public $pin;

    public $remainingTime = 60; // Initial countdown time in seconds
    public $canResend = false;

    public function mount()
    {
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


    public function render()
    {
        return view('livewire.users.verification')->extends('layouts.auth')->title('Email Verification');
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

        auth()->user()->update([
            'is_otp_verified' => true
        ]);

        return $this->flash('success', 'Email verified successfully! Welcome to your dashboard.', [], route('home'));
    }
}
