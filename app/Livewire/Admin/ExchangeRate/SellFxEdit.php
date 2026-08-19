<?php

namespace App\Livewire\Admin\ExchangeRate;

use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;

class SellFxEdit extends Component
{
    use LivewireAlert;
    use WithFileUploads;

    public $exchangeRate;
    public $currency;
    public $volume;
    public $price;
    public $status;
    public $logo;

    public $limit;
    public $account_name;
    public $account_number;
    public $bank_name;
    public $swift_code;
    public $description;


    public function mount($rate)
    {
        $this->exchangeRate = $rate;
        $this->currency = $rate->currency;
        $this->price = $rate->sell_price;
        $this->status = $rate->sell_status;
        $this->volume = $rate->volume;

        $this->limit = $rate->limit;
        $this->account_name = $rate->account_name;
        $this->account_number = $rate->account_number;
        $this->bank_name = $rate->bank_name;
        $this->swift_code = $rate->swift_code;
        $this->description = $rate->description;
    }

    public function update()
    {
        $this->validate([
            'currency' => 'required',
            'price' => 'required|numeric',
            'volume' => 'required|numeric',
            'status' => 'required',
            'logo' => 'nullable|image|max:4096'
        ]);


        $this->exchangeRate->update([
            'currency' => $this->currency,
            'sell_price' => $this->price,
            'volume' => $this->volume,
            'sell_status' => $this->status,
            'limit' => $this->limit,
        ]);

        if ($this->logo) {
            $this->exchangeRate->update([
                'logo' => $this->logo->store('exchange-rates', ['disk' => 'public'])
            ]);
        }


        return $this->flash('success', 'Exchange rate updated successfully', [], route('admin.exchange-rates'));
    }

    public function render()
    {
        return view('livewire.admin.exchange-rate.sell-fx-edit');
    }
}
