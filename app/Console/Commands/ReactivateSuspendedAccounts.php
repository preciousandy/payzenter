<?php

namespace App\Console\Commands;

use App\Mail\AccountReactivatedMail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ReactivateSuspendedAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:reactivate-suspended';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically reactivate user accounts whose suspension period has expired';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired suspended accounts...');

        $expiredSuspensions = User::where('account_status', User::ACCOUNT_STATUS_SUSPENDED)
            ->whereNotNull('suspended_until')
            ->where('suspended_until', '<=', now())
            ->get();

        if ($expiredSuspensions->isEmpty()) {
            $this->info('No expired suspended accounts found.');
            return;
        }

        $reactivatedCount = 0;

        foreach ($expiredSuspensions as $user) {
            try {
                // Reactivate the account
                $user->update([
                    'account_status' => User::ACCOUNT_STATUS_ACTIVE,
                    'suspended_until' => null,
                    'suspension_duration_days' => null,
                    'suspension_reason' => null
                ]);

                // Send reactivation email
                Mail::to($user->email)->send(new AccountReactivatedMail($user));

                $this->info("Reactivated account for: {$user->name} ({$user->email})");
                $reactivatedCount++;
            } catch (\Exception $e) {
                $this->error("Failed to reactivate account for {$user->name}: " . $e->getMessage());
            }
        }

        $this->info("Successfully reactivated {$reactivatedCount} account(s).");
    }
}
