<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CableHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $histories  = [
            [
                'user_id' => 21,
                'card_number' => '7038947492',
                'phone' => '08122240618',
                'provider_code' => 'dstv',
                'package_code' => 'dstv6',
                'customer_name' => 'John Doe',
            ],
            [
                'user_id' => 21,
                'card_number' => '6738947492',
                'phone' => '08122240618',
                'provider_code' => 'gotv',
                'package_code' => 'gotv-jinja',
                'customer_name' => 'Jane Doe',
            ],
            [
                'user_id' => 21,
                'card_number' => '2318947492',
                'phone' => '09012323456',
                'provider_code' => 'dstv',
                'package_code' => 'dstv79',
                'customer_name' => 'Alice Smith',
            ],
        ];

        foreach ($histories as $history) {
            \App\Models\CableHistory::create($history);
        }
    }
}
