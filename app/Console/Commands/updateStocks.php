<?php

namespace App\Console\Commands;

use App\Models\ApiKey;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class updateStocks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-stocks';

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
                'X-RapidAPI-Host' => 'real-time-finance-data.p.rapidapi.com'
            ])->get(env("STOCKS_API_URL") . "/market-trends", [
                'trend_type' => 'MOST_ACTIVE',
            ]);

            $data = $res->json();

            if ($res->successful()) {

                $stocks = $data['data']['trends'];

                foreach ($stocks as $stock) {
                    \App\Models\StockPrice::updateOrCreate(
                        ['symbol' => $stock['symbol']],
                        [
                            'company' => $stock['name'],
                            'change' => $stock['change'],
                            'change_percent' => $stock['change_percent'],
                            'price' => $stock['price'],
                        ]
                    );
                }
            } else {

                throw new \Exception($data['message'] ?? 'An error occurred');
            }
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage());
        }
    }
}
