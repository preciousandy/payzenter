<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BettingProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bettingSites = [
            "1xBet",
            "BangBet",
            "Bet9ja",
            "BetKing",
            "BetLand",
            "BetLion",
            "BetWay",
            "CloudBet",
            "LiveScoreBet",
            "MerryBet",
            "NaijaBet",
            "NairaBet",
            "SupaBet"
        ];

        foreach ($bettingSites as $site) {
            \App\Models\BettingProvider::updateOrCreate(
                ['name' => $site],
                ['name' => $site]
            );
        }
    }
}
