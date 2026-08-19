<?php

namespace App\Livewire\Admin\ExchangeRate;

use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
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

    public function mount($rate)
    {
        $this->exchangeRate = $rate;
        $this->currency = $rate->currency;
        $this->price = $rate->price;
        $this->status = $rate->status;
        $this->volume = $rate->volume;
        $this->limit = $rate->limit;
    }

    public function render()
    {
        return view('livewire.admin.exchange-rate.edit');
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
            'price' => $this->price,
            'volume' => $this->volume,
            'status' => $this->status,
            'limit' => $this->limit
        ]);

        if ($this->logo) {
            $this->exchangeRate->update([
                'logo' => $this->logo->store('exchange-rates', ['disk' => 'public'])
            ]);
        }


        return $this->flash('success', 'Exchange rate updated successfully', [], route('admin.exchange-rates'));
    }
}
