<?php

namespace App\Livewire\Internationaltrandfer;

use App\Models\UserTransaction;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Send extends Component
{
    public $amount, $bank, $account_number,  $pin;

    public function render()
    {
        return view('livewire.internationaltrandfer.send');
    }

    public function sendMoney()
    {
        $data =   $this->validate([
            'amount' => 'required|numeric',
            'bank' => 'required',
            'account_number' => 'required',
            'pin' => 'required|numeric|digits:4',
        ]);

        if ($this->amount > auth()->user()->wallet_balance) {
            $this->addError('amount', 'Insufficient funds');
            return;
        }

        if (!Hash::check($this->pin, auth()->user()->pin)) {
            $this->alert('error', 'Incorrect pin');
            return;
        }

        auth()->user()->decrement('wallet_balance', $this->amount);

        auth()->user()->transactions()->create([
            'amount' => $this->amount,
            'type' => UserTransaction::FOREIGN_TO_LOCAL_TRANSFER,
            'status' => UserTransaction::STATUS_SUCCESSFUL,
            'message' => 'Transfered ' . $this->amount . ' to ' . $this->account_number,
            'fee' => 0 // No fee calculation in this component
        ]);

        return redirect()->route('home')->with('success', 'Transfer was successful');
    }
}
