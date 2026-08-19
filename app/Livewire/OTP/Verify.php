<?php

namespace App\Livewire\OTP;

use App\Services\OTPService;
use App\Services\UserService;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Verify extends Component
{
    use LivewireAlert;


    public $otp;
    public $isCodeSent = false;

    public function sendOTP()
    {
        $otp_service = new OTPService;

        $response =    $otp_service->generateOTP(auth()->user()->email);

        if ($response['status'] == 'error') {
            return $this->alert('error', $response['message']);
        }

        $this->isCodeSent = true;
    }

    public function render()
    {
        return view('livewire.o-t-p.verify')->title('Verify OTP');
    }
}
