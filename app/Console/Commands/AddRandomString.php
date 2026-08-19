<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AddRandomString extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-random-string';

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
        $randomString = bin2hex(random_bytes(16));

        \App\Models\Testing::create([
            'random_string' => $randomString,
        ]);

        $this->info('Random string added successfully.');
    }
}
