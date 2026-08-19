<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class AccountLevels extends Component
{
    public $levels = [
        "green" => [
            "min" => 0,
            "max" => 100000
        ],
        "blue" => [
            "min" => 100000,
            "max" => 1000000
        ],
        "black" => [
            "min" => 1000000,
            "max" => 10000000
        ],
        "gold" => [
            "min" => 10000000,
            "max" => null
        ]
    ];

    public $current_level;

    public function mount()
    {
        $this->current_level = auth()->user()->level;
    }

    public function render()
    {
        return view('livewire.account-levels')->layout('layouts.auth')->title('Account Levels');
    }
}
