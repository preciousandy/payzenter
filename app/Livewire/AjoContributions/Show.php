<?php

namespace App\Livewire\AjoContributions;

use App\Models\AjoContribution;
use App\Models\AjoContributionDonation;
use App\Models\AjoContributionMember;
use App\Models\AjoContributionRound;
use App\Models\User;
use App\Models\UserTransaction;
use App\Notifications\NotifyUser;
use App\Services\HashIdService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;

class Show extends Component
{
    use LivewireAlert;
    use WithFileUploads;

    public $ajoContribution;
    public $pin;
    public $members;
    public $isUserContributing;
    public $hasUserDonated;
    public $toggleAutoSave;
    public $rounds = [];

    public $activeRound;

    public $isAdmin = false;

    public $adminUser;

    public $name, $no_of_members, $cover_image;

    public $contributionTotalAmount = 0;

    public function mount($hashId)
    {
        $this->ajoContribution = (new HashIdService)->getRecord($hashId, AjoContribution::class);

        // put admin first in the list
        $this->members = $this->ajoContribution->members()->orderByPivot('created_at', 'asc')->get();

        $this->rounds = $this->ajoContribution->rounds()->whereNotNull('user_id')->get();

        $this->isUserContributing = $this->ajoContribution->members()->where('user_id', auth()->id())->first()->pivot->is_contributing;

        $this->hasUserDonated = $this->ajoContribution->activeRound()->donations()->where('user_id', auth()->id())->exists();


        $this->toggleAutoSave = $this->ajoContribution->members()->where('user_id', auth()->id())->first()->pivot->if_auto_save ? true : false;

        $this->isAdmin = $this->ajoContribution->members()->where('user_id', auth()->id())->first()->pivot->role == AjoContributionMember::ROLE_ADMIN;

        $this->adminUser = $this->ajoContribution->members()->where('role', AjoContributionMember::ROLE_ADMIN)->first();


        $this->name = $this->ajoContribution->name;
        $this->no_of_members = $this->ajoContribution->no_of_members;

        $this->activeRound = $this->ajoContribution->activeRound();

        $this->contributionTotalAmount = $this->ajoContribution->type === AjoContribution::TYPE_PERSONAL ? $this->ajoContribution->amount * $this->ajoContribution->members()->count() : $this->ajoContribution->amount *  ($this->ajoContribution->members()->count() - 1);

        // dd($this->ajoContribution->getActiveRoundDefaulters()->pluck('id')->toArray());
    }


    public function render()
    {
        return view('livewire.ajo-contributions.show')->title('Ajo Contribution Details');
    }

    public function addMoney()
    {
        $this->validate([
            'pin' => 'required|numeric|digits:4'
        ]);

        if ($this->ajoContribution->amount > auth()->user()->wallet_balance) {
            $this->alert('error', 'Insufficient funds');
            return;
        }

        if (!Hash::check($this->pin, auth()->user()->pin)) {
            $this->alert('error', 'Incorrect pin');
            return;
        }

        $currentRound = AjoContributionRound::where([
            'ajo_contribution_id' => $this->ajoContribution->id,
            'order' => $this->ajoContribution->current_round
        ])->first();


        DB::beginTransaction();

        $amount = $this->ajoContribution->amount;

        $beneficiary = $currentRound->beneficiary;

        $beneficiary->increment('ajo_contribution_wallet', $amount);

        $beneficiary->transactions()->create([
            'amount' => $amount,
            'type' => UserTransaction::TYPE_CREDIT,
            'message' => $amount . ' was added to your Ajo Contribution wallet from ' . $this->ajoContribution->name . ' Ajo contribution',
            'source' => UserTransaction::SOURCE_AJO_CONTRIBUTION,
            'status' => UserTransaction::STATUS_SUCCESSFUL,
            'fee' => 0 // No fee for AJO contributions
        ]);

        AjoContributionDonation::create([
            'user_id' => auth()->id(),
            'ajo_contribution_round_id' => $currentRound->id,
            'amount' =>  $this->ajoContribution->amount,
            'status' => UserTransaction::STATUS_SUCCESSFUL
        ]);

        auth()->user()->update([
            'wallet_balance' => auth()->user()->wallet_balance - $this->ajoContribution->amount
        ]);

        auth()->user()->transactions()->create([
            'amount' => $this->ajoContribution->amount,
            'type' => UserTransaction::TYPE_DEBIT,
            'status' => UserTransaction::STATUS_SUCCESSFUL,
            'message' => 'Added ' . $this->ajoContribution->amount . ' to ' . $this->ajoContribution->name . ' ajo contribution',
            'source' => UserTransaction::SOURCE_AJO_CONTRIBUTION,
            'fee' => 0 // No fee for AJO contributions
        ]);

        $currentRound->increment('balance', $this->ajoContribution->amount);

        // check if the user is still owing ajo contribution
        if ($this->ajoContribution->activeRound()->status === AjoContributionRound::STATUS_PENDING) {
            $is_still_owing_ajo_contribution = false;

            auth()->user()->ajoContributions->each(function ($ajo) use (&$is_still_owing_ajo_contribution) {
                if ($ajo->activeRound()->status !== AjoContributionRound::STATUS_PENDING) {
                    return;
                }
                $defaulters = $ajo->getActiveRoundDefaulters()->pluck('id')->toArray();

                if (in_array(auth()->id(), $defaulters)) {
                    $is_still_owing_ajo_contribution = true;
                }
            });

            if (!$is_still_owing_ajo_contribution) {
                auth()->user()->update([
                    'is_owing_ajo_contribution' => false
                ]);
            }
        }



        DB::commit();


        return  $this->flash('success', 'Money Added Successfully', [], route('ajo-contributions.show', $this->ajoContribution->hash_id));
    }

