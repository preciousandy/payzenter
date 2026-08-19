<?php

namespace App\Console\Commands;

use App\Jobs\CheckTransferStatus;
use App\Models\Transfer;
use Illuminate\Console\Command;

class UpdateTransferStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-transfer-status';

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
        $pendingTransfers = Transfer::whereIn('status', [
            Transfer::STATUS_PENDING,
            Transfer::DOT_TRANSFER_STATUS_RUNNING,
        ])
            ->where('created_at', '>=', now()->subHours(24)) // limit old ones
            ->get();

        // $this->line("Found {$pendingTransfers->count()} pending transfers to check.");

        foreach ($pendingTransfers as $transfer) {
            CheckTransferStatus::dispatch($transfer);
        }
    }
}
