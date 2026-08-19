<?php

namespace App\Console\Commands;

use App\Helpers\SettingHelper;
use App\Models\Savings;
use App\Models\UserTransaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CloseLockedSavings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:close-locked-savings';

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
        try {
            $lockedActiveSavings = Savings::active()->whereType('locked')->get();
            $fee = SettingHelper::get(SettingHelper::CLOSE_LOCKED_SAVINGS_FEE);

            $this->info("Found {$lockedActiveSavings->count()} locked active savings accounts to process");

            $processedCount = 0;
            $errorCount = 0;

            foreach ($lockedActiveSavings as $savings) {

                if (!$savings->end_date) {
                    $this->error("Savings account: {$savings->name} (ID: {$savings->id}) does not have an end date");
                    continue;
                }
                try {
                    if ($savings->end_date->isPast() || $savings->end_date->isToday()) {
                        $this->info("Processing savings account: {$savings->name} (ID: {$savings->id}) for user: {$savings->user->email}");

                        DB::beginTransaction();

                        $amountEarned = $savings->balance + $savings->interest_accumulated;
                        $total = $amountEarned - $fee;

                        $this->info("Savings balance: {$savings->balance}, Interest: {$savings->interest_accumulated}, Fee: {$fee}, Total to credit: {$total}");

                        $savings->user->increment('wallet_balance', $total);

                        $savings->user->transactions()->create([
                            'amount' => $total,
                            'type' => UserTransaction::TYPE_CREDIT,
                            'message' => 'Your savings account ' . $savings->name . ' was closed as the end date has reached',
                            'status' => UserTransaction::STATUS_SUCCESSFUL,
                            'source' => UserTransaction::SOURCE_SAVE_WITH_INTEREST
                        ]);

                        $savings->update([
                            'status' => Savings::STATUS_CLOSED,
                            'balance' => 0,
                            'interest_accumulated' => 0,
                            'end_date' => now()
                        ]);

                        $savings->activities()->create([
                            'message' => "Your savings account was closed as the end date has reached",
                        ]);

                        DB::commit();

                        $processedCount++;
                        $this->info("Successfully closed savings account: {$savings->name}");
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                    $errorCount++;
                    $this->error("Error processing savings account: {$savings->name} (ID: {$savings->id}) - " . $e->getMessage());
                    Log::error("Failed to close locked savings", [
                        'savings_name' => $savings->name,
                        'savings_id' => $savings->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    continue;
                }
            }

            $this->info("Successfully processed {$processedCount} savings accounts");
            if ($errorCount > 0) {
                $this->warn("Failed to process {$errorCount} savings accounts - check logs for details");
            }
        } catch (\Exception $e) {
            $this->error("Fatal error in close locked savings command: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());

            throw $e;
        }
    }
}
