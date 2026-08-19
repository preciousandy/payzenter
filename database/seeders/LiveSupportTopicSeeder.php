<?php

namespace Database\Seeders;

use App\Models\liveSupportTopic;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LiveSupportTopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $topics = [
            'General',
            'Transfer',
            "Bills",
            "Ajo Savings",
            "Ajo Contribution",
        ];

        foreach ($topics as $topic) {
            liveSupportTopic::updateOrCreate(['name' => $topic]);
        }
    }
}
