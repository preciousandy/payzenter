<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Password;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class ForgotPassword extends Component
{
    use LivewireAlert;

    public $email;


    public function sendLink()
    {
        $this->validate([
            'email' => 'email|required|max:255|exists:users,email',
        ]);


        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            [
                'email' => $this->email
            ]
        );



        if ($status !== Password::RESET_LINK_SENT) {
            return $this->flash('error', "Failed to send password reset link", [], route('forgot_password'));
        }



        $this->flash('success', "Password reset link sent successfully", [], route('welcome'));
    }


    public function render()
    {
        return view('livewire.forgot-password')->layout('layouts.auth')->title('Forgot Password');
    }
}
