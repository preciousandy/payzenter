<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AirtimeDataHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'user_id' => 21,
                'phone_number' => '08067345622',
                'network_code' => 'mtn',
                'network_name' => 'MTN NG',
                'type' => 'airtime',
            ],
            [
                'user_id' => 21,
                'phone_number' => '08067345622',
                'network_code' => 'glo',
                'network_name' => 'GLO NG',
                'type' => 'data',
            ],
            [
                'user_id' => 21,
                'phone_number' => '08067345622',
                'network_code' => 'airtel',
                'network_name' => 'AIRTEL NG',
                'type' => 'airtime',
            ],
            [
                'user_id' => 21,
                'phone_number' => '08067345622',
                'network_code' => '9mobile',
                'network_name' => '9MOBILE NG',
                'type' => 'data',
            ],
        ];


        foreach ($data as $item) {
            \App\Models\AirtimeDataHistory::updateOrCreate(
                [
                    'user_id' => $item['user_id'],
                    'phone_number' => $item['phone_number'],
                    'network_code' => $item['network_code'],
                ],
                [
                    'network_name' => $item['network_name'],
                    'type' => $item['type'],
                ]
            );
        }
    }
}
