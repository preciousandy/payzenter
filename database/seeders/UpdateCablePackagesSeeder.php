<?php

namespace Database\Seeders;

use App\Models\Cable;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UpdateCablePackagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Cable::where('name', 'DSTV')->update([
            'code' => 'dstv',
        ]);
        Cable::where('name', 'GOTV')->update([
            'code' => 'gotv',
        ]);
        Cable::where('name', 'Startimes')->update([
            'code' => 'startimes',
        ]);
    }
}
