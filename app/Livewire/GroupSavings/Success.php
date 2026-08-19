<?php

namespace App\Livewire\GroupSavings;

use App\Models\GroupSavings;
use App\Services\HashIdService;
use Livewire\Component;

class Success extends Component
{
    public $groupSavings;

    public function mount($hashId)
    {
        $this->groupSavings = (new HashIdService)->getRecord($hashId, GroupSavings::class);
    }

    public function render()
    {
        return view('livewire.group-savings.success')->title('Group Savings Invitation Success');
    }
}
