<?php

namespace App\Console\Commands;

use App\Helpers\SettingHelper;
use App\Models\AjoContribution;
use App\Models\AjoContributionMember;
use App\Models\AjoContributionRound;
use App\Models\User;
use App\Models\UserTransaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PayoutAjoContribution extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:payout-ajo-contribution';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process Ajo Contribution payouts and manage round transitions automatically';

    /**
     * Execute the console command.
     */

    private function deductAutoSaveContributions($contribution, $round)
    {
        // Validate inputs
        if (!$round || !$round->exists) {
            $this->line('Invalid round provided for contribution: ' . $contribution->name);
            return;
        }

        $membersWithAutoSave = $contribution->members()->where('if_auto_save', true)->get();

        foreach ($membersWithAutoSave as $member) {
            DB::beginTransaction();

            try {
                // Check if user already donated with row locking to prevent race conditions
                $existingDonation = $round->donations()
                    ->where('user_id', $member->id)
                    ->lockForUpdate()
                    ->exists();

                if ($existingDonation) {
                    DB::rollback();
                    continue;
                }

                // Validate sufficient balance
                if ($member->wallet_balance < $contribution->amount) {
                    DB::rollback();
                    $this->line("Insufficient balance for user {$member->name} in contribution: {$contribution->name}");
                    continue;
                }

                // Validate beneficiary exists
                $beneficiary = $round->beneficiary;
                if (!$beneficiary) {
                    DB::rollback();
                    $this->line("No beneficiary found for round {$round->order} in contribution: {$contribution->name}");
                    continue;
                }

                // Perform all financial operations atomically
                $member->decrement('wallet_balance', $contribution->amount);

                $member->transactions()->create([
                    'amount' => $contribution->amount,
                    'type' => UserTransaction::TYPE_DEBIT,
                    'message' => $contribution->name .  ' Ajo Contribution Auto deduction',
                    'source' => UserTransaction::SOURCE_AJO_CONTRIBUTION,
                    'status' => UserTransaction::STATUS_SUCCESSFUL
                ]);

                $round->increment('balance', $contribution->amount);

                $round->donations()->create([
                    'user_id' => $member->id,
                    'amount' => $contribution->amount,
                    'status' => UserTransaction::STATUS_SUCCESSFUL
                ]);

                $beneficiary->increment('ajo_contribution_wallet', $contribution->amount);

                $beneficiary->transactions()->create([
                    'amount' => $contribution->amount,
                    'type' => UserTransaction::TYPE_CREDIT,
                    'message' => $contribution->amount . ' was added to your Ajo Contribution wallet from ' . $contribution->name . ' Ajo contribution',
                    'source' => UserTransaction::SOURCE_AJO_CONTRIBUTION,
                    'status' => UserTransaction::STATUS_SUCCESSFUL
                ]);

                DB::commit();
                $this->line("Auto-deducted {$contribution->amount} from {$member->name} for {$contribution->name}");
            } catch (\Exception $e) {
                DB::rollback();
                $this->line("Error processing auto-deduction for {$member->name} in {$contribution->name}: " . $e->getMessage());
                continue;
            }
        }
    }

    private function givePayouts($contribution)
    {
        // Validate contribution is active and has active round
        if (!$contribution || $contribution->status !== AjoContribution::STATUS_ACTIVE) {
            $this->line("Contribution {$contribution->name} is not active, skipping.");
            return;
        }

        $activeRound = $contribution->activeRound();
        if (!$activeRound) {
            $this->line("No active round found for contribution: {$contribution->name}");
            return;
        }

        try {
            // deduct the auto save contributions for the current round
            $this->deductAutoSaveContributions($contribution, $activeRound);

            // if end date is today and there are defaulters
            if (count($contribution->getActiveRoundDefaulters()) > 0 && $contribution->next_round_date->isToday()) {

                if ($contribution->activeRound()->status === AjoContributionRound::STATUS_PENDING) {
                    return;
                }

                DB::beginTransaction();

                try {
                    $contribution->activeRound()->update([
                        'status' => AjoContributionRound::STATUS_PENDING
                    ]);

                    $defaultersIDs = $contribution->getActiveRoundDefaulters()->pluck('id')->toArray();

                    User::whereIn('id', $defaultersIDs)->update([
                        'is_owing_ajo_contribution' => true
                    ]);

                    DB::commit();
                    $this->line('There are defaulters for contribution: ' . $contribution->name);
                } catch (\Exception $e) {
                    DB::rollback();
                    $this->line('Error handling defaulters for contribution ' . $contribution->name . ': ' . $e->getMessage());
                }

                return;
            }

            if (count($contribution->getActiveRoundDefaulters()) > 0) {
                return;
            }

            if ($activeRound->status !== AjoContributionRound::STATUS_COMPLETED) {
                DB::beginTransaction();

                try {
                    // pay admin for business contribution
                    if ($contribution->type === AjoContribution::TYPE_BUSINESS) {
                        $referral_bonus = getTotalFee($activeRound->balance, 0,  SettingHelper::get(SettingHelper::AJO_CONTRIBUTION_REFERRAL_BONUS));

                        $this->line('Ajo contribution: ' . $contribution->name . ' referral bonus: ' . $referral_bonus);

                        $admin = $contribution->members()->where(['role' => AjoContributionMember::ROLE_ADMIN, 'is_contributing' => false])->first();

                        if ($admin) {
                            $admin->increment('ajo_contribution_wallet', $referral_bonus);

                            $admin->transactions()->create([
                                'amount' => $referral_bonus,
                                'type' => UserTransaction::TYPE_CREDIT,
                                'message' => 'You received ₦' . $referral_bonus . ' as referral bonus from ' . $contribution->name . ' Ajo Contribution for round ' . $activeRound->order,
                                'source' => UserTransaction::SOURCE_AJO_CONTRIBUTION,
                                'status' => UserTransaction::STATUS_SUCCESSFUL
                            ]);
                        } else {
                            $this->line('No admin found for business contribution: ' . $contribution->name);
                        }
                    }

                    $activeRound->update([
                        'status' => AjoContributionRound::STATUS_COMPLETED
                    ]);

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollback();
                    $this->line('Error completing round for contribution ' . $contribution->name . ': ' . $e->getMessage());
                    return;
                }

                // move to next round logic
                DB::beginTransaction();

                try {
                    $number_of_parcticipants = $contribution->members()->where('is_contributing', true)->count();
                    $completed_rounds = $contribution->rounds()->where('status', AjoContributionRound::STATUS_COMPLETED)->count();

                    if ($number_of_parcticipants !== $completed_rounds) {

                        $contribution->update([
                            'current_round' => $activeRound->order + 1,
                            'next_round_date' => match ($contribution->frequency) {
                                AjoContribution::FREQUENCY_WEEKLY => now()->addWeek(),
                                AjoContribution::FREQUENCY_MONTHLY => now()->addMonth(),
                                default => now()->addYear()
                            }
                        ]);

                        // update the next round status
                        $nextRound = AjoContributionRound::where([
                            'ajo_contribution_id' => $contribution->id,
                            'order' => $activeRound->order + 1
                        ])->first();

                        if ($nextRound) {
                            $nextRound->update(['status' => AjoContributionRound::STATUS_ON_GOING]);

                            // deduct users auto save for next round
                            $this->deductAutoSaveContributions($contribution, $nextRound);
                        } else {
                            $this->line('Next round not found for contribution: ' . $contribution->name);
                        }
                    }

                    $this->line('Moved to next round for contribution: ' . $contribution->name);
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollback();
                    $this->line('Error moving to next round for contribution ' . $contribution->name . ': ' . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            $this->line('General error processing contribution ' . $contribution->name . ': ' . $e->getMessage());
        }
    }

    public function handle()
    {
        $this->line('Starting Ajo Contribution payout processing...');

        try {
            $activeAjoContributions = AjoContribution::active()->get();
            $this->line('Found ' . $activeAjoContributions->count() . ' active Ajo contributions to process.');

            foreach ($activeAjoContributions as $contribution) {
                $this->line('Processing contribution: ' . $contribution->name);

                try {
                    // Skip if active round is already completed
                    $activeRound = $contribution->activeRound();
                    if (!$activeRound) {
                        $this->line('No active round found for contribution: ' . $contribution->name);
                        continue;
                    }

                    if ($activeRound->status === AjoContributionRound::STATUS_COMPLETED) {
                        $this->line('Round already completed for contribution: ' . $contribution->name);
                        continue;
                    }

                    $this->givePayouts($contribution);
                } catch (\Exception $e) {
                    $this->line('Error processing contribution ' . $contribution->name . ': ' . $e->getMessage());
                    // Continue with other contributions even if one fails
                    continue;
                }
            }

            $this->line('Ajo Contribution payout processing completed.');
        } catch (\Exception $e) {
            $this->line('Fatal error in Ajo Contribution payout processing: ' . $e->getMessage());
            return 1; // Return error code
        }

        return 0; // Return success code
    }
}
