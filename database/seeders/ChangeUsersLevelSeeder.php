<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChangeUsersLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users  =  User::all();

        foreach ($users as $user) {
            $user->level = User::LEVEL_GREEN;
            $user->save();
        }
    }
}
