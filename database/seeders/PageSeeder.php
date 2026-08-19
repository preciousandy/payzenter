<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Page::create([
            'code' => \App\Models\Page::TERMS_AND_CONDITIONS,
            'content' => 'Terms and conditions content',
        ]);

        \App\Models\Page::create([
            'code' => \App\Models\Page::PRIVACY_POLICY,
            'content' => 'Privacy policy content',
        ]);
    }
}
