<?php

namespace Database\Seeders;

use App\Models\Cable;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cable_networks = Cable::all();

        $cable_networks->each(function ($cable) {
            $cable->packages()->delete();
            $cable->delete();
        });

        $Cables =  [
            [
                "name" => "DSTV",
                "code" => "dstv",
                "img_url" => "dstv.jpg",
                "packages" => [
                    [
                        "name" => "DStv Padi",
                        "package_code" => "dstv-padi",
                        "amount" => 2950,
                        "price" => 3200
                    ],
                    [
                        "name" => "DStv Yanga",
                        "package_code" => "dstv-yanga",
                        "amount" => 4200,
                        "price" => 4500
                    ],
                    [
                        "name" => "DStv Confam",
                        "package_code" => "dstv-confam",
                        "amount" => 7400,
                        "price" => 7800
                    ],
                    [
                        "name" => "DStv Asia",
                        "package_code" => "dstv6",
                        "amount" => 33000,
                        "price" => 33500
                    ],
                    [
                        "name" => "DStv Compact",
                        "package_code" => "dstv79",
                        "amount" => 12500,
                        "price" => 13000

                    ],
                    [
                        "name" => "DStv Compact Plus",
                        "package_code" => "dstv7",
                        "amount" => 19800,
                        "price" => 20000
                    ],
                    [
                        "name" => "DStv Premium",
                        "package_code" => "dstv3",
                        "amount" => 29500,
                        "price" => 30000
                    ],
                    [
                        "name" => "DStv Premium Asia",
                        "package_code" => "dstv10",
                        "amount" => 33000,
                        "price" => 33500

                    ],
                    [
                        "name" => "DStv Premium-French",
                        "package_code" => "dstv9",
                        "amount" => 45600,
                        "price" => 46000
                    ],
                    [
                        "name" => "DStv Confam + ExtraView",
                        "package_code" => "confam-extra",
                        "amount" => 11400,
                        "price" => 11700
                    ],
                    [
                        "name" => "DStv Yanga + ExtraView",
                        "package_code" => "yanga-extra",
                        "amount" => 8200,
                        "price" => 8400
                    ],
                    [
                        "name" => "DStv Padi + ExtraView",
                        "package_code" => "padi-extra",
                        "amount" => 6950,
                        "price" => 7100
                    ],
                    [
                        "name" => "DStv Compact + Asia",
                        "package_code" => "com-asia",
                        "amount" => 22400,
                        "price" => 22700
                    ],
                    [
                        "name" => "DStv Compact + Extra View",
                        "package_code" => "dstv30",
                        "amount" => 16500,
                        "price" => 16700
                    ],
                    [
                        "name" => "DStv Compact + French Touch",
                        "package_code" => "com-frenchtouch",
                        "amount" => 21100,
                        "price" => 21300
                    ],
                    [
                        "name" => "DStv Premium – Extra View",
                        "package_code" => "dstv33",
                        "amount" => 33500,
                        "price" => 34000

                    ],
                    [
                        "name" => "DStv Compact Plus – Asia",
                        "package_code" => "dstv40",
                        "amount" => 29700,
                        "price" => 30000
                    ],
                    [
                        "name" => "DStv Compact + French Touch + ExtraView",
                        "package_code" => "com-frenchtouch-extra",
                        "amount" => 24400,
                        "price" => 24700
                    ],
                    [
                        "name" => "DStv Compact + Asia + ExtraView",
                        "package_code" => "com-asia-extra",
                        "amount" => 26400,
                        "price" => 26600
                    ],
                    [
                        "name" => "DStv Compact Plus + French Plus",
                        "package_code" => "dstv43",
                        "amount" => 28600,
                        "price" => 28900
                    ],
                    [
                        "name" => "DStv Compact Plus + French Touch",
                        "package_code" => "complus-frenchtouch",
                        "amount" => 24400,
                        "price" => 24600
                    ],
                    [
                        "name" => "DStv Compact Plus – Extra View",
                        "package_code" => "dstv45",
                        "amount" => 23800,
                        "price" => 24000
                    ],
                    [
                        "name" => "DStv Compact Plus + FrenchPlus + Extra View",
                        "package_code" => "complus-french-extraview",
                        "amount" => 39900,
                        "price" => 40200
                    ],
                    [
                        "name" => "DStv Compact + French Plus",
                        "package_code" => "dstv47",
                        "amount" => 35900,
                        "price" => 36200
                    ],
                    [
                        "name" => "DStv Compact Plus + Asia + ExtraView",
                        "package_code" => "dstv48",
                        "amount" => 26400,
                        "price" => 26700
                    ],
                    [
                        "name" => "DStv Premium + Asia + Extra View",
                        "package_code" => "dstv61",
                        "amount" => 43400,
                        "price" => 43700
                    ],
                    [
                        "name" => "DStv Premium + French + Extra View",
                        "package_code" => "dstv62",
                        "amount" => 40700,
                        "price" => 41000
                    ],
                    [
                        "name" => "DStv HDPVR Access Service",
                        "package_code" => "hdpvr-access-service",
                        "amount" => 29500,
                        "price" => 29700
                    ],
                    [
                        "name" => "DStv French Plus Add-on",
                        "package_code" => "frenchplus-addon",
                        "amount" => 16100,
                        "price" => 16300
                    ],
                    [
                        "name" => "DStv Asian Add-on",
                        "package_code" => "asia-addon",
                        "amount" => 9900,
                        "price" => 10200
                    ],
                    [
                        "name" => "DStv French Touch Add-on",
                        "package_code" => "frenchtouch-addon",
                        "amount" => 4600,
                        "price" => 4900
                    ],
                    [
                        "name" => "ExtraView Access",
                        "package_code" => "extraview-access",
                        "amount" => 4000,
                        "price" => 4200
                    ],
                    [
                        "name" => "DStv French 11",
                        "package_code" => "french11",
                        "amount" => 7200,
                        "price" => 7500
                    ],
                ]
            ],
            [
                "name" => "GOTV",
                "code" => "gotv",
                "img_url" => "gotv.jpeg",
                "packages" => [
                    [
                        "name" => "GOtv Smallie",
                        "package_code" => "gotv-smallie",
                        "amount" => 1300,
                        "price" => 1500
                    ],
                    [
                        "name" => "GOtv Jinja",
                        "package_code" => "gotv-jinja",
                        "amount" => 2700,
                        "price" => 2900
                    ],
                    [
                        "name" => "GOtv Jolli",
                        "package_code" => "gotv-jolli",
                        "amount" => 3950,
                        "price" => 4100
                    ],
                    [
                        "name" => "GOtv Max",
                        "package_code" => "gotv-max",
                        "amount" => 5700,
                        "price" => 5900
                    ],
                    [
                        "name" => "GOtv Supa",
                        "package_code" => "gotv-supa",
                        "amount" => 7600,
                        "price" => 7900
                    ],
                ]
            ],
            [
                "name" => "StarTimes",
                "code" => "startimes",
                "img_url" => "startimes.png",
                "packages" =>  [
                    [
                        "name" => "Startimes Nova",
                        "package_code" => "nova",
                        "amount" => 1500,
                        "price" => 1650
                    ],
                    [
                        "name" => "Startimes Basic",
                        "package_code" => "basic",
                        "amount" => 2600,
                        "price" => 2750
                    ],
                    [
                        "name" => "Startimes Smart",
                        "package_code" => "smart",
                        "amount" => 3500,
                        "price" => 3650
                    ],
                    [
                        "name" => "Startimes Classic",
                        "package_code" => "classic",
                        "amount" => 3800,
                        "price" => 4950
                    ],
                    [
                        "name" => "Startimes Super",
                        "package_code" => "super",
                        "amount" => 6500,
                        "price" => 6650
                    ],
                ]
            ]
        ];

        foreach ($Cables as $cable) {
            $the_cable = Cable::create([
                'name' => $cable['name'],
                'code' => $cable['code'],
                'img_url' => $cable['img_url'],
            ]);

            foreach ($cable['packages'] as $package) {
                $the_cable->packages()->create([
                    'name' => $package['name'],
                    'package_code' => $package['package_code'],
                    'amount' => $package['amount'],
                    'price' => $package['price'],
                ]);
            }
        }
    }
}
