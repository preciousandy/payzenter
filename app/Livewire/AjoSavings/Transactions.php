<?php

namespace App\Livewire\AjoSavings;

use App\Models\AjoSavings;
use App\Services\HashIdService;
use Livewire\Component;

class Transactions extends Component
{
    public $savings;
    public $transactions;

    public function mount($hash_id)
    {
        $this->savings = (new HashIdService)->getRecord($hash_id, AjoSavings::class);

        $this->transactions = $this->savings->transactions()->latest()->get();
    }

    public function render()
    {
        return view('livewire.ajo-savings.transactions')->title('Ajo Savings Transactions');
    }
}
