<?php

namespace App\Livewire;

use App\Models\Transfer;
use App\Models\UserTransaction;
use App\Services\HashIdService;
use App\Services\TransferService;
use Livewire\Component;

class TransactionReceipt extends Component
{
    public UserTransaction $transaction;

    public function mount($hash_id)
    {
        $this->transaction = (new HashIdService)->getRecord($hash_id, UserTransaction::class);

        if ($this->transaction->source !== UserTransaction::SOURCE_BANK_TRANSFER) {
            return;
        }

        $transfer = null;

        if (is_string($this->transaction->reference) && trim($this->transaction->reference) !== '') {
            $transfer = Transfer::query()
                ->where('user_id', $this->transaction->user_id)
                ->where('reference', $this->transaction->reference)
                ->latest('id')
                ->first();
        }

        if (!$transfer) {
            $fee = (float) ($this->transaction->fee ?? 0);
            $transferAmount = (float) ($this->transaction->amount ?? 0) - $fee;

            $transfer = Transfer::query()
                ->where('user_id', $this->transaction->user_id)
                ->where('type', Transfer::TYPE_BANK)
                ->where('amount', $transferAmount)
                ->where('fee', $fee)
                ->whereBetween('created_at', [
                    $this->transaction->created_at?->copy()->subMinutes(10) ?? now()->subMinutes(10),
                    $this->transaction->created_at?->copy()->addMinutes(10) ?? now()->addMinutes(10),
                ])
                ->latest('id')
                ->first();
        }

        if (!$transfer) {
            return;
        }

        if ($transfer->status === Transfer::STATUS_PENDING && ($transfer->transfer_id || $transfer->reference)) {
            $transferService = new TransferService();
            $provider = strtolower((string) data_get($transfer->response_data, 'provider', 'flutterwave'));

            if ($provider === 'palmpay') {
                $statusRes = $transferService->checkPalmPayTransferStatus((string) $transfer->transfer_id, (string) $transfer->reference);
            } elseif ($provider === 'dot') {
                $statusRes = $transferService->checkDotTransferStatus((string) $transfer->transfer_id, (string) $transfer->reference);
            } else {
                $statusRes = $transferService->checkFlutterwaveTransferStatus((string) $transfer->transfer_id, (string) $transfer->reference);
            }

            if (in_array(($statusRes['status'] ?? null), ['success', 'failed'], true) && isset($statusRes['state']) && $statusRes['state'] !== $transfer->status) {
                $transfer->update([
                    'status' => $statusRes['state'],
                    'last_status_check_at' => now(),
                    'response_data' => array_merge($transfer->response_data ?? [], [
                        'provider' => $provider,
                        'status_check' => $statusRes['data'] ?? $statusRes['raw'] ?? null,
                    ]),
                ]);
                $transfer->refresh();
            }
        }

        $mappedStatus = match ($transfer->status) {
            Transfer::STATUS_SUCCESS => UserTransaction::STATUS_SUCCESSFUL,
            Transfer::STATUS_FAILED => UserTransaction::STATUS_FAILED,
            default => UserTransaction::STATUS_PENDING,
        };

        if ($this->transaction->status !== $mappedStatus) {
            $this->transaction->update(['status' => $mappedStatus]);
            $this->transaction->refresh();
        }
    }

    public function render()
    {
        return view('livewire.transaction-receipt')->title('Transaction Receipt');
    }
}
