<?php

namespace App\Livewire\AjoSavings;

use App\Services\HashIdService;
use Livewire\Component;

class Index extends Component
{
    public $savings = [];
    public $savingsHistory = [];
    public $ajoSavingsBalance = 0;
    public $referralCode;

    public function mount()
    {
        $this->savings = auth()->user()->ajoSavings()->active()->latest()->get();
        $this->ajoSavingsBalance = auth()->user()->ajoSavings()->active()->sum('balance');
        $this->savingsHistory = auth()->user()->ajoSavings()->closed()->latest()->get()->take(5);
        $this->referralCode = auth()->user()->hash_id;
    }

    public function render()
    {
        return view('livewire.ajo-savings.index')->title('Ajo Savings');
    }
}
