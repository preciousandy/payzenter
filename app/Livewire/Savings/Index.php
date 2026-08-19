<?php

namespace App\Livewire\Savings;

use App\Helpers\SettingHelper;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        $lockedSavingsInterestRate = SettingHelper::get(SettingHelper::LOCKED_SAVINGS_INTEREST, 0);

        $unlockedSavingsInterestRate = SettingHelper::get(SettingHelper::UNLOCKED_SAVINGS_INTEREST, 0);

        $groupSavings = auth()->user()->groupSavings()->take(2)->get();

        return view('livewire.savings.index', [
            'interestSavings' => auth()->user()->savings()->active()->latest()->take(2)->get(),
            'lockedSavingsInterestRate' => $lockedSavingsInterestRate,
            'unlockedSavingsInterestRate' => $unlockedSavingsInterestRate,
            'groupSavings' => $groupSavings,
        ])->title('Savings');
    }
}
