<?php

namespace App\Livewire\AjoContributions;

use App\Helpers\SettingHelper;
use App\Models\AjoContribution;
use App\Models\UserTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Index extends Component
{
    use LivewireAlert;

    public $contributions = [];

    public $amount, $pin;


    public function mount()
    {
        $this->contributions = auth()->user()->ajoContributions()->active()->latest()->get();
    }

    public function withDraw()
    {
        $this->validate([
            'amount' => 'required|numeric|min:100',
            'pin' => 'required|numeric|min:1'
        ]);

        if (!Hash::check($this->pin, auth()->user()->pin)) {
            $this->alert('error', 'Incorrect pin');
            return;
        }

        if ($this->amount > auth()->user()->ajo_contribution_wallet) {
            return $this->alert('error', 'Insufficient funds in your Ajo Contribution Wallet');
        }

        $balance = auth()->user()->ajo_contribution_wallet;

        $fee = getTotalFee($balance, SettingHelper::get(SettingHelper::AJO_CONTRIBUTION_FEE), SettingHelper::get(SettingHelper::AJO_CONTRIBUTION_PERCENTAGE));


        if ($this->amount < $fee) {
            return $this->alert('error', 'Withdrawal amount cannot be less than the fee');
        }

        DB::beginTransaction();

        $amount = $this->amount - $fee;



        auth()->user()->increment('wallet_balance', $amount);

        auth()->user()->decrement('ajo_contribution_wallet', $this->amount);

        UserTransaction::create([
            'user_id' => auth()->id(),
            'amount' => $amount,
            'type' => UserTransaction::TYPE_CREDIT,
            'message' => $amount . ' was added to your wallet  from your Ajo Contribution Wallet',
            'source' => UserTransaction::SOURCE_AJO_CONTRIBUTION,
            'status' => UserTransaction::STATUS_SUCCESSFUL,
            'fee' => $fee
        ]);

        DB::commit();


        $this->flash('success', 'Withdrawal successful', [], route('ajo-contributions.index'));
    }

    public function render()
    {
        return view('livewire.ajo-contributions.index')->title('Ajo Contributions');
    }
}
