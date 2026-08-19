<?php

namespace App\Livewire\Assets;

use App\Models\CryptoCurrency;
use App\Models\ExchangeRate;
use App\Models\Investment;
use App\Services\HashIdService;
use DateTime;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class Show extends Component
{
    public $assetUID;
    public $type;

    public $crypto = [];

    public $naira_to_dollar = 0;

    public $historicalDates = [];



    public function mount()
    {
        $this->naira_to_dollar = ExchangeRate::firstWhere('code', 'USD')->rate;

        $this->crypto = CryptoCurrency::where('uuid', $this->assetUID)->first();

        $this->historicalDates = $this->getLastNDates(count($this->crypto->changes));



        // try {
        //     $res = Http::withHeaders([
        //         'Accept' => 'application/json',
        //         'Content-Type' => 'application/json',
        //         'X-RapidAPI-Key' => env('COIN_RANKING_API_KEY'),
        //         'X-RapidAPI-Host' => env('COIN_RANKING_API_HOST')
        //     ])->get(env("COIN_RANKING_API_URL") . "/coin/" . $this->assetUID);

        //     $data = $res->json();

        //     if ($res->successful()) {
        //         $this->crypto = $data['data']['coin'];


        //         $this->isLoading = false;
        //     } else {
        //         $this->isLoading = false;
        //     }
        // } catch (\Throwable $th) {
        //     $this->isLoading = false;
        //     throw $th;
        // }
    }

    private function getLastNDates($days)
    {
        $dates = [];
        $today = new DateTime();

        for ($i = 1; $i <= $days; $i++) {
            $date = clone $today;
            $date->modify("-$i day");
            $dates[] = $date->format('Y-m-d'); // or any other format you prefer
        }

        return array_reverse($dates);
    }

    public function render()
    {
        return view('livewire.assets.show')->title('Holdings Asset Details');
    }
}
