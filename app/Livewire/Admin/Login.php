<?php

namespace App\Livewire\Admin;

use App\Utils\Roles;
use Livewire\Component;

class Login extends Component
{
    public $email;
    public $password;
    public $remember;

    public function mount()
    {
        if (auth()->check() && !auth()->user()->hasRole(Roles::CUSTOMER)) {
            return redirect()->route('admin.dashboard');
        }
    }

    public function render()
    {
        return view('livewire.admin.login')->extends('layouts.auth');
    }

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!auth()->attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            $this->addError('email', 'Email or password is incorrect.');
            return;
        }

        if (auth()->user()->hasRole(Roles::CUSTOMER)) {
            auth()->logout();
            return redirect()->route('welcome')->with('error', 'You are not authorized to access this page.');
        }

        return redirect()->route('admin.dashboard');
    }
}
