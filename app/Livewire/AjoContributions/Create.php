<?php

namespace App\Livewire\AjoContributions;

use App\Models\AjoContribution;
use App\Models\AjoContributionMember;
use App\Models\AjoContributionRound;
use App\Utils\Roles;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use LivewireAlert, WithFileUploads;
    public $name, $type, $frequency, $no_of_members, $amount, $start_date, $cover_image;

    public $description;

    public function render()
    {
        return view('livewire.ajo-contributions.create')->title('Create Ajo Contribution');
    }

    public function create()
    {
        $this->validate([
            'name' => 'required',
            'type' => 'required',
            'frequency' => 'required',
            'no_of_members' => 'required',
            'amount' => 'required',
            'start_date' => 'required',
            'cover_image' => 'nullable|image',
            'description' => 'nullable'
        ]);

        $cover_image = null;
        if ($this->cover_image) {
            $cover_image = $this->cover_image->store('ajo-contributions', 'public');
        }

        DB::beginTransaction();

        $ajo = AjoContribution::create([
            'name' => $this->name,
            'type' => $this->type,
            'frequency' => $this->frequency,
            'no_of_members' => $this->no_of_members,
            'amount' => $this->amount,
            'start_date' => $this->start_date,
            'current_round' => 1,
            'cover_image' => $cover_image,
            'description' => $this->description
        ]);

        if ($this->type === AjoContribution::TYPE_PERSONAL) {
            $ajo->update([
                'next_round_date' => match ($this->frequency) {
                    AjoContribution::FREQUENCY_WEEKLY => $ajo->start_date->addWeek(),
                    AjoContribution::FREQUENCY_MONTHLY => $ajo->start_date->addMonth(),
                    default => $ajo->start_date->addYear()
                }
            ]);
        }

        AjoContributionMember::create([
            'ajo_contribution_id' => $ajo->id,
            'user_id' => auth()->id(),
            'role' => Roles::ADMIN,
            'is_contributing' => $this->type === 'personal' ? true : false,
            'order_number' => $this->type === 'personal' ? 1 : null,
        ]);

        AjoContributionRound::create([
            'ajo_contribution_id' => $ajo->id,
            'user_id' => $this->type === 'personal' ? auth()->id() : null,
            'order' => 1,
            'balance' => 0,
            'created_at' => $this->start_date,
            'status' => AjoContributionRound::STATUS_ON_GOING
        ]);

        DB::commit();




        $this->flash('success', 'Ajo Contribution created successfully', [], route('ajo-contributions.success', $ajo->hash_id));
    }
}
