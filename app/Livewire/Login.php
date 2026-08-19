<?php

namespace App\Livewire;

use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use App\Services\OTPService;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Login extends Component
{



    public $email = '';
    public $password = '';
    public $remember = false;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required',
    ];

    public function login()
    {
        $this->validate();



        if (!$this->ensureIsNotRateLimited()) {
            return;
        }

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            // Authentication passed
            $this->resetRateLimiter();

            if (env('APP_ENV') == 'local') {

                return redirect()->route('home');
            }
            return redirect()->route('verify-login');
        }

        // Authentication failed
        $this->incrementRateLimiter();

        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }

    private function ensureIsNotRateLimited(): bool
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return true;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => [
                trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ],
        ]);
    }

    private function incrementRateLimiter()
    {
        RateLimiter::hit($this->throttleKey(), 60); // Lock for 1 minute
    }

    private function resetRateLimiter()
    {
        RateLimiter::clear($this->throttleKey());
    }

    private function throttleKey(): string
    {
        return mb_strtolower($this->email) . '|' . request()->ip();
    }

    public function render()
    {
        return view('livewire.login')->extends('layouts.auth')->title('Login');
    }
}
