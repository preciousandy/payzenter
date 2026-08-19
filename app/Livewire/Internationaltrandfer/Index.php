<?php

namespace App\Livewire\Internationaltrandfer;

use App\Models\InternationalTransfer;
use Livewire\Component;

class Index extends Component
{
    public $transfers;
    public $totalVolume;

    public function mount()
    {
        $this->transfers = InternationalTransfer::whereUserId(auth()->id())->latest()->get();


        $this->totalVolume = $this->transfers->sum('amount_deducted_in_local_currency');
    }

    public function render()
    {
        return view('livewire.internationaltrandfer.index')->title('International Transfers');
    }
}
