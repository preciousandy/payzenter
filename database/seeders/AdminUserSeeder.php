<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // Use your application's User model
use Illuminate\Support\Facades\Hash;
use App\Utils\Roles; // Use your Roles class

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Define the Admin role name from your Roles utility class
        $adminRole = Roles::ADMIN;

        // Create the Admin User if they don't already exist by email
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'], // Unique identifier to check existence
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                // Always use Hash::make() for password storage
                'password' => Hash::make('secretpassword'), // Set a default password
                'email_verified_at' => now(),
                'wallet_balance' =>0.00
            ]
        );
        
        
        if ($admin) {
            $admin->assignRole($adminRole);
            $admin->update(['kyc_status' => \App\Models\Kyc::STATUS_VERIFIED]);
        }
    }
}