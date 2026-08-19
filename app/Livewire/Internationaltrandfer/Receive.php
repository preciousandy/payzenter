<?php

namespace App\Livewire\Internationaltrandfer;

use App\Models\ExchangeRate;
use App\Models\InternationalTransfer;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Receive extends Component
{
    public $name, $account_number, $email, $currency, $bank, $amount;

    public $exchangeRates = [];

    public function mount()
    {
        $this->exchangeRates = ExchangeRate::orderBy('currency')->get();
    }

    public function render()
    {
        return view('livewire.internationaltrandfer.receive');
    }

    public function sendMoney()
    {
        $this->validate([
            'name' => 'required',
            'account_number' => 'required',
            'email' => 'required|email',
            'currency' => 'required',
            'bank' => 'required',
            'amount' => 'required|numeric',
        ]);

        $exchangeRate = ExchangeRate::where('currency', $this->currency)->first();

        DB::beginTransaction();

        InternationalTransfer::create([
            'user_id' => auth()->id(),
            'recipient_name' => $this->name,
            'recipient_account_number' => $this->account_number,
            'recipient_email' => $this->email,
            'recipient_bank' => $this->bank,
            'recipient_amount' => $this->amount,
            'amount_deducted_in_local_currency' => $this->amount * $exchangeRate->rate,
            'recipient_currency' => $this->currency,
            'status' => InternationalTransfer::STATUS_PENDING,
        ]);

        auth()->user()->decrement('wallet_balance', $this->amount * $exchangeRate->rate);

        DB::commit();

        return redirect()->route('international-transfer.index')->with('success', 'Transfer request sent successfully');
    }
}
