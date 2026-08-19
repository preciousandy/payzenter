<?php

namespace App\Console\Commands;

use App\Models\ApiKey;
use App\Models\CryptoCurrency;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class updateCrypto extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-crypto';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $res = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'X-RapidAPI-Key' => ApiKey::getApiKey(ApiKey::RAPID_API_KEY),
                'X-RapidAPI-Host' => env('COIN_RANKING_API_HOST')
            ])->get(env("COIN_RANKING_API_URL") . "/coins", [
                'referenceCurrencyUuid' => 'yhjMzLPhuIDl',
                'timePeriod' => '24h',
                'tiers[0]' => 1,
                'limit' => 100,
            ]);

            $data = $res->json();

            if ($res->successful()) {

                $coins = $data['data']['coins'];


                foreach ($coins as $coin) {
                    CryptoCurrency::updateOrCreate(
                        ['uuid' => $coin['uuid']],
                        [
                            'name' => $coin['name'],
                            'symbol' => $coin['symbol'],
                            'icon' => $coin['iconUrl'],
                            'price_in_usd' => $coin['price'],
                            'changes' => array_slice($coin['sparkline'], 0, -1),
                            'status' => CryptoCurrency::STATUS_OPEN,
                            'change_percentage' => $coin['change'],
                        ]
                    );
                }
            } else {
                throw new \Exception($data['message']);
            }
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage());
        }
    }
}
