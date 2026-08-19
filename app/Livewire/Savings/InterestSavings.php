<?php

namespace App\Livewire\Savings;

use App\Helpers\SettingHelper;
use Livewire\Component;

class InterestSavings extends Component
{
    public $savings = [];
    public $totalSavings = 0;

    public $lockedSavingsInterestRate = 0;

    public $unlockedSavingsInterestRate = 0;

    public function mount()
    {
        $this->savings = auth()->user()->savings()->active()->latest()->get();
        $this->totalSavings = auth()->user()->savings()->active()->sum('balance');

        $this->lockedSavingsInterestRate = SettingHelper::get(SettingHelper::LOCKED_SAVINGS_INTEREST, 0);

        $this->unlockedSavingsInterestRate = SettingHelper::get(SettingHelper::UNLOCKED_SAVINGS_INTEREST, 0);
    }

    public function render()
    {
        return view('livewire.savings.interest-savings')->title('Interest Savings');
    }
}
