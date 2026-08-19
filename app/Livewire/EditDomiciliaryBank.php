<?php

namespace App\Livewire;

use App\Models\DomiciliaryBank;
use App\Models\ExchangeRate;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class EditDomiciliaryBank extends Component
{
    use LivewireAlert;

    public $account_name, $account_number, $bank_name, $description, $swift_code;

    public $exchangeRates = [];

    public $selectedExchangeRates = [];

    public DomiciliaryBank $bank;

    public function mount()
    {
        $this->account_name = $this->bank->account_name;
        $this->account_number = $this->bank->account_number;
        $this->bank_name = $this->bank->bank_name;
        $this->description = $this->bank->description;
        $this->swift_code = $this->bank->swift_code;

        $this->exchangeRates = ExchangeRate::orderBy('currency')->get();

        $this->selectedExchangeRates = $this->bank->currencies->pluck('id')->toArray();
    }


    public function addCurrency($currencyId)
    {
        $this->selectedExchangeRates[] = $currencyId;
    }

    public function updateBank()
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
        $this->bank->update([
            'account_name' => $this->account_name,
            'account_number' => $this->account_number,
            'bank_name' => $this->bank_name,
            'description' => $this->description,
            'swift_code' => $this->swift_code
        ]);

        $this->bank->currencies()->sync($this->selectedExchangeRates);

        DB::commit();

        $this->flash('success', 'Bank updated successfully!', [], route('admin.exchange-rates'));
    }

    public function render()
    {
        return view('livewire.edit-domiciliary-bank');
    }
}
