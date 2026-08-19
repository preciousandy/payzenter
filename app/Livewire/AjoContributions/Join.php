<?php

namespace App\Livewire\AjoContributions;

use App\Models\AjoContribution;
use App\Models\AjoContributionMember;
use App\Models\AjoContributionRound;
use App\Services\HashIdService;
use App\Utils\Roles;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Illuminate\Support\Str;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Join extends Component
{
    use LivewireAlert;
    public $ajoContribution;
    public $code;

    public function mount()
    {
        if (request()->has('code')) {
            $this->ajoContribution = (new HashIdService)->getRecord(request('code'), AjoContribution::class);
        }
    }


    public function render()
    {
        return view('livewire.ajo-contributions.join')->title('Join Ajo Contribution');
    }

    public function checkAjoContribution()
    {
        $this->validate([
            'code' => 'required'
        ]);



        $this->ajoContribution = (new HashIdService)->getRecord($this->code, AjoContribution::class);
    }

    public function joinAjoContribution()
    {
        $previuosOrderNumber  = (int) $this->ajoContribution->members()->first()->pivot->order_number;

        if ($this->ajoContribution->type == AjoContribution::TYPE_PERSONAL && $this->ajoContribution->no_of_members === $this->ajoContribution->members()->count()) {
            return $this->flash('error', 'This Ajo Contribution is already full', [], route('ajo-contributions.index'));
        }

        // the -1 is to remove the admin from the count
        if ($this->ajoContribution->type == AjoContribution::TYPE_BUSINESS && $this->ajoContribution->no_of_members  === $this->ajoContribution->members()->count() - 1) {
            return $this->flash('error', 'This Ajo Contribution is already full', [], route('ajo-contributions.index'));
        }

        if ($this->ajoContribution->members()->where('user_id', auth()->id())->exists()) {
            return    $this->flash('error', 'You are already a member of this Ajo Contribution', [], route('ajo-contributions.index'));
        }

        DB::beginTransaction();


        $lastOrderNumber = $this->ajoContribution->members()->orderByPivot('order_number', 'desc')->first()->pivot->order_number;


        AjoContributionMember::create([
            'ajo_contribution_id' => $this->ajoContribution->id,
            'user_id' => auth()->id(),
            'is_contributing' => true,
            'role' => 'member',
            'order_number' => $lastOrderNumber + 1
        ]);


        if ($this->ajoContribution->type === AjoContribution::TYPE_BUSINESS) {

            if ($this->ajoContribution->members->count() === 1) {
                AjoContributionRound::where([
                    'ajo_contribution_id' => $this->ajoContribution->id,
                    'order' => 1
                ])->update([
                    'user_id' => auth()->id()
                ]);


                $this->ajoContribution->update([
                    'next_round_date' => match ($this->ajoContribution->frequency) {
                        AjoContribution::FREQUENCY_WEEKLY => $this->ajoContribution->start_date->addWeek(),
                        AjoContribution::FREQUENCY_MONTHLY => $this->ajoContribution->start_date->addMonth(),
                        default => $this->ajoContribution->start_date->addYear()
                    }
                ]);
            } else {
                AjoContributionRound::create([
                    'ajo_contribution_id' => $this->ajoContribution->id,
                    'user_id' => auth()->id(),
                    'order' => $lastOrderNumber + 1,
                    'balance' => 0,
                    'status' => AjoContributionRound::STATUS_AWAITING
                ]);
            }
        } else {
            AjoContributionRound::create([
                'ajo_contribution_id' => $this->ajoContribution->id,
                'user_id' => auth()->id(),
                'order' => $lastOrderNumber + 1,
                'balance' => 0,
                'status' => AjoContributionRound::STATUS_AWAITING
            ]);
        }

        DB::commit();



        $this->flash('success', 'You have successfully joined the Ajo Contribution', [], route('ajo-contributions.index'));
    }
}
