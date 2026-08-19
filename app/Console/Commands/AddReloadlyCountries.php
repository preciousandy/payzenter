<?php

namespace App\Console\Commands;

use App\Models\ReloadlyCountry;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class AddReloadlyCountries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-reloadly-countries';

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
        $res =   Http::withHeaders([
            'Accept' => 'application/com.reloadly.giftcards-v1+json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . env("RELOADLY_ACCESS_TOKEN")
        ])->get(env("RELOADLY_API_URL") . "/countries");

        $data = $res->json();

        foreach ($data as $country) {
            ReloadlyCountry::create([
                'iso_name' => $country['isoName'],
                'name' => $country['name'],
                'currency_code' => $country['currencyCode'],
                'currency_name' => $country['currencyName'],
                'flag_url' => $country['flagUrl']
            ]);
        }

        $this->info('Countries add succesfully');
    }
}
