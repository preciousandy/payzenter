<?php

namespace App\Jobs;

use App\Models\Transfer;
use App\Models\UserTransaction;
use App\Services\TransferService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class CheckTransferStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $transferService;

    /**
     * Create a new job instance.
     */
    public function __construct(public Transfer $transfer)
    {
        $this->transferService = new TransferService();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $provider = strtolower((string) data_get($this->transfer->response_data, 'provider', 'dot'));

        if ($provider === 'palmpay') {
            $response = $this->transferService->checkPalmPayTransferStatus($this->transfer->transfer_id, $this->transfer->reference);
        } elseif ($provider === 'flutterwave') {
            $response = $this->transferService->checkFlutterwaveTransferStatus($this->transfer->transfer_id, $this->transfer->reference);
        } else {
            $response = $this->transferService->checkDotTransferStatus($this->transfer->transfer_id, $this->transfer->reference);
        }

        if (($response['status'] ?? null) === 'success' && ($response['state'] ?? null) === Transfer::STATUS_SUCCESS) {
            $this->transfer->update([
                'status' => Transfer::STATUS_SUCCESS,
                'response_data' => array_merge($this->transfer->response_data ?? [], [
                    'provider' => $provider,
                    'status_check' => $response['data'] ?? $response['raw'] ?? null,
                ]),
            ]);

            // trigger any business logic: notify user, credit account, etc.
        } elseif (($response['status'] ?? null) === 'failed' || ($response['state'] ?? null) === Transfer::STATUS_FAILED) {
            $wasFailed = $this->transfer->status === Transfer::STATUS_FAILED;

            $this->transfer->update([
                'status' =>  Transfer::STATUS_FAILED,
                'response_data' => array_merge($this->transfer->response_data ?? [], [
                    'provider' => $provider,
                    'status_check' => $response['data'] ?? $response['raw'] ?? null,
                ]),
            ]);

            // handle refund logic or alert admin/user

            if (!$wasFailed) {
                $this->handleFailedTransfer($this->transfer->fresh());
            }
        } else {
            // still pending, optionally update last_checked_at
            $this->transfer->update([
                'last_status_check_at' => now(),
                'response_data' => array_merge($this->transfer->response_data ?? [], [
                    'provider' => $provider,
                    'status_check' => $response['data'] ?? $response['raw'] ?? null,
                ]),
            ]);
        }
    }

    private function handleFailedTransfer(Transfer $transfer): void
    {
        DB::beginTransaction();


        $transfer->update([
            'status' => Transfer::STATUS_FAILED
        ]);

        $user = $transfer->user;
        $total = $transfer->amount + $transfer->fee;

        $user->increment('wallet_balance', $total);

        UserTransaction::create([
            'user_id' => $user->id,
            'type' => UserTransaction::TYPE_CREDIT,
            'amount' => $total,
            'source' => UserTransaction::SOURCE_BANK_TRANSFER,
            'status' => UserTransaction::STATUS_SUCCESSFUL,
            'message' => "Refunded N{$total} to your account due to failed transfer"
        ]);

        DB::commit();
    }
}
