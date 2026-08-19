<?php

namespace App\Livewire\Savings\InterestSavings;

use App\Helpers\SettingHelper;
use App\Models\Savings;
use App\Models\UserTransaction;
use App\Services\HashIdService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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

    public $breaking_locked_savings_fee;
    public $source = 'balance';

    public $lockedSavingsInterestRate = 0;

    public $unlockedSavingsInterestRate = 0;

    public $fee;

    public function mount($hashId)
    {
        $this->savings = (new HashIdService)->getRecord($hashId, Savings::class);

        $this->activities = $this->savings->activities()->latest()->get();

        $this->name = $this->savings->name;

        $this->breaking_locked_savings_fee = SettingHelper::get(SettingHelper::BREAKING_LOCKED_SAVINGS_FEE, 10);

        $this->lockedSavingsInterestRate = SettingHelper::get(SettingHelper::LOCKED_SAVINGS_INTEREST, 0);

        $this->unlockedSavingsInterestRate = SettingHelper::get(SettingHelper::UNLOCKED_SAVINGS_INTEREST, 0);

        $this->fee = SettingHelper::get(SettingHelper::CLOSE_UNLOCKED_SAVINGS_FEE);
    }


    public function render()
    {
        return view('livewire.savings.interest-savings.show')->title('Interest Savings Details');
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

        return $this->flash('success', 'Savings updated successfully', [], route('savings.interest-savings.show', $this->savings->hash_id));
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
            return $this->alert('error', 'Insufficient wallet balance');
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
            'message' => 'Added ₦ ' . number_format($this->amount) . ' to your savings',
            'user_id' => auth()->id()
        ]);

        // record user transaction
        auth()->user()->transactions()->create([
            'amount' => $this->amount,
            'type' => UserTransaction::TYPE_DEBIT,
            'message' => 'Added ₦ ' . formatNumber($this->amount) . ' my into ' . $this->savings->name . ' savings account',
            'status' => UserTransaction::STATUS_SUCCESSFUL,
            'source' => UserTransaction::SOURCE_SAVE_WITH_INTEREST
        ]);

        DB::commit();

        return   $this->flash('success', 'Added ' . number_format($this->amount) . ' to your savings', [], route('savings.interest-savings.show', $this->savings->hash_id));
    }

    public function withDraw()
    {
        $this->validate([
            'withdraw_amount' => 'required|numeric',
            'pin' => 'required|digits:4',
            'source' => 'required|in:balance,interest'
        ]);

        if (Hash::check($this->pin, auth()->user()->pin) === false) {
            return $this->alert('error', "Incorrect PIN");
        }

        $fee = getPercentageAmount($this->withdraw_amount, $this->breaking_locked_savings_fee);

        $amount = $this->savings->type === Savings::TYPE_LOCKED ? $this->withdraw_amount - $fee : $this->withdraw_amount;


        if ($this->source === 'interest' && $this->withdraw_amount > $this->savings->interest_accumulated) {
            return $this->alert('error', 'Insufficient interest balance');
        }

        if ($this->source === 'balance' && $this->withdraw_amount > $this->savings->balance) {
            return $this->alert('error', 'Insufficient savings balance');
        }

        DB::beginTransaction();

        if ($this->source === 'interest') {
            $this->savings->decrement('interest_accumulated', $this->withdraw_amount);
        } else {
            $this->savings->decrement('balance', $this->withdraw_amount);
        }

        $this->savings->activities()->create([
            'message' => "You withdrew ₦ {$this->withdraw_amount} from your savings " . ($this->source == 'interest' ? "interest" : "balance"),
            'user_id' => auth()->id()
        ]);

        auth()->user()->increment('wallet_balance', $this->amount);

        auth()->user()->transactions()->create([
            'amount' => $amount,
            'type' => UserTransaction::TYPE_CREDIT,
            'message' => 'Withdrew ₦ ' . $this->withdraw_amount . ' from your savings account',
            'status' => UserTransaction::STATUS_SUCCESSFUL,
            'source' => UserTransaction::SOURCE_SAVE_WITH_INTEREST
        ]);

        DB::commit();

        return $this->flash('success', 'Withdrawal successful', [], route('savings.interest-savings.show', $this->savings->hash_id));
    }

    public function closeAccount()
    {
        $this->validate([
            'pin' => 'required|digits:4'
        ]);

        if (Hash::check($this->pin, auth()->user()->pin) === false) {
            return $this->alert('error', "Incorrect PIN");
        }

        DB::beginTransaction();

        $amountEarned = $this->savings->balance + $this->savings->interest_accumulated;

        $total = $amountEarned - $this->fee;

        auth()->user()->increment('wallet_balance', $total);

        auth()->user()->transactions()->create([
            'amount' => $total,
            'type' => UserTransaction::TYPE_CREDIT,
            'message' => 'Closed my ' . $this->savings->name . ' savings account',
            'status' => UserTransaction::STATUS_SUCCESSFUL,
            'source' => UserTransaction::SOURCE_SAVE_WITH_INTEREST
        ]);

        $this->savings->update([
            'status' => Savings::STATUS_CLOSED,
            'balance' => 0,
            'interest_accumulated' => 0,
            'end_date' => now()
        ]);

        $this->savings->activities()->create([
            'message' => "You closed your savings account",
            'user_id' => auth()->id()
        ]);

        DB::commit();

        return $this->flash('success', 'Savings account closed successfully', [], route('savings.interest-savings'));
    }
}
