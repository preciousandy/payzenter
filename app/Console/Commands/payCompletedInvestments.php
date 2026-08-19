<?php

namespace App\Console\Commands;

use App\Models\CryptoCurrency;
use App\Models\Investment;
use App\Models\StockPrice;
use App\Models\UserTransaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class payCompletedInvestments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:pay-completed-investments';

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
        // function applyPercentage($percentage, $amount)
        // {
        //     // Calculate the adjustment amount
        //     $adjustment = ($percentage / 100) * $amount;

        //     // Apply the adjustment to the original amount
        //     $newAmount = $amount + $adjustment;


        //     return round($newAmount, 2);
        // }

        $investments = Investment::active()->whereDate('end_date', '<=', now())->with('user')->get();

        foreach ($investments as $investment) {
            $currentPercentage = 0;

            if ($investment->asset_type == Investment::ASSET_TYPE_CRYPTO) {
                $currentPercentage = CryptoCurrency::where('symbol', $investment->asset_symbol)->first()->change_percentage;
            }

            if ($investment->asset_type == Investment::ASSET_TYPE_STOCK) {
                $currentPercentage = StockPrice::where('symbol', $investment->asset_symbol)->first()->change_percent;
            }



            $amount_earned = calculatePercentageChange($currentPercentage, $investment->amount_invested);


            DB::beginTransaction();

            $investment->update([
                'final_change_percentage' => $currentPercentage,
                'amount_earned' => $amount_earned,
                'status' => Investment::STATUS_COMPLETED
            ]);

            $investment->user->increment('wallet_balance', $amount_earned);

            $investment->user->transactions()->create([
                'type' => UserTransaction::TYPE_CREDIT,
                'amount' => $amount_earned,
                'status' => UserTransaction::STATUS_SUCCESSFUL,
                'message' => "You earned $amount_earned from your investment in $investment->asset_name",
                'source' => UserTransaction::SOURCE_INVESTMENTS
            ]);

            DB::commit();
        }
    }
}
