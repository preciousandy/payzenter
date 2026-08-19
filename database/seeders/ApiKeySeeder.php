<?php

namespace Database\Seeders;

use App\Models\ApiKey;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ApiKeySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $apiKeys = [

            // rapid api
            [
                'name' => ApiKey::RAPID_API_KEY,
                'value' => 'rapidapi_test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
                'description' => 'Rapid API key for accessing various APIs'
            ],

        ];

        foreach ($apiKeys as $apiKey) {
            ApiKey::updateOrCreate(
                ['name' => $apiKey['name']],
                [
                    'value' => $apiKey['value'],
                    'description' => $apiKey['description']
                ]
            );
        }
    }
}
