<?php

namespace App\Livewire;

use App\Models\DomiciliaryBank;
use App\Models\ExchangeRate;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class DomiciliaryBanksTab extends Component
{
    use LivewireAlert;

    public $account_name, $account_number, $bank_name, $description, $swift_code;

    public $exchangeRates = [];


    public $selectedExchangeRates = [];

    public function mount()
    {
        $this->exchangeRates = ExchangeRate::orderBy('currency')->get();
    }

    public function addCurrency($currencyId)
    {
        $this->selectedExchangeRates[] = $currencyId;
    }

    public function addBank()
    {
        $this->validate([
            'account_name' => 'required',
            'account_number' => 'required',
            'bank_name' => 'required',
            'description' => 'required',
            'swift_code' => 'required',
            'selectedExchangeRates' => 'required|array|min:1'
        ]);

        DB::beginTransaction();

        $bank = DomiciliaryBank::create([
            'account_name' => $this->account_name,
            'account_number' => $this->account_number,
            'bank_name' => $this->bank_name,
            'description' => $this->description,
            'swift_code' => $this->swift_code
        ]);

        $bank->currencies()->attach($this->selectedExchangeRates);

        DB::commit();

        $this->flash('success', 'Bank added successfully!', [], route('admin.exchange-rates'));
    }

    public function render()
    {
        return view('livewire.domiciliary-banks-tab');
    }
}
