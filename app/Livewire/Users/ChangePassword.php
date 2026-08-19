<?php

namespace App\Livewire\Users;

use App\Rules\StrongPassword;
use Illuminate\Support\Facades\Hash;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class ChangePassword extends Component
{
    use LivewireAlert;

    public $user;
    public $current_password;
    public $password;
    public $password_confirmation;

    public function mount()
    {
        $this->user = auth()->user();
    }

    public function save()
    {
        $this->validate([
            'current_password' => 'required|min:8|current_password',
            'password' => ['required', 'confirmed', new StrongPassword],
        ]);

        $this->user->update([
            'password' => Hash::make($this->password),
        ]);

        auth()->logout();

        return $this->flash('success', 'Password has been changed successfully!', [], route('login'));
    }

    public function render()
    {
        return view('livewire.users.change-password')->title('Change Password');
    }
}
