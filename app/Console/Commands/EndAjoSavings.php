<?php

namespace App\Console\Commands;

use App\Helpers\SettingHelper;
use App\Models\AjoSavings;
use App\Models\UserTransaction;
use App\Notifications\NotifyUser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class EndAjoSavings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:end-ajo-savings';

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
        $activeAjoSavings = AjoSavings::active()->get();

        DB::beginTransaction();
        foreach ($activeAjoSavings as  $savings) {
            if ($savings->end_date->isPast() || $savings->end_date->isToday()) {

                if ($savings->payback_amount > 0) {
                    $message = 'Your ' . $savings->type . ' Ajo savings has some pending amount that needs to be paid back before it can be closed';
                    $savings->user->notify(new NotifyUser($message, "Ajo Savings"));
                    $this->info($message);
                    continue;
                }

                $totalFee = getTotalFee($savings->amount, SettingHelper::get(
                    SettingHelper::AJO_SAVINGS_FEE
                ),  SettingHelper::get(
                    SettingHelper::AJO_SAVINGS_PERCENTAGE
                ));

                $amount = $savings->balance - $totalFee;

                $savings->user->increment('wallet_balance', $amount);

                if ($savings->referred_by) {
                    $referrer_bonus =
                        getTotalFee($savings->balance, 0,  SettingHelper::get(
                            SettingHelper::AJO_SAVINGS_REFERRAL_BONUS
                        ));

                    $savings->referrer->increment('wallet_balance', $referrer_bonus);

                    $savings->referrer->transactions()->create([
                        'amount' => $referrer_bonus,
                        'type' => UserTransaction::TYPE_CREDIT,
                        'message' => 'You earned referral bonus for ' . $savings->user->name . ' Ajo savings account',
                        'status' => UserTransaction::STATUS_SUCCESSFUL,
                        'source' => UserTransaction::SOURCE_AJO_SAVINGS
                    ]);
                }

                $savings->user->transactions()->create([
                    'amount' => $amount,
                    'type' => UserTransaction::TYPE_CREDIT,
                    'message' => 'Your ' . $savings->type . ' Ajo savings account   was closed as the end date has reached',
                    'status' => UserTransaction::STATUS_SUCCESSFUL,
                    'source' => UserTransaction::SOURCE_AJO_SAVINGS
                ]);

                $savings->update([
                    'status' => AjoSavings::STATUS_CLOSED,
                    'amount_earned' => $amount,
                ]);
            }
        }

        DB::commit();
    }
}
