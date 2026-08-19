<?php

namespace App\Console\Commands;

use App\Exceptions\InsufficientBalance;
use App\Helpers\SettingHelper;
use App\Livewire\Transfer\SwiftPay\User;
use App\Models\AjoSavings;
use App\Models\UserTransaction;
use App\Notifications\NotifyUser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DeductSavings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:deduct-ajo-savings';

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
        $ajoSavings = AjoSavings::active()->where('payment_method', 'wallet')->get();

        foreach ($ajoSavings as $savings) {

            $dateOfLastSavingsDeducted   =    $savings->transactions()->latest()->first()->created_at;



            if ($savings->type === AjoSavings::TYPE_DAILY) {

                try {
                    $this->info('Processing ' . $savings->id);

                    if ($savings->user->wallet_balance < $savings->amount) {
                        throw new InsufficientBalance('Insufficient balance');
                    }

                    DB::beginTransaction();

                    $amount = $savings->amount;

                    $savings->increment('balance', $savings->amount);

                    $savings->user->decrement('wallet_balance', $amount);

                    $savings->user->transactions()->create([
                        'type' => UserTransaction::TYPE_DEBIT,
                        'amount' => $amount,
                        'status' => UserTransaction::STATUS_SUCCESSFUL,
                        'message' => $amount . ' was deducted for your Ajo Savings',
                        'source' => UserTransaction::SOURCE_AJO_SAVINGS
                    ]);

                    $savings->transactions()->create([
                        'amount' => $amount,
                        'source' => 'wallet',
                        'status' => UserTransaction::STATUS_SUCCESSFUL,
                        'message' => 'Funds deposited'
                    ]);

                    DB::commit();

                    $message = 'Daily savings deducted successfully';
                    Log::channel('ajo-savings')->info($message);
                } catch (InsufficientBalance $exception) {

                    $savings->transactions()->create([
                        'amount' => $savings->amount,
                        'source' => 'wallet',
                        'status' => UserTransaction::STATUS_FAILED,
                        'message' => 'Insufficient balance'
                    ]);

                    $message = 'Insufficient balance ' . $exception->getMessage();

                    Log::channel('ajo-savings')->error($message);
                } catch (\Throwable $th) {

                    $savings->transactions()->create([
                        'amount' => $savings->amount,
                        'source' => 'wallet',
                        'status' => UserTransaction::STATUS_FAILED,
                        'message' => 'Error processing ajo savings'
                    ]);

                    $message = 'Error processing ajo savings ' . $th->getMessage();

                    Log::channel('ajo-savings')->error($message);
                }
            }

            if ($savings->type === AjoSavings::TYPE_WEEKLY && $dateOfLastSavingsDeducted->addWeek()->isToday()) {

                try {

                    $this->info('Processing ' . $savings->id);

                    if ($savings->user->wallet_balance < $savings->amount) {
                        throw new InsufficientBalance('Insufficient balance');
                    }

                    DB::beginTransaction();



                    $amount = $savings->amount;

                    $savings->increment('balance', $savings->amount);

                    $savings->user->decrement('wallet_balance', $amount);

                    $savings->user->transactions()->create([
                        'type' => UserTransaction::TYPE_DEBIT,
                        'amount' => $amount,
                        'status' => UserTransaction::STATUS_SUCCESSFUL,
                        'message' => $amount . ' was deducted for your Ajo Savings',
                        'source' => UserTransaction::SOURCE_AJO_SAVINGS
                    ]);

                    $savings->transactions()->create([
                        'amount' => $amount,
                        'source' => 'wallet',
                        'status' => UserTransaction::STATUS_SUCCESSFUL,
                        'message' => 'Funds deposited'


                    ]);

                    DB::commit();


                    $message = 'Weekly savings deducted successfully';
                    Log::channel('ajo-savings')->info($message);
                } catch (InsufficientBalance $exception) {

                    $savings->transactions()->create([
                        'amount' => $savings->amount,
                        'source' => 'wallet',
                        'status' => UserTransaction::STATUS_FAILED,
                        'message' => 'Insufficient balance'
                    ]);

                    $message = 'Insufficient balance ' . $exception->getMessage();

                    Log::channel('ajo-savings')->error($message);
                } catch (\Throwable $th) {



                    $savings->transactions()->create([
                        'amount' => $savings->amount,
                        'source' => 'wallet',
                        'status' => UserTransaction::STATUS_FAILED,
                        'message' => 'Error processing ajo savings'
                    ]);

                    $message = 'Error processing ajo savings ' . $th->getMessage();

                    Log::channel('ajo-savings')->error($message);
                }
            }

            if ($savings->type === AjoSavings::TYPE_MONTHLY && $dateOfLastSavingsDeducted->addMonth()->isToday()) {

                try {

                    $this->info('Processing ' . $savings->id);

                    if ($savings->user->wallet_balance < $savings->amount) {
                        throw new InsufficientBalance('Insufficient balance');
                    }

                    DB::beginTransaction();



                    $amount = $savings->amount;

                    $savings->increment('balance', $savings->amount);

                    $savings->user->decrement('wallet_balance', $amount);

                    $savings->user->transactions()->create([
                        'type' => UserTransaction::TYPE_DEBIT,
                        'amount' => $amount,
                        'status' => UserTransaction::STATUS_SUCCESSFUL,
                        'message' => $amount . ' was deducted for your Ajo Savings',
                        'source' => UserTransaction::SOURCE_AJO_SAVINGS
                    ]);

                    $savings->transactions()->create([
                        'amount' => $amount,
                        'source' => 'wallet',
                        'status' => UserTransaction::STATUS_SUCCESSFUL,
                        'message' => 'Funds deposited'

                    ]);

                    DB::commit();


                    $message = 'Monthly savings deducted successfully';
                    Log::channel('ajo-savings')->info($message);
                } catch (InsufficientBalance $exception) {

                    $savings->transactions()->create([
                        'amount' => $savings->amount,
                        'source' => 'wallet',
                        'status' => UserTransaction::STATUS_FAILED,
                        'message' => 'Insufficient balance'
                    ]);

                    $message = 'Insufficient balance ' . $exception->getMessage();

                    Log::channel('ajo-savings')->error($message);
                } catch (\Throwable $th) {



                    $savings->transactions()->create([
                        'amount' => $savings->amount,
                        'source' => 'wallet',
                        'status' => UserTransaction::STATUS_FAILED,
                        'message' => 'Error processing ajo savings'
                    ]);

                    $message = 'Error processing ajo savings ' . $th->getMessage();

                    Log::channel('ajo-savings')->error($message);
                }
            }

            if ($savings->type === AjoSavings::TYPE_YEARLY && $dateOfLastSavingsDeducted->addYear()->isToday()) {

                try {

                    $this->info('Processing ' . $savings->id);

                    if ($savings->user->wallet_balance < $savings->amount) {
                        throw new InsufficientBalance('Insufficient balance');
                    }

                    DB::beginTransaction();



                    $amount = $savings->amount;

                    $savings->increment('balance', $savings->amount);

                    $savings->user->decrement('wallet_balance', $amount);

                    $savings->user->transactions()->create([
                        'type' => UserTransaction::TYPE_DEBIT,
                        'amount' => $amount,
                        'status' => UserTransaction::STATUS_SUCCESSFUL,
                        'message' => $amount . ' was deducted for your Ajo Savings',
                        'source' => UserTransaction::SOURCE_AJO_SAVINGS
                    ]);

                    $savings->transactions()->create([
                        'amount' => $amount,
                        'source' => 'wallet',
                        'status' => UserTransaction::STATUS_SUCCESSFUL,
                        'message' => 'Funds deposited'

                    ]);
                    DB::commit();


                    $message = 'Yearly savings deducted successfully';
                    Log::channel('ajo-savings')->info($message);
                } catch (InsufficientBalance $exception) {

                    $savings->transactions()->create([
                        'amount' => $savings->amount,
                        'source' => 'wallet',
                        'status' => UserTransaction::STATUS_FAILED,
                        'message' => 'Insufficient balance'
                    ]);

                    $message = 'Insufficient balance ' . $exception->getMessage();

                    Log::channel('ajo-savings')->error($message);
                } catch (\Throwable $th) {



                    $savings->transactions()->create([
                        'amount' => $savings->amount,
                        'source' => 'wallet',
                        'status' => UserTransaction::STATUS_FAILED,
                        'message' => 'Error processing ajo savings'
                    ]);

                    $message = 'Error processing ajo savings ' . $th->getMessage();

                    Log::channel('ajo-savings')->error($message);
                }
            }
        }
    }
}
