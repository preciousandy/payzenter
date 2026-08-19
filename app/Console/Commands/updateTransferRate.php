<?php

namespace App\Console\Commands;

use App\Models\TransferRate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class updateTransferRate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-transfer-rate';

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
        $currencies = ['USD', 'GBP', 'EUR'];


        foreach ($currencies as $currency) {
            try {
                $url  = env("FLW_API_URL") . "/transfers/rates";
                $res = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . env('FLW_SECRET_KEY')
                ])->get($url, [
                    'amount' => 100,
                    "destination_currency" => $currency,
                    "source_currency" => "NGN"
                ]);

                $data = $res->json();


                if ($res->successful()) {

                    $rate = $data['data']['rate'];

                    TransferRate::updateOrCreate(
                        ['currency' => $currency],
                        [
                            'rate' => $rate
                        ]
                    );

                    $this->info("Rate for " . $currency . " updated to " . $rate);
                } else {
                    throw new \Exception($data['message']);
                }
            } catch (\Throwable $th) {
                throw new \Exception($th->getMessage());
            }
        }
    }
}
