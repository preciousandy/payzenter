<?php

namespace App\Livewire\Savings\GroupSavings;

use App\Helpers\SettingHelper;
use App\Models\GroupSavings;
use App\Models\GroupSavingsMembers;
use App\Models\User;
use App\Models\UserTransaction;
use App\Services\HashIdService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Show extends Component
{
    use LivewireAlert;

    public $savings;
    public $name;
    public $amount;
    public $withdraw_amount;
    public $pin;
    public $activities;
    public $members;
    public $reason;
    public $withdrawal_reason;

    public $is_admin = false;

    public $withdrawal_fee = 0;

    public function mount($hashId)
    {
        $this->savings = (new HashIdService)->getRecord($hashId, GroupSavings::class);

        if ($this->savings->members->find(Auth::user()->id)->pivot->status !== GroupSavingsMembers::STATUS_ACTIVE) {
            abort(403, 'You are not a member of this group savings');
        }

        $this->activities = $this->savings->activities()->latest()->get();

        $this->name = $this->savings->name;

        $this->members = $this->savings->members;

        $this->withdrawal_fee = SettingHelper::get(SettingHelper::GROUP_SAVINGS_WITHDRAWAL_FEE, 0);

        $this->is_admin = $this->savings->members->find(auth()->id())->pivot->role === GroupSavingsMembers::ROLE_ADMIN;
    }

    public function render()
    {
        return view('livewire.savings.group-savings.show')->title('Group Savings Details');
    }

    public function update()
    {
        $this->validate([
            'name' => 'required'
        ]);

        if ($this->name === $this->savings->name) {
            return $this->addError('name', 'No changes made');
        }

        $this->savings->update([
            'name' => $this->name
        ]);

        // record activity
        $this->savings->activities()->create([
            'message' => 'Savings details was updated',
            'user_id' => auth()->id()
        ]);

        return $this->flash('success', 'Savings updated successfully', [], route('savings.group-savings.show', $this->savings->hash_id));
    }

    public function addMoney()
    {
        $this->validate([
            'amount' => 'required|numeric',
            'pin' => 'required|digits:4'
        ]);


        if (Hash::check($this->pin, auth()->user()->pin) === false) {
            return $this->alert('error', "Incorrect PIN");
        }


        if ($this->amount > auth()->user()->wallet_balance) {
            return $this->addError('amount', 'Insufficient wallet balance');
        }

        if ($this->savings->type === GroupSavings::TYPE_STRICT) {
            $amountDonated = $this->savings->donations()->where('user_id', auth()->id())->sum('amount');

            $errorMessage = 'You are exceeding the member  target amount, pls add amount less than ' . number_format($this->savings->member_target_amount - $amountDonated);

            if ($amountDonated + $this->amount > $this->savings->member_target_amount) {
                $this->addError('amount', $errorMessage);

                return;
            }
        }

        DB::beginTransaction();

        $this->savings->update([
            'balance' => $this->savings->balance + $this->amount
        ]);

        auth()->user()->update([
            'wallet_balance' => auth()->user()->wallet_balance - $this->amount
        ]);


        // record activity
        $this->savings->activities()->create([
            'message' => auth()->user()->name .  ' added ₦' . number_format($this->amount) . ' to the group savings',
            'user_id' => auth()->id()
        ]);

        // record user transaction
        auth()->user()->transactions()->create([
            'amount' => $this->amount,
            'type' => UserTransaction::TYPE_DEBIT,
            'message' => 'Added money my into ' . $this->savings->name . ' group savings account',
            'status' => UserTransaction::STATUS_SUCCESSFUL,
            'source' => UserTransaction::SOURCE_GROUP_SAVINGS
        ]);

        $this->savings->donations()->create([
            'user_id' => auth()->id(),
            'amount' => $this->amount
        ]);

        DB::commit();

        return $this->flash('success', 'Added ' . number_format($this->amount) . ' to this group savings', [], route('savings.group-savings.show', $this->savings->hash_id));
    }

    public function withDraw()
    {
        $this->validate([
            'withdraw_amount' => 'required|numeric',
            'pin' => 'required|digits:4',
            'withdrawal_reason' => 'required'
        ]);


        if (Hash::check($this->pin, auth()->user()->pin) === false) {
            return $this->alert('error', "Incorrect PIN");
        }


        if ($this->withdraw_amount > $this->savings->balance) {
            return $this->addError('withdraw_amount', 'Insufficient savings balance');
        }



        $amount = $this->withdraw_amount - $this->withdrawal_fee;



        DB::beginTransaction();

        $this->savings->decrement('balance', $this->withdraw_amount);

        $this->savings->activities()->create([
            'message' => auth()->user()->name . " withdrew ₦{$this->withdraw_amount} from the group savings account due to: {$this->withdrawal_reason}",
            'user_id' => auth()->id()
        ]);

        auth()->user()->increment('wallet_balance', $amount);

        auth()->user()->transactions()->create([
            'amount' => $amount,
            'type' => UserTransaction::TYPE_CREDIT,
            'message' => 'Withdrew ' . $this->withdraw_amount . ' from ' . $this->savings->name .  ' group  savings account',
            'status' => UserTransaction::STATUS_SUCCESSFUL,
            'source' => UserTransaction::SOURCE_GROUP_SAVINGS
        ]);

        DB::commit();

        return $this->flash('success', 'Withdrawal successful', [], route('savings.group-savings.show', $this->savings->hash_id));
    }

    public function makeUserAdmin($userId)
    {
        $this->savings->members()->updateExistingPivot($userId, ['role' => 'admin']);

        return redirect()->route('savings.group-savings.show', $this->savings->hash_id)->with('success', 'User is now an admin');
    }

    public function removeMember(User $user)
    {
        $this->validate([
            'reason' => 'required'
        ]);

        $this->savings->members()->updateExistingPivot($user->id, ['status' => GroupSavingsMembers::STATUS_REMOVED]);

        $this->savings->activities()->create([
            'message' => $user->name . ' was removed from the group savings by ' . auth()->user()->name  . ' due to: ' . $this->reason,
            'user_id' => auth()->id()
        ]);

        Notification::send($user, new \App\Notifications\GroupSavingsMemberRemoved($this->savings));

        return $this->flash('success', 'User removed successfully', [], route('savings.group-savings.show', $this->savings->hash_id));
    }

    public function endSavings()
    {
        $this->validate([
            'pin' => 'required|digits:4'
        ]);

        if (!Hash::check($this->pin, auth()->user()->pin)) {
            $this->alert('error', 'Incorrect pin');
            return;
        }

        if ($this->savings->balance > 0) {
            $this->alert('error', 'You cannot close this group savings because it has a balance');
            return;
        }

        DB::beginTransaction();

        $this->savings->update([
            'status' => GroupSavings::STATUS_CLOSED
        ]);

        $this->savings->activities()->create([
            'message' => auth()->user()->name . ' closed this group',
            'user_id' => auth()->id()
        ]);

        DB::commit();

        return $this->flash('success', 'Group savings closed successfully', [], route('savings.group-savings'));
    }
}
