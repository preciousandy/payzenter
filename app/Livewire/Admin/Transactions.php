<?php

namespace App\Livewire\Admin;

use App\Models\Transfer;
use App\Models\UserTransaction;
use App\Services\TransferService;
use Illuminate\Database\Eloquent\Builder;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class Transactions extends Component
{
    use WithPagination;
    use LivewireAlert;

    // Active Tab: 'transfers' (Bank Transfers & Withdrawals) or 'debits' (All Outgoing Debits)
    public $activeTab = 'transfers';

    // Bank Transfers Filters
    public $search = '';
    public $status = '';
    public $provider = '';
    public $from_date = '';
    public $to_date = '';

    // Outgoing Debits Filters
    public $debitSearch = '';
    public $debitSource = '';
    public $debitStatus = '';
    public $debitFromDate = '';
    public $debitToDate = '';

    // Modal state for viewing transfer details
    public $showDetailModal = false;
    public $selectedTransfer = null;

    protected $queryString = [
        'activeTab' => ['except' => 'transfers'],
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'provider' => ['except' => ''],
        'from_date' => ['except' => ''],
        'to_date' => ['except' => ''],
        'debitSearch' => ['except' => ''],
        'debitSource' => ['except' => ''],
        'debitStatus' => ['except' => ''],
        'debitFromDate' => ['except' => ''],
        'debitToDate' => ['except' => ''],
    ];

    public function setTab($tab)
    {
        if (in_array($tab, ['transfers', 'debits'], true)) {
            $this->activeTab = $tab;
            $this->resetPage();
        }
    }

    public function updating($name, $value)
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        if ($this->activeTab === 'transfers') {
            $this->reset(['search', 'status', 'provider', 'from_date', 'to_date']);
        } else {
            $this->reset(['debitSearch', 'debitSource', 'debitStatus', 'debitFromDate', 'debitToDate']);
        }
        $this->resetPage();
    }

    public function openDetailModal(int $transferId)
    {
        $tx = Transfer::with(['user.merchantKyb'])->find($transferId);
        if (!$tx) {
            $this->alert('error', 'Transfer record not found.');
            return;
        }

        $provider = strtolower((string) data_get($tx->response_data, 'provider', ''));
        if ($provider === '') {
            if (str_starts_with((string) $tx->reference, 'flw_')) {
                $provider = 'flutterwave';
            } elseif (str_starts_with((string) $tx->reference, 'plm_')) {
                $provider = 'palmpay';
            } elseif (str_starts_with((string) $tx->reference, 'dot_')) {
                $provider = 'dot';
            }
        }

        $merchant = $tx->user;
        $kyb = $merchant?->merchantKyb;

        $this->selectedTransfer = [
            'id' => $tx->id,
            'reference' => $tx->reference,
            'transfer_id' => $tx->transfer_id,
            'amount' => (float) $tx->amount,
            'fee' => (float) $tx->fee,
            'status' => $tx->status,
            'description' => $tx->description,
            'type' => $tx->type,
            'bank_name' => $tx->bank_name,
            'bank_code' => $tx->bank_code,
            'account_name' => $tx->account_name,
            'account_number' => $tx->account_number,
            'provider' => $provider !== '' ? $provider : 'N/A',
            'failure_reason' => $tx->getFailureReason(),
            'merchant_id' => $merchant?->id,
            'merchant_name' => $merchant ? trim($merchant->first_name . ' ' . $merchant->last_name) : 'N/A',
            'business_name' => $kyb?->business_name ?: ($merchant?->business_name ?? null),
            'merchant_email' => $merchant?->email ?? '',
            'merchant_phone' => $merchant?->phone ?? '',
            'last_status_check_at' => optional($tx->last_status_check_at)->format('d M Y, H:i:s'),
            'created_at' => optional($tx->created_at)->format('d M Y, H:i:s'),
            'updated_at' => optional($tx->updated_at)->format('d M Y, H:i:s'),
            'response_data' => $tx->response_data,
        ];

        $this->showDetailModal = true;
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedTransfer = null;
    }

    public function checkStatus(int $transferId)
    {
        $tx = Transfer::find($transferId);
        if (!$tx) {
            $this->alert('error', 'Transfer record not found.');
            return;
        }

        $provider = strtolower((string) data_get($tx->response_data, 'provider', ''));
        if ($provider === '') {
            if (str_starts_with((string) $tx->reference, 'flw_')) {
                $provider = 'flutterwave';
            } elseif (str_starts_with((string) $tx->reference, 'plm_')) {
                $provider = 'palmpay';
            } elseif (str_starts_with((string) $tx->reference, 'dot_')) {
                $provider = 'dot';
            } else {
                $provider = 'flutterwave';
            }
        }

        $transferService = app(TransferService::class);
        $res = null;

        try {
            if ($provider === 'palmpay') {
                $res = $transferService->checkPalmPayTransferStatus((string) $tx->transfer_id, (string) $tx->reference);
            } elseif ($provider === 'dot') {
                $res = $transferService->checkDotTransferStatus((string) $tx->transfer_id, (string) $tx->reference);
            } else {
                $res = $transferService->checkFlutterwaveTransferStatus((string) $tx->transfer_id, (string) $tx->reference);
            }

            $newState = $res['state'] ?? null;
            if ($newState && in_array($newState, [Transfer::STATUS_SUCCESS, Transfer::STATUS_FAILED, Transfer::STATUS_PENDING], true)) {
                $oldStatus = $tx->status;
                $tx->update([
                    'status' => $newState,
                    'last_status_check_at' => now(),
                    'response_data' => array_merge($tx->response_data ?? [], [
                        'status_check' => $res,
                    ]),
                ]);

                $this->alert('success', "Transfer status queried from {$provider}: status is {$newState}.");
            } else {
                $msg = $res['message'] ?? 'Could not determine latest status from provider.';
                $this->alert('info', "Provider response: {$msg}");
            }
        } catch (\Throwable $e) {
            $this->alert('error', 'Error querying transfer status: ' . $e->getMessage());
        }

        if ($this->showDetailModal && optional($this->selectedTransfer)['id'] === $transferId) {
            $this->openDetailModal($transferId);
        }
    }

    protected function filteredTransfersQuery(): Builder
    {
        $query = Transfer::query()->with(['user.merchantKyb']);

        if ($this->search !== '') {
            $term = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('reference', 'like', $term)
                    ->orWhere('transfer_id', 'like', $term)
                    ->orWhere('account_name', 'like', $term)
                    ->orWhere('account_number', 'like', $term)
                    ->orWhere('bank_name', 'like', $term)
                    ->orWhere('description', 'like', $term)
                    ->orWhereHas('user', function ($u) use ($term) {
                        $u->where('first_name', 'like', $term)
                            ->orWhere('last_name', 'like', $term)
                            ->orWhere('email', 'like', $term)
                            ->orWhere('phone', 'like', $term)
                            ->orWhereHas('merchantKyb', function ($k) use ($term) {
                                $k->where('business_name', 'like', $term);
                            });
                    });
            });
        }

        if ($this->status !== '') {
            if ($this->status === 'success') {
                $query->whereIn('status', ['success', 'successful', 'completed', 'SUCCEEDED']);
            } elseif ($this->status === 'failed') {
                $query->whereIn('status', ['failed', 'FAILED']);
            } elseif ($this->status === 'pending') {
                $query->whereIn('status', ['pending', 'RUNNING', 'NEW', 'processing']);
            } else {
                $query->where('status', $this->status);
            }
        }

        if ($this->provider !== '') {
            $prov = strtolower(trim($this->provider));
            $query->where(function ($q) use ($prov) {
                $q->where('response_data->provider', $prov)
                    ->orWhere('reference', 'like', $prov . '%');
            });
        }

        if ($this->from_date !== '') {
            $query->whereDate('created_at', '>=', $this->from_date);
        }

        if ($this->to_date !== '') {
            $query->whereDate('created_at', '<=', $this->to_date);
        }

        return $query;
    }

    protected function filteredDebitsQuery(): Builder
    {
        $query = UserTransaction::query()->where('type', UserTransaction::TYPE_DEBIT)->with('user');

        if ($this->debitSearch !== '') {
            $term = '%' . trim($this->debitSearch) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('reference', 'like', $term)
                    ->orWhere('message', 'like', $term)
                    ->orWhere('source', 'like', $term)
                    ->orWhereHas('user', function ($u) use ($term) {
                        $u->where('first_name', 'like', $term)
                            ->orWhere('last_name', 'like', $term)
                            ->orWhere('email', 'like', $term);
                    });
            });
        }

        if ($this->debitSource !== '') {
            $query->where('source', $this->debitSource);
        }

        if ($this->debitStatus !== '') {
            $query->where('status', $this->debitStatus);
        }

        if ($this->debitFromDate !== '') {
            $query->whereDate('created_at', '>=', $this->debitFromDate);
        }

        if ($this->debitToDate !== '') {
            $query->whereDate('created_at', '<=', $this->debitToDate);
        }

        return $query;
    }

    public function render()
    {
        $transfersBase = $this->filteredTransfersQuery();
        $debitsBase = $this->filteredDebitsQuery();

        // Overall badge counts
        $transfersTotalCount = Transfer::count();
        $debitsTotalCount = UserTransaction::where('type', UserTransaction::TYPE_DEBIT)->count();

        // Transfer statistics
        $successStates = ['success', 'successful', 'completed', 'SUCCEEDED'];
        $pendingStates = ['pending', 'RUNNING', 'NEW', 'processing'];
        $failedStates = ['failed', 'FAILED'];

        $transferStats = [
            'count' => (clone $transfersBase)->count(),
            'totalVolume' => (float) (clone $transfersBase)->whereIn('status', $successStates)->sum('amount'),
            'totalPendingVolume' => (float) (clone $transfersBase)->whereIn('status', $pendingStates)->sum('amount'),
            'successCount' => (clone $transfersBase)->whereIn('status', $successStates)->count(),
            'pendingCount' => (clone $transfersBase)->whereIn('status', $pendingStates)->count(),
            'failedCount' => (clone $transfersBase)->whereIn('status', $failedStates)->count(),
            'totalFee' => (float) (clone $transfersBase)->whereIn('status', $successStates)->sum('fee'),
        ];

        // Debits statistics
        $debitStats = [
            'count' => (clone $debitsBase)->count(),
            'totalVolume' => (float) (clone $debitsBase)->sum('amount'),
            'successCount' => (clone $debitsBase)->where('status', UserTransaction::STATUS_SUCCESSFUL)->count(),
            'pendingCount' => (clone $debitsBase)->where('status', UserTransaction::STATUS_PENDING)->count(),
        ];

        $transfers = $this->activeTab === 'transfers'
            ? $transfersBase->orderByDesc('id')->paginate(15)
            : null;

        $debits = $this->activeTab === 'debits'
            ? $debitsBase->orderByDesc('id')->paginate(15)
            : null;

        return view('livewire.admin.transactions', [
            'transfers' => $transfers,
            'debits' => $debits,
            'transferStats' => $transferStats,
            'debitStats' => $debitStats,
            'transfersTotalCount' => $transfersTotalCount,
            'debitsTotalCount' => $debitsTotalCount,
        ])->extends('layouts.admin');
    }
}
