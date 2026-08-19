<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ElectricityHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'user_id' => 21,
                'meter_number' => '1234567890',
                'phone' => '08122240618',
                'provider_code' => 'eko-electric',
                'provider_name' => 'Eko Electricity Distribution Company',
                'package_code' => 'prepaid',
                'customer_name' => 'John Doe',
                'customer_address' => '123, Main Street, Lagos',
            ],

            [
                'user_id' => 21,
                'meter_number' => '0987654321',
                'phone' => '09012345678',
                'provider_code' => 'ikeja-electric',
                'provider_name' => 'Ikeja Electricity Distribution Company',
                'package_code' => 'postpaid',
                'customer_name' => 'Jane Doe',
                'customer_address' => '456, Second Street, Lagos',
            ],

            [
                'user_id' => 21,
                'meter_number' => '1122334455',
                'phone' => '07012345678',
                'provider_code' => 'abuja-electric',
                'provider_name' => 'Abuja Electricity Distribution Company',
                'package_code' => 'prepaid',
                'customer_name' => 'Alice Smith',
                'customer_address' => '789, Third Street, Abuja',
            ],

            [
                'user_id' => 21,
                'meter_number' => '2233445566',
                'phone' => '08012345678',
                'provider_code' => 'portharcourt-electric',
                'provider_name' => 'Port Harcourt Electricity Distribution Company',
                'package_code' => 'postpaid',
                'customer_name' => 'Bob Johnson',
                'customer_address' => '101, Fourth Street, Port Harcourt',
            ],
        ];

        foreach ($data as $history) {
            \App\Models\ElectricityHistory::create($history);
        }
    }
}
