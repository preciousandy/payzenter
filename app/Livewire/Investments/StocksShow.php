<?php

namespace App\Livewire\Investments;

use App\Models\ApiKey;
use App\Models\Investment;
use App\Models\StockPrice;
use App\Services\HashIdService;
use Illuminate\Support\Facades\Http;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class StocksShow extends Component
{
    use LivewireAlert;

    public $symbol;

    public $stock;

    public $isLoading = true;

    public $investment;

    public $possible_earnings = 0;

    public $current_rate = 0;

    public $earnings = 0;

    public function mount($hash_id)
    {
        // $this->symbol = request('symbol');

        $this->investment = (new HashIdService)->getRecord($hash_id, Investment::class);

        $this->current_rate = StockPrice::where('symbol', $this->investment->asset_symbol)->first()->change_percent;

        $this->possible_earnings = calculatePercentageChange($this->current_rate, $this->investment->amount_invested);

        $this->earnings = $this->investment->status == 'active' ? $this->possible_earnings : $this->investment->amount_earned;

        try {
            $res = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'X-RapidAPI-Key' => ApiKey::getApiKey(ApiKey::RAPID_API_KEY),
                'X-RapidAPI-Host' => 'real-time-finance-data.p.rapidapi.com'
            ])->get(env("STOCKS_API_URL") . "/stock-overview", [
                'symbol' => $this->investment->asset_symbol,
            ]);

            $data = $res->json();



            if ($res->successful()) {

                $this->stock = $data['data'];

                $this->isLoading = false;
            } else {
                $this->isLoading = false;
                throw new \Exception($data['message'], 1);
            }
        } catch (\Throwable $th) {

            $this->isLoading = false;
            return $this->flash('error', $th->getMessage(), [], route('investments.portfolio'));
        }
    }

    public function render()
    {
        return view('livewire.investments.stocks-show')->title('Stocks Investment Details');
    }
}
