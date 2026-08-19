<?php

namespace App\Livewire\OTP;

use App\Services\OTPService;
use Illuminate\Support\Facades\Session;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Validate extends Component
{
    use LivewireAlert;

    public $pin;


    public function render()
    {
        return view('livewire.o-t-p.validate')->title('Validate OTP');
    }

    public function verifyOTP()
    {

        $this->validate([
            'pin' => 'required|numeric|digits:6',
        ]);

        $otp_service = new OTPService;

        $reponse = $otp_service->verifyOTP(auth()->user()->email, $this->pin);


        if ($reponse['status'] == 'error') {
            return $this->alert('error', $reponse['message']);
        }

        Session::put('otp_verified', true);

        $this->flash('success', 'OTP verified successfully', [], url(Session::get('url.intended', '/home')));
    }
}
