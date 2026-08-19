<?php

namespace App\Console\Commands;

use App\Models\ElectricProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class UpdateElectricProviders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-electric-providers';

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
            $url  = env("FLW_API_URL") . "/bills/UTILITYBILLS/billers";
            $res = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . env('FLW_SECRET_KEY')
            ])->get($url, [
                'country' => 'NG',
            ]);

            $data = $res->json();

            if ($res->successful()) {

                $providers = $data['data'];

                foreach ($providers as $provider) {
                    ElectricProvider::updateOrCreate(
                        ['name' => $provider['name']],
                        [
                            'biller_code' => $provider['biller_code']
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
