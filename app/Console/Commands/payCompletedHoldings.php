<?php

namespace App\Console\Commands;

use App\Models\ExchangeRate;
use App\Models\Holdings;
use App\Models\MetalRate;
use App\Models\UserTransaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class payCompletedHoldings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:pay-completed-holdings';

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
        $holdings = Holdings::active()
            ->whereDate('end_date', '<=', now())
            ->with('user')
            ->get();

        foreach ($holdings as $holding) {
            $currentRate = 0;

            if ($holding->asset_type === Holdings::ASSET_TYPE_FIAT) {
                $currentRate = ExchangeRate::where('code', $holding->asset_symbol)->first()->rate;
            }

            if ($holding->asset_type === Holdings::ASSET_TYPE_METAL) {
                $currentRate = MetalRate::where('metal', $holding->asset_symbol)->first()->price;
            }

            $amount_earned = $holding->asset_amount * $currentRate;

            DB::beginTransaction();

            $holding->update([
                'amount_earned' => $amount_earned,
                'naira_end_rate' => $currentRate,
                'status' => Holdings::STATUS_COMPLETED
            ]);

            $holding->user->increment('wallet_balance', $amount_earned);

            $holding->user->transactions()->create([
                'amount' => $amount_earned,
                'type' => UserTransaction::TYPE_CREDIT,
                'status' => UserTransaction::STATUS_SUCCESSFUL,
                'message' => "You have earned "  . round($amount_earned, 2) . "  from your $holding->asset_symbol holdings.",
                'source' => UserTransaction::SOURCE_HOLDINGS
            ]);

            DB::commit();
        }
    }
}
