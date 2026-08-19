<?php

namespace App\Livewire\Users;

use Illuminate\Support\Facades\Hash;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class ChangeTansferPin extends Component
{
    use LivewireAlert;

    public $user;

    public $current_pin, $pin, $confirm_pin;

    public function mount()
    {
        $this->user = auth()->user();
    }

    public function update()
    {
        $this->validate([
            'current_pin' => 'required|numeric|digits:4',
            'pin' => 'required|numeric|digits:4',
            'confirm_pin' => 'required|numeric|digits:4|same:pin',
        ]);

        if (!Hash::check($this->current_pin, $this->user->pin)) {
            return $this->addError('current_pin', 'Incorrent PIN');
        }

        $this->user->pin = Hash::make($this->confirm_pin);
        $this->user->save();

        return $this->flash('success', 'Transaction PIN updated successfully!', [], route('profile'));
    }


    public function render()
    {
        return view('livewire.users.change-tansfer-pin')->title('Change Transaction PIN');
    }
}
