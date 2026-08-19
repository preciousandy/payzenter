<?php

namespace App\Livewire;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Livewire\Component;
use Illuminate\Validation\Rules;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Support\Str;


class ResetPassword extends Component
{
    use LivewireAlert;


    public $token;
    public $email,  $password, $password_confirmation;

    public function mount($token)
    {
        $this->token = $token;
        $this->email = request('email');
    }

    public function resetPassword()
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.

        $data = [
            'email' => $this->email,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
            'token' => $this->token,
        ];
        $status = Password::reset(
            $data,
            function ($user) {
                $user->forceFill([
                    'password' => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])->save();

                // event(new PasswordReset($user));
            }
        );

        $this->flash('success', "Password changed successfully", [], route('login'));
    }
    public function render()
    {
        return view('livewire.reset-password')->layout('layouts.auth')->title('Reset Password');
    }
}
