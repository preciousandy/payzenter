<?php

namespace App\Jobs;

use App\Models\Transfer;
use App\Models\User;
use App\Models\UserTransaction;
use App\Services\TransferService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransferToBank implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public $account, public User $user,)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $transferService = new TransferService();
            $provider = $transferService->resolveTransferProvider();

            if ($provider === 'palmpay') {
                $transferReference = $transferService->initiatePalmPayTransfer(
                    $this->account['bank_code'],
                    $this->account['account_number'],
                    $this->account['account_name'],
                    $this->account['amount'],
                    $this->account['reference'],
                    $this->account['description'] ?? 'Settlement withdrawal'
                );

                // If PalmPay returns a payout failure (e.g. MC100041 abnormal account status), attempt Flutterwave fallback
                if (($transferReference['state'] ?? null) === 'FAILED' || ($transferReference['status'] ?? null) === 'error') {
                    Log::channel('transfer-logs')->warning("PalmPay payout failed (" . ($transferReference['message'] ?? '') . "). Attempting Flutterwave fallback...");
                    if ($transferService->canUseFlutterwaveTransfer()) {
                        $transferReference = $transferService->initiateFlutterwaveTransfer(
                            $this->account['bank_code'],
                            $this->account['account_number'],
                            $this->account['account_name'],
                            $this->account['amount'],
                            $this->account['reference'],
                            (string) ($this->account['description'] ?? 'Settlement withdrawal')
                        );
                        $provider = 'flutterwave';
                    }
                }
            } elseif ($provider === 'flutterwave') {
                $transferReference = $transferService->initiateFlutterwaveTransfer(
                    $this->account['bank_code'],
                    $this->account['account_number'],
                    $this->account['account_name'],
                    $this->account['amount'],
                    $this->account['reference'],
                    (string) ($this->account['description'] ?? 'Settlement withdrawal')
                );
            } else {
                $transferReference = $transferService->initaiteDotTransfer(
                    $this->account['bank_code'],
                    $this->account['account_number'],
                    $this->account['account_name'],
                    $this->account['amount'],
                    $this->account['reference'],
                    $this->account['description'] ?? 'Settlement withdrawal',
                    $this->account['bank_name'],
                    $this->user
                );
            }

            Log::channel('transfer-logs')->info("Transfer reference response (" . $provider . "): " . json_encode($transferReference));

            if (($transferReference['state'] ?? null) === 'FAILED' || ($transferReference['status'] ?? null) === 'error') {
                throw new \Exception($transferReference['message'] ?? 'Transfer failed via ' . $provider);
            }

            $tranferRecord = Transfer::firstWhere('reference', $this->account['reference']);

            if (!$tranferRecord) {
                throw new \Exception("Transfer record not found for reference: {$this->account['reference']}");
            }

            $tranferRecord->update([
                'reference' => $transferReference['receiptNumber'] ?? $this->account['reference'],
                'status' => $transferReference['state'] ?? Transfer::STATUS_PENDING,
                'transfer_id' => (string) ($transferReference['requestId'] ?? ($transferReference['receiptNumber'] ?? $this->account['reference'])),
                'response_data' => array_merge($tranferRecord->response_data ?? [], [
                    'provider' => $provider,
                    'initiation_response' => $transferReference,
                ]),
            ]);
        } catch (\Exception $e) {
            $this->handleFailedTransfer($e);

            Log::channel('transfer-logs')->error("Transfer job failed: " . $e->getMessage());

            throw new \Exception("Transfer failed: " . $e->getMessage());
        }
    }

    private function handleFailedTransfer(\Exception $e)
    {
        DB::beginTransaction();

        $tranferRecord = Transfer::firstWhere('reference', $this->account['reference']);

        if ($tranferRecord) {
            $tranferRecord->update([
                'status' => Transfer::STATUS_FAILED
            ]);

            $user = $tranferRecord->user;
            $total = $this->account['amount'] + ($this->account['swiftpay_fee'] ?? 0);

            $user->increment('wallet_balance', $total);

            UserTransaction::create([
                'user_id' => $user->id,
                'type' => UserTransaction::TYPE_CREDIT,
                'amount' => $total,
                'source' => UserTransaction::SOURCE_BANK_TRANSFER,
                'status' => UserTransaction::STATUS_SUCCESSFUL,
                'message' => "Refunded N{$total} to your account due to failed transfer"
            ]);
        }

        DB::commit();
    }
}
