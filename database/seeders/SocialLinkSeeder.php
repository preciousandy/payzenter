<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SocialLinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $socialLinks = [
            ['name' => 'Facebook', 'link' => 'https://facebook.com', 'icon' => 'facebook'],
            ['name' => 'Twitter', 'link' => 'https://twitter.com', 'icon' => 'twitter'],
            ['name' => 'Instagram', 'link' => 'https://instagram.com', 'icon' => 'instagram'],
            ['name' => 'LinkedIn', 'link' => 'https://linkedin.com', 'icon' => 'linkedin'],
            ['name' => 'YouTube', 'link' => 'https://youtube.com', 'icon' => 'youtube'],
            ['name' => 'Whatsapp', 'link' => 'https://whatsapp.com', 'icon' => 'whatsapp'],

        ];

        foreach ($socialLinks as $socialLink) {
            \App\Models\SocialLink::create($socialLink);
        }
    }
}
