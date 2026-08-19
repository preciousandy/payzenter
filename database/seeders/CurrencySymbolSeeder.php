<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencySymbolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currency_symbols = [
            "USD" => "$",
            "EUR" => "€",
            "JPY" => "¥",
            "GBP" => "£",
            "AUD" => "A$",
            "CAD" => "C$",
            "CHF" => "Fr",
            "CNY" => "¥",
            "NZD" => "NZ$",
            "SGD" => "S$"
        ];


        foreach ($currency_symbols as $code => $symbol) {
            \App\Models\ExchangeRate::where('code', $code)->update(['currency_symbol' => $symbol]);
        }
    }
}
