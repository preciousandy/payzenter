<?php

namespace App\Livewire\InterestSavings;

use App\Helpers\SettingHelper;
use Livewire\Component;

class History extends Component
{
    public $savings = [];

    public $lockedSavingsInterestRate = 0;

    public $unlockedSavingsInterestRate = 0;

    public function mount()
    {
        $this->savings = auth()->user()->savings()->closed()->latest()->get();

        $this->lockedSavingsInterestRate = SettingHelper::get(SettingHelper::LOCKED_SAVINGS_INTEREST, 0);

        $this->unlockedSavingsInterestRate = SettingHelper::get(SettingHelper::UNLOCKED_SAVINGS_INTEREST, 0);
    }

    public function render()
    {
        return view('livewire.interest-savings.history')->title('Interest Savings History');
    }
}
