<?php

namespace App\Livewire\Stocks;

use App\Models\ApiKey;
use Illuminate\Support\Facades\Http;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Show extends Component
{
    use LivewireAlert;
    public $symbol;

    public $stock;

    public $isLoading = true;

    public function mount()
    {
        // $this->symbol = request('symbol');

        try {
            $res = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'X-RapidAPI-Key' => ApiKey::getApiKey(ApiKey::RAPID_API_KEY),
                'X-RapidAPI-Host' => 'real-time-finance-data.p.rapidapi.com'
            ])->get(env("STOCKS_API_URL") . "/stock-overview", [
                'symbol' => $this->symbol,
            ]);

            $data = $res->json();



            if ($res->successful()) {

                $this->stock = $data['data'];

                if (count($this->stock) == 0) {
                    throw new \Exception("Stock not found", 1);
                }
                $this->isLoading = false;
            } else {
                $this->isLoading = false;

                throw new \Exception($data['message'], 1);
            }
        } catch (\Throwable $th) {
            $this->isLoading = false;
            return $this->flash('error', $th->getMessage(), [], route('investments.index'));
        }
    }

    public function render()
    {
        return view('livewire.stocks.show')->title('Stocks Details');
    }
}
