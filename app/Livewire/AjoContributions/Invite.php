<?php

namespace App\Livewire\AjoContributions;

use App\Models\AjoContribution;
use App\Models\AjoContributionMember;
use App\Models\AjoContributionRound;
use App\Services\HashIdService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Invite extends Component
{
    public $ajo;

    public function mount($hashId)
    {
        $this->ajo = (new HashIdService)->getRecord($hashId, AjoContribution::class);
    }


    public function render()
    {
        return view('livewire.ajo-contributions.invite')->title('Invite to Ajo Contribution');
    }


    public function joinAjoContribution()
    {

        if ($this->ajo->no_of_members === $this->ajo->members()->count()) {
            return redirect()->route('ajo-contributions.index')->with('error', 'Ajo Contribution is full');
        }

        if ($this->ajo->members()->where('user_id', auth()->id())->exists()) {
            return redirect()->route('ajo-contributions.index')->with('error', 'You are already a member of this Ajo Contribution');
        }


        $previuosOrderNumber  = (int) $this->ajo->members()->first()->pivot->order_number;

        DB::beginTransaction();


        AjoContributionMember::create([
            'ajo_contribution_id' => $this->ajo->id,
            'user_id' => auth()->id(),
            'is_contributing' => true,
            'role' => 'member',
            'order_number' => $previuosOrderNumber + 1
        ]);

        if ($this->ajo->type === AjoContribution::TYPE_BUSINESS && $previuosOrderNumber == 0) {
            AjoContributionRound::where([
                'ajo_contribution_id' => $this->ajo->id,
                'order' => 1
            ])->update([
                'user_id' => auth()->id()
            ]);
        }

        DB::commit();

        return redirect()->route('ajo-contributions.index')->with('success', 'You have successfully joined the Ajo Contribution');
    }
}