    public function updatedToggleAutoSave($value)
    {
        $this->ajoContribution->members()->where('user_id', auth()->id())->update([
            'if_auto_save' => $value
        ]);

        // return to_route('ajo-contributions.show', $this->ajoContribution->hash_id)->with('success', " Auto Save is now " . ($value ? 'enabled' : 'disabled'));

        return   $this->flash('success', " Auto Save is now " . ($value ? 'enabled' : 'disabled'), [], route('ajo-contributions.show', $this->ajoContribution->hash_id));
    }

    public function repayMoney($order_number)
    {
        $this->validate([
            'pin' => 'required|numeric'
        ]);

        if ($this->ajoContribution->amount > auth()->user()->wallet_balance) {
            $this->alert('error', 'Insufficient funds');
            return;
        }

        if (!Hash::check($this->pin, auth()->user()->pin)) {
            $this->alert('error', 'Incorrect pin');
            return;
        }

        $repayment_round = AjoContributionRound::where([
            'ajo_contribution_id' => $this->ajoContribution->id,
            'order' => $order_number
        ])->first();


        DB::beginTransaction();

        AjoContributionDonation::create([
            'user_id' => auth()->id(),
            'ajo_contribution_round_id' => $repayment_round->id,
            'amount' =>  $this->ajoContribution->amount,
            'status' => UserTransaction::STATUS_SUCCESSFUL
        ]);

        auth()->user()->update([
            'wallet_balance' => auth()->user()->wallet_balance - $this->ajoContribution->amount
        ]);

        auth()->user()->transactions()->create([
            'amount' => $this->ajoContribution->amount,
            'type' => UserTransaction::TYPE_DEBIT,
            'status' => UserTransaction::STATUS_SUCCESSFUL,
            'message' => 'Added ' . $this->ajoContribution->amount . ' to ' . $this->ajoContribution->name . ' ajo contribution',
            'source' => UserTransaction::SOURCE_AJO_CONTRIBUTION,
            'fee' => 0 // No fee for AJO contributions
        ]);

        $repayment_round->increment('balance', $this->ajoContribution->amount);

        // pay the next person
        $repayment_round->beneficiary->increment('ajo_contribution_wallet', $this->ajoContribution->amount);

        UserTransaction::create([
            'user_id' => $repayment_round->beneficiary->id,
            'amount' => $this->ajoContribution->amount,
            'type' => UserTransaction::TYPE_CREDIT,
            'status' => UserTransaction::STATUS_SUCCESSFUL,
            'message' => 'Received ' . $this->ajoContribution->amount . ' from ' . $this->ajoContribution->name . ' ajo contribution',
            'source' => UserTransaction::SOURCE_AJO_CONTRIBUTION,
            'fee' => 0 // No fee for AJO contributions
        ]);

        DB::commit();


        return $this->flash('success', 'Money repayed Successfully', [], route('ajo-contributions.show', $this->ajoContribution->hash_id));
    }

    public function endAjoContribution()
    {
        $this->validate([
            'pin' => 'required|numeric'
        ]);

        if (!Hash::check($this->pin, auth()->user()->pin)) {
            $this->alert('error', 'Incorrect pin');
            return;
        }

        if ($this->ajoContribution->getActiveRoundDefaulters()->count() > 0) {
            $this->alert('error', 'You can\'t end the Ajo Contribution with active round defaulters');
            return;
        }

        DB::beginTransaction();

        $this->ajoContribution->update([
            'status' => AjoContribution::STATUS_CLOSED
        ]);

        DB::commit();

        return $this->flash('success', 'Ajo Contribution Ended Successfully', [], route('ajo-contributions.show', $this->ajoContribution->hash_id));
    }

    public function updateAjoContribution()
    {
        $this->validate([
            'name' => 'required|string',
            'no_of_members' => 'required|numeric',
            'cover_image' => 'nullable|image'
        ]);


        $this->ajoContribution->update([
            'name' => $this->name,
            'no_of_members' => $this->no_of_members,
            'cover_image' => $this->cover_image ? $this->cover_image->store('ajo-contributions', 'public') : $this->ajoContribution->cover_image
        ]);


        return  $this->flash('success', 'Ajo Contribution Updated Successfully', [], route('ajo-contributions.show', $this->ajoContribution->hash_id));
    }

    public function reportMember($user_id, $round_order)
    {
        if ($this->isAdmin) {
            return $this->alert('error', 'You can not report a member as an admin');
        }
        $user = User::findOrFail($user_id);


        $admin = $this->ajoContribution->members()->where('role', AjoContributionMember::ROLE_ADMIN)->first();

        $message = $user->name . ' was reported  for not paying ' . $this->ajoContribution->name . ' Ajo Contribution for round ' . $round_order;

        $admin->notify(new NotifyUser($message, UserTransaction::SOURCE_AJO_CONTRIBUTION));

        return  $this->flash('success', 'Member Reported Successfully', [], route('ajo-contributions.show', $this->ajoContribution->hash_id));
    }
}
