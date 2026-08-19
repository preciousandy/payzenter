<?php

namespace App\Console\Commands;

use App\Models\DataPlan;
use App\Models\Network;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class UpdateDataPlans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-data-plans';

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
        $networks = Network::all();

        $allDataPlans = DataPlan::all();

        $billsService = new \App\Services\BillsService();

        $response = $billsService->fetchEbillsDataPlans();


        if ($response['status'] == 'success') {
            $allDataPlans->each(function ($plan) {
                $plan->delete();
            });

            $newPlans = $response['data'];

            foreach ($newPlans as $plan) {
                DataPlan::create(
                    [
                        'network_code' => $plan['service_id'],
                        'title' => $plan['data_plan'],
                        'amount' => $plan['price'],
                        'price' => $plan['price'],
                        'item_code' => $plan['variation_id'],
                    ]
                );
            }
        }
    }
}
