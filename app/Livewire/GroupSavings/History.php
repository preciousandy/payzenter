<?php

namespace App\Livewire\GroupSavings;

use Livewire\Component;

class History extends Component
{
    public $groupSavings = [];

    public function mount()
    {
        $this->groupSavings = auth()->user()->closedGroupSavings;
    }

    public function render()
    {
        return view('livewire.group-savings.history')->title('Group Savings History');
    }
}
