<?php

namespace App\Livewire\Transfer;

use App\Models\Transfer;
use App\Models\UserTransaction;
use App\Services\HashIdService;
use App\Services\TransferService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Receipt extends Component
{
    public $transfer;

    public function mount($hash_id)
    {
        $this->transfer = (new HashIdService)->getRecord($hash_id, Transfer::class);

        if ($this->transfer && $this->transfer->status === Transfer::STATUS_PENDING) {
            $this->checkAndRefreshStatus();
        }
    }

    public function checkAndRefreshStatus()
    {
        if (!$this->transfer || $this->transfer->status !== Transfer::STATUS_PENDING) {
            return;
        }

        $transferService = new TransferService();
        $provider = strtolower((string) data_get($this->transfer->response_data, 'provider', 'palmpay'));

        try {
            $res = null;
            if ($provider === 'palmpay') {
                $res = $transferService->checkPalmPayTransferStatus((string) $this->transfer->transfer_id, (string) $this->transfer->reference);
            } elseif ($provider === 'flutterwave') {
                $res = $transferService->checkFlutterwaveTransferStatus((string) $this->transfer->transfer_id, (string) $this->transfer->reference);
            } else {
                $res = $transferService->checkDotTransferStatus((string) $this->transfer->transfer_id, (string) $this->transfer->reference);
            }

            $newState = null;
            if ($provider === 'dot') {
                if (isset($res['status']) && $res['status'] === 'success') {
                    $newState = Transfer::STATUS_SUCCESS;
                } elseif (isset($res['status']) && $res['status'] === 'failed') {
                    $newState = Transfer::STATUS_FAILED;
                }
            } else {
                if (isset($res['state']) && in_array($res['state'], [Transfer::STATUS_SUCCESS, Transfer::STATUS_FAILED], true)) {
                    $newState = $res['state'];
                }
            }

            if ($newState) {
                DB::transaction(function () use ($newState, $res) {
                    $previousStatus = $this->transfer->status;
                    
                    $this->transfer->update([
                        'status' => $newState,
                        'last_status_check_at' => now(),
                        'response_data' => array_merge($this->transfer->response_data ?? [], [
                            'status_check' => $res,
                        ]),
                    ]);

                    $ut = UserTransaction::query()
                        ->where('user_id', $this->transfer->user_id)
                        ->where('reference', $this->transfer->reference)
                        ->where('source', UserTransaction::SOURCE_BANK_TRANSFER)
                        ->latest('id')
                        ->first();

                    if ($ut) {
                        $ut->update([
                            'status' => $newState === Transfer::STATUS_SUCCESS 
                                ? UserTransaction::STATUS_SUCCESSFUL 
                                : UserTransaction::STATUS_FAILED,
                        ]);
                    }

                    if ($newState === Transfer::STATUS_FAILED && $previousStatus !== Transfer::STATUS_FAILED) {
                        $totalRefund = $this->transfer->amount + $this->transfer->fee;
                        $this->transfer->user->increment('wallet_balance', $totalRefund);

                        UserTransaction::create([
                            'user_id' => $this->transfer->user_id,
                            'amount' => $totalRefund,
                            'type' => UserTransaction::TYPE_CREDIT,
                            'status' => UserTransaction::STATUS_SUCCESSFUL,
                            'source' => UserTransaction::SOURCE_BANK_TRANSFER,
                            'message' => "Refund of failed bank transfer to {$this->transfer->account_name} ({$this->transfer->account_number})",
                            'reference' => 'RF_' . $this->transfer->reference,
                        ]);
                    }
                });

                $this->transfer = $this->transfer->fresh();
            }
        } catch (\Throwable $e) {
            // silent fail
        }
    }

    public function render()
    {
        return view('livewire.transfer.receipt')->title('Bank Transfer Receipt');
    }
}
