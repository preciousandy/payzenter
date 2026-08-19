<?php

namespace Database\Seeders;

use App\Models\DataPlan;
use App\Models\Network;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DataPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $newtorks = Network::all();

        $newtorks->each(function ($network) {
            $network->dataPlans()->delete();
            $network->delete();
        });


        $mtn =  [
            [
                "plan_id" => "500",
                "title" => "MTN SME Data 500MB – 30 Days",
                "amount" => 189,
                "price" => 200
            ],
            [
                "plan_id" => "M1024",
                "title" => "MTN SME Data 1GB – 30 Days",
                "amount" => 299,
                "price" => 350
            ],
            [
                "plan_id" => "M2024",
                "title" => "MTN SME Data 2GB – 30 Days",
                "amount" => 599,
                "price" => 650
            ],
            [
                "plan_id" => "3000",
                "title" => "MTN SME Data 3GB – 30 Days",
                "amount" => 899,
                "price" => 1000
            ],
            [
                "plan_id" => "5000",
                "title" => "MTN SME Data 5GB – 30 Days",
                "amount" => 1499,
                "price" => 1600
            ],
            [
                "plan_id" => "10000",
                "title" => "MTN SME Data 10GB – 30 Days",
                "amount" => 2999,
                "price" => 3300
            ],

            [
                "plan_id" => "mtn-30gb-8000",
                "title" => "MTN Data 30GB – 30 Days",
                "amount" => 7899,
                "price" => 8000
            ],
            [
                "plan_id" => "mtn-40gb-10000",
                "title" => "MTN Data 40GB – 30 Days",
                "amount" => 9859,
                "price" => 10000
            ],
            [
                "plan_id" => "mtn-75gb-15000",
                "title" => "MTN Data 75GB – 30 Days",
                "amount" => 14899,
                "price" => 16000
            ],
        ];

        $airtel =  [
            [
                "plan_id" => "AIRTEL500MB",
                "title" => "Airtel Data 500MB (Gift) – 30 Days",
                "amount" => 149,
                "price" => 200
            ],
            [
                "plan_id" => "AIRTEL1GB",
                "title" => "Airtel Data 1GB (Gift) – 30 Days",
                "amount" => 249,
                "price" => 350
            ],
            [
                "plan_id" => "AIRTEL2GB",
                "title" => "Airtel Data 2GB (Gift) – 30 Days",
                "amount" => 499,
                "price" => 600
            ],
            [
                "plan_id" => "AIRTEL5GB",
                "title" => "Airtel Data 5GB (Gift) – 30 Days",
                "amount" => 1249,
                "price" => 1500
            ],
            [
                "plan_id" => "AIRTEL10GB",
                "title" => "Airtel Data 10GB (Gift) – 30 Days",
                "amount" => 2499,
                "price" => 3000
            ],
            [
                "plan_id" => "AIRTEL15GB",
                "title" => "Airtel Data 15GB (Gift) – 30 Days",
                "amount" => 3699,
                "price" => 4000
            ],
            [
                "plan_id" => "AIRTEL20GB",
                "title" => "Airtel Data 20GB (Gift) – 30 Days",
                "amount" => 4899,
                "price" => 6000
            ],

            [
                "plan_id" => "airt-11000",
                "title" => "Airtel Data 40GB – 30 Days",
                "amount" => 10699,
                "price" => 12000
            ],
        ];

        $glo = [
            // [
            //     "plan_id" => "glo100x",
            //     "title" => "Glo Data 1GB – 5 Nights"
            // ],
            // [
            //     "plan_id" => "glo200x",
            //     "title" => "Glo Data 1.25GB – 1 Day (Sunday)"
            // ],
            [
                "plan_id" => "G500",
                "title" => "Glo Data 1.35GB – 14 Days",
                "amount" => 485,
                "price" => 600
            ],
            [
                "plan_id" => "G2000",
                "title" => "Glo Data 5.8GB – 30 Days",
                "amount" => 1939,
                "price" => 2500
            ],
            [
                "plan_id" => "G1000",
                "title" => "Glo Data 2.9GB – 30 Days",
                "amount" => 969,
                "price" => 1100
            ],
            [
                "plan_id" => "G2500",
                "title" => "Glo Data 7.7GB – 30 Days",
                "amount" => 2439,
                "price" => 2600
            ],
            [
                "plan_id" => "G3000",
                "title" => "Glo Data 10GB – 30 Days",
                "amount" => 2939,
                "price" => 3300
            ],
            [
                "plan_id" => "G4000",
                "title" => "Glo Data 13.25GB – 30 Days",
                "amount" => 3879,
                "price" => 4200
            ],
            [
                "plan_id" => "G5000",
                "title" => "Glo Data 18.25GB – 30 Days",
                "amount" => 4839,
                "price" => 5500
            ],
            [
                "plan_id" => "G8000",
                "title" => "Glo Data 29.5GB – 30 Days",
                "amount" => 7779,
                "price" => 8500
            ],
            [
                "plan_id" => "glo10000",
                "title" => "Glo Data 50GB – 30 Days",
                "amount" => 9859,
                "price" => 11000
            ],
        ];

        $etisalat = [
            [
                "plan_id" => "9MOB1000",
                "title" => "9mobile Data 1GB – 30 Days",
                "amount" => 979,
                "price" => 1000
            ],
            [
                "plan_id" => "9MOB34500",
                "title" => "9mobile Data 2.5GB – 30 Days",
                "amount" => 1979,
                "price" => 2000
            ],
            [
                "plan_id" => "9MOB8000",
                "title" => "9mobile Data 11.5GB – 30 Days",
                "amount" => 7899,
                "price" => 8200
            ],
            [
                "plan_id" => "9MOB5000",
                "title" => "9mobile Data 15GB – 30 Days",
                "amount" => 9859,
                "price" => 10500
            ],
        ];

        $mtnNetwork = Network::create([
            'name' => 'MTN NG',
            'vtu_code' => 'mtn',
            'img_url' => 'mtn.png',
        ]);

        foreach ($mtn as $plan) {
            DataPlan::create([
                'network_id' => $mtnNetwork->id,
                'item_code' => $plan['plan_id'],
                'title' => $plan['title'],
                'amount' => $plan['amount'],
                'price' => $plan['price'],
            ]);
        }

        $airtelNetwork = Network::create([
            'name' => 'Airtel NG',
            'vtu_code' => 'airtel',
            'img_url' => 'airtel.png',
        ]);

        foreach ($airtel as $plan) {
            DataPlan::create([
                'network_id' => $airtelNetwork->id,
                'item_code' => $plan['plan_id'],
                'title' => $plan['title'],
                'amount' => $plan['amount'],
                'price' => $plan['price'],
            ]);
        }

        $gloNetwork = Network::create([
            'name' => 'Glo NG',
            'vtu_code' => 'glo',
            'img_url' => 'glo.png',
        ]);

        foreach ($glo as $plan) {
            DataPlan::create([
                'network_id' => $gloNetwork->id,
                'item_code' => $plan['plan_id'],
                'title' => $plan['title'],
                'amount' => $plan['amount'],
                'price' => $plan['price'],
            ]);
        }

        $etisalatNetwork = Network::create([
            'name' => '9mobile NG',
            'vtu_code' => 'etisalat',
            'img_url' => 'etisalat.png',
        ]);

        foreach ($etisalat as $plan) {
            DataPlan::create([
                'network_id' => $etisalatNetwork->id,
                'item_code' => $plan['plan_id'],
                'title' => $plan['title'],
                'amount' => $plan['amount'],
                'price' => $plan['price'],
            ]);
        }
    }
}
