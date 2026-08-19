<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ResetTransactionVolume extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-transaction-volume';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = \App\Models\User::all();

        foreach ($users as $user) {
            $user->transaction_volume = 0;
            $user->save();
        }
    }
}
