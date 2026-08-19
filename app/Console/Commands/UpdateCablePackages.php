<?php

namespace App\Console\Commands;

use App\Models\Cable;
use App\Models\CablePackage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class UpdateCablePackages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-cable-packages';

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
        $cables = Cable::all();

        $allPackages = CablePackage::all();

        $billsService = new \App\Services\BillsService();

        $response = $billsService->fetchEbillsTvPlans();


        if ($response['status'] == 'success') {
            $allPackages->each(function ($plan) {
                $plan->delete();
            });

            $newPlans = $response['data'];

            foreach ($newPlans as $plan) {
                CablePackage::create(
                    [
                        'provider_code' => $plan['service_id'],
                        'name' => $plan['package_bouquet'],
                        'amount' => $plan['price'],
                        'price' => $plan['price'],
                        'package_code' => $plan['variation_id'],
                        'availability' => $plan['availability'],
                    ]
                );
            }
        }
    }
}
