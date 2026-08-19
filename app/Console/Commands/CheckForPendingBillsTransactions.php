<?php

namespace App\Console\Commands;

use App\Jobs\VerifyStatusOfBills;
use App\Models\UserTransaction;
use Illuminate\Console\Command;

class CheckForPendingBillsTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-for-pending-bills-transactions';

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
        $bills_sources = [
            UserTransaction::SOURCE_ELECTRICITY,
            UserTransaction::SOURCE_CABLE,
            UserTransaction::SOURCE_DATA,
            UserTransaction::SOURCE_AIRTIME,
        ];

        $pending_bills_transactions = UserTransaction::whereIn('source', $bills_sources)
            ->where('status', UserTransaction::STATUS_PENDING)
            ->get();

        if ($pending_bills_transactions->isEmpty()) {
            $this->info('No pending bills transactions found.');
            return;
        }

        foreach ($pending_bills_transactions as $transaction) {
            // Dispatch a job to verify the status of each pending transaction
            VerifyStatusOfBills::dispatch($transaction);
        }

        $this->info('Dispatched jobs to verify pending bills transactions.');
    }
}
