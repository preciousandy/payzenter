<?php

namespace App\Console\Commands;

use App\Helpers\SettingHelper;
use App\Models\Savings;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class addInterestToSavings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-interest-to-savings';

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
            $activeSavings = Savings::active()->get();

            DB::beginTransaction();

            foreach ($activeSavings as $savings) {
                if ($savings->type == Savings::TYPE_LOCKED) {
                    $interestGained = getPercentageAmount($savings->balance, SettingHelper::get(SettingHelper::LOCKED_SAVINGS_INTEREST));
                } else {
                    $interestGained = getPercentageAmount($savings->balance, SettingHelper::get(SettingHelper::UNLOCKED_SAVINGS_INTEREST));
                }

                $savings->increment('interest_accumulated', $interestGained);

                $this->info($savings->name .  ' Interest gained: ' . $interestGained);

                $savings->activities()->create([
                    'message' => 'You gained an interest of ₦ ' . formatNumber($interestGained) . ' to your savings',
                ]);
            }

            DB::commit();
        } catch (\Throwable $th) {
            Log::channel('ajo-savings')->error($th->getMessage());
        }
    }
}
