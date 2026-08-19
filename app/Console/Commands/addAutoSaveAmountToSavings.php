<?php

namespace App\Console\Commands;

use App\Exceptions\InsufficientBalance;
use App\Models\Savings;
use App\Models\UserTransaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class addAutoSaveAmountToSavings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-auto-save-amount-to-savings';

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
        $activeAutoSavings = Savings::active()->autoSave()->get();

        foreach ($activeAutoSavings as $savings) {

            $dateOfLastSavingsDeducted   = $savings->auto_save_last_deduction;

            $this->info($savings->frequency === Savings::FREQUENCY_DAILY);

            if ($savings->frequency === Savings::FREQUENCY_DAILY && $dateOfLastSavingsDeducted->addDay()->isToday()) {

                try {
                    $this->info('Processing daily ' . $savings->id);

                    if ($savings->user->wallet_balance < $savings->auto_save_amount) {
                        throw new \Exception('Insufficient balance');
                    }

                    DB::beginTransaction();

                    $savings->increment('balance', $savings->auto_save_amount);

                    $savings->user->decrement('wallet_balance', $savings->auto_save_amount);

                    $savings->activities()->create([
                        'message' => "Auto save amount of ₦{$savings->auto_save_amount} was added to your savings",
                    ]);

                    $savings->user->transactions()->create([
                        'amount' => $savings->auto_save_amount,
                        'source' => UserTransaction::SOURCE_SAVE_WITH_INTEREST,
                        'status' => UserTransaction::STATUS_SUCCESSFUL,
                        'message' => "Auto save amount of ₦{$savings->auto_save_amount} was added to your savings",
                        'type' => UserTransaction::TYPE_DEBIT
                    ]);

                    $savings->update([
                        'auto_save_last_deduction' => now()
                    ]);

                    DB::commit();

                    $message = 'Daily savings deducted successfully';
                } catch (\Throwable $th) {

                    $this->info('error' . $th->getMessage());

                    $savings->activities()->create([
                        'message' => "Error processing auto save amount of ₦{$savings->auto_save_amount}",
                    ]);
                }
            }

            if ($savings->frequency === Savings::FREQUENCY_WEEKLY && $dateOfLastSavingsDeducted->addWeek()->isToday()) {

                try {
                    $this->info('Processing weekly ' . $savings->id);

                    if ($savings->user->wallet_balance < $savings->auto_save_amount) {
                        throw new \Exception('Insufficient balance');
                    }

                    DB::beginTransaction();

                    $savings->increment('balance', $savings->auto_save_amount);

                    $savings->user->decrement('wallet_balance', $savings->auto_save_amount);

                    $savings->activities()->create([
                        'message' => "Auto save amount of ₦{$savings->auto_save_amount} was added to your savings",
                    ]);

                    $savings->user->transactions()->create([
                        'amount' => $savings->auto_save_amount,
                        'source' => UserTransaction::SOURCE_SAVE_WITH_INTEREST,
                        'status' => UserTransaction::STATUS_SUCCESSFUL,
                        'message' => "Auto save amount of ₦{$savings->auto_save_amount} was added to your savings",
                        'type' => UserTransaction::TYPE_DEBIT
                    ]);

                    $savings->update([
                        'auto_save_last_deduction' => now()
                    ]);

                    DB::commit();

                    $message = 'Daily savings deducted successfully';
                } catch (\Throwable $th) {

                    $savings->activities()->create([
                        'message' => "Error processing auto save amount of ₦{$savings->auto_save_amount}",
                    ]);
                }
            }

            if ($savings->frequency === Savings::FREQUENCY_MONTHLY && $dateOfLastSavingsDeducted->addMonth()->isToday()) {

                try {
                    $this->info('Processing monthly' . $savings->id);

                    if ($savings->user->wallet_balance < $savings->auto_save_amount) {
                        throw new \Exception('Insufficient balance');
                    }

                    DB::beginTransaction();

                    $savings->increment('balance', $savings->auto_save_amount);

                    $savings->user->decrement('wallet_balance', $savings->auto_save_amount);

                    $savings->activities()->create([
                        'message' => "Auto save amount of ₦{$savings->auto_save_amount} was added to your savings",
                    ]);

                    $savings->user->transactions()->create([
                        'amount' => $savings->auto_save_amount,
                        'source' => UserTransaction::SOURCE_SAVE_WITH_INTEREST,
                        'status' => UserTransaction::STATUS_SUCCESSFUL,
                        'message' => "Auto save amount of ₦{$savings->auto_save_amount} was added to your savings",
                        'type' => UserTransaction::TYPE_DEBIT
                    ]);

                    $savings->update([
                        'auto_save_last_deduction' => now()
                    ]);

                    DB::commit();

                    $message = 'Daily savings deducted successfully';
                } catch (\Throwable $th) {

                    $savings->activities()->create([
                        'message' => "Error processing auto save amount of ₦{$savings->auto_save_amount}",
                    ]);
                }
            }
        }
    }
}
