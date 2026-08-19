<?php

namespace Database\Seeders;

use App\Models\ElectricProvider;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ElectricProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $providers = [
            [
                "name" => "Abuja Electricity Distribution Company (AEDC)",
                "code" => "abuja-electric"
            ],
            [
                "name" => "Eko Electricity Distribution Company (EKEDC)",
                "code" => "eko-electric"
            ],
            [
                "name" => "Ibadan Electricity Distribution Company (IBEDC)",
                "code" => "ibadan-electric"
            ],
            [
                "name" => "Ikeja Electricity Distribution Company (IKEDC)",
                "code" => "ikeja-electric"
            ],
            [
                "name" => "Jos Electricity Distribution PLC (JEDplc)",
                "code" => "jos-electric"
            ],
            [
                "name" => "Kaduna Electricity Distribution Company (KAEDCO)",
                "code" => "kaduna-electric"
            ],
            [
                "name" => "Kano Electricity Distribution Company (KEDCO)",
                "code" => "kano-electric"
            ],
            [
                "name" => "Port Harcourt Electricity Distribution Company (PHED)",
                "code" => "portharcourt-electric"
            ],
            [
                "name" => "Enugu Electricity Distribution Company (EEDC)",
                "code" => "enugu-electric"
            ],
            [
                "name" => "Benin Electricity Distribution Company (BEDC)",
                "code" => "benin-electric"
            ],
            [
                "name" => "Aba Power Limited (APL)",
                "code" => "aba-electric"
            ],
            [
                "name" => "Yola Electricity Distribution Company (YEDC)",
                "code" => "yola-electric"
            ]
        ];


        foreach ($providers as $provider) {
            ElectricProvider::updateOrCreate(
                [
                    'code' => $provider['code'],
                ],
                [
                    'name' => $provider['name'],
                ]
            );
        }
    }
}
