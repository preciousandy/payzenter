<?php

namespace App\Livewire\Bills;

use App\Models\Cable;
use App\Models\Network;
use Livewire\Component;

class Index extends Component
{
    public $networks;

    public $cables;

    public function mount()
    {
        $this->networks = Network::all();

        $this->cables = Cable::all();
    }
    public function render()
    {
        return view('livewire.bills.index')->title('Bills');
    }
}
