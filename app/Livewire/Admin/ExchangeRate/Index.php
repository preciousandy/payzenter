<?php

namespace App\Livewire\Admin\ExchangeRate;

use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Http;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Index extends Component
{
    use LivewireAlert;
    public $currency, $rate;


    public function render()
    {
        return view('livewire.admin.exchange-rate.index', [])->extends('layouts.admin');
    }

    public function refreshRates()
    {
        try {
            $res = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->get(env("METALS_API_URL") . "/latest", [
                'api_key' => env('METALS_API_KEY'),
                'currency' => 'NGN',
            ]);

            $data = $res->json();


            $rates = $data['currencies'];


            $currencies = ExchangeRate::all();

            foreach ($currencies as $currency) {
                $currency->rate = $rates[$currency->code];
                $currency->price = $currency->rate + getPercentageAmount($currency->rate, 2);
                $currency->save();

                $this->flash('success', 'Exchange rates updated successfully', [], route('admin.exchange-rates'));
            }
        } catch (\Throwable $th) {
            $this->alert('error', $th->getMessage());
        }
    }

    public function create()
    {
        $this->validate([
            'currency' => 'required',
            'rate' => 'required|numeric'
        ]);

        ExchangeRate::create([
            'currency' => $this->currency,
            'rate' => $this->rate
        ]);

        return redirect()->route('admin.exchange-rates')->with('success', 'Exchange rate created successfully');
    }
}
