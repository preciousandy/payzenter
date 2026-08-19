<?php

namespace App\Livewire\Savings;

use App\Models\GroupSavings as ModelsGroupSavings;
use App\Services\HashIdService;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class GroupSavings extends Component
{
    use LivewireAlert;

    public $savings = [];

    public $groupCode;

    public $totalSavingsDonations = 0;

    public function mount()
    {
        $this->savings = auth()->user()->groupSavings;

        $this->totalSavingsDonations = auth()->user()->groupSavingsDonations->sum('amount');
    }

    public function checkInvite()
    {
        $this->validate([
            'groupCode' => 'required'
        ]);

        try {
            $groupSavings = (new HashIdService)->getRecord($this->groupCode, ModelsGroupSavings::class);
        } catch (\Throwable $th) {
            $this->alert('error', 'Invalid group code');

            return;
        }

        return redirect()->route('savings.group-savings.invite', ['hashId' => $groupSavings->hash_id]);
    }

    public function render()
    {
        return view('livewire.savings.group-savings')->title('Group Savings');
    }
}
