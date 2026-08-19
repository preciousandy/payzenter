<?php

namespace App\Console;

use App\Jobs\CheckTransferStatus;
use App\Models\Transfer;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('app:deduct-ajo-savings')->dailyAt('00:00');
        $schedule->command('app:update-fiats-and-metals')->dailyAt('00:10');
        $schedule->command('app:update-stocks')->dailyAt('00:20');
        $schedule->command('app:update-crypto')->dailyAt('00:30');
        $schedule->command('app:pay-completed-holdings')->dailyAt('00:40');
        $schedule->command('app:pay-completed-investments')->dailyAt('00:50');
        $schedule->command('app:add-interest-to-savings')->dailyAt('01:00');
        $schedule->command('app:close-locked-savings')->dailyAt('01:10');
        $schedule->command('app:add-auto-save-amount-to-savings')->dailyAt('01:20');
        $schedule->command('app:end-ajo-savings')->dailyAt('01:30');
        $schedule->command('app:payout-ajo-contribution')->dailyAt('01:40');
        $schedule->command('app:update-transfer-rate')->dailyAt('01:50');
        $schedule->command('app:upgrade-users-level')->dailyAt('02:00');
        $schedule->command('users:reactivate-suspended')->hourly(); // Check for expired suspensions every hour
        $schedule->command('app:update-transfer-status')->everyThreeMinutes();
        $schedule->command('app:update-data-plans')->weekly();
        $schedule->command('app:update-cable-packages')->weekly();
        $schedule->command('app:reset-transaction-volume')->daily();
        $schedule->command('app:check-for-pending-bills-transactions')->everyThreeMinutes();
        $schedule->command('app:send-rates-notification')->dailyAt('10:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
