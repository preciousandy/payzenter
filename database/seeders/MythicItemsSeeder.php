<?php

namespace Database\Seeders;

use App\Models\MythicItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MythicItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mythic_items = [
            "M13 Morningstar",
            "AK-47 Radiance",
            "Rytec AMR Nautilus",
            "QQ9 Double Kinetics",
            "Fennec Ascended",
            "KRM-262 Cardinal",
            "M4LMG Metamorphosis",
            "DL Q33 Cosmos",
            "M21 EBR - Deep Sea",
            "RUS-79 - Cybernetic",
            "Locus - Aurora Borealis",
            "SVD - Mythic Dragon",
            "M16 - Royal Knight",
            "Man-O-War - Ascendancy",
            "CBR4 - Black Gold",
            "HS2126 - Phoenix",
            "RPD - Lunar New Year",
            "UL736 - Magma",
            "MK2 Carbine - Emerald",
            "AK-47 - Frostbite",
            "M13 - Evil Clown",
            "Man-O-War - Peacekeeper",
            "M4LMG - Black Gold",
            "DL Q33 - Holy Trinity",
            "HS2126 - Dragonbone",
            "CBR4 - No Stock",
            "RUS-79 - Oxidized",
            "SVD - Corruptor",
            "M16 - Backscratcher",
            "M21 EBR - Wildfire",
            "KRM-262 - Pumpkin",
            "QQ9 - Papillon",
            "Locus - Apostle",
            "M4 - Archangel",
            "UL736 - Acid Rain",
            "RPD - Hades",
            "MP5 - Black Gold",
            "Type 25 - Geometry",
            "KN-44 - Annihilator",
            "ASM10 - Golden Phoenix",
            "AGR 2014 - Golden Dragon",
            "FR556 - Six Legged",
            "M249 - Space Lizard",
            "BY15 - Black Gold",
            "HS2126 - Lava",
            "Shorty - Goldfish",
            "S36 - Enchanted",
            "KRM-262 - Heartbeat",
            "RUS-79 - Black Gold",
            "M16 - Gold Rush",
            "M4LMG - Space Agent",
            "DL Q33 - Arctic Wolf",
            "M21 EBR - Sakura",
        ];

        foreach ($mythic_items as $mythic_item) {
            MythicItem::create([
                'name' => $mythic_item,
            ]);
        }
    }
}
