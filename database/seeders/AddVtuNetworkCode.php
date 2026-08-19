<?php

namespace Database\Seeders;

use App\Models\Network;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddVtuNetworkCode extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Network::where('name', 'MTN NG')->update(['vtu_code' => 'mtn']);
        Network::where('name', 'GLO NG')->update(['vtu_code' => 'glo']);
        Network::where('name', 'Airtel NG')->update(['vtu_code' => 'airtel']);
        Network::where('name', '9mobile NG')->update(['vtu_code' => 'etisalat']);
    }
}
