<?php

namespace App\Livewire\Admin;

use App\Models\GatewayTransaction;
use App\Models\GatewayWebhookDelivery;
use App\Models\PaymentTransaction;
use App\Services\GatewayPaymentStatusService;
use App\Services\GatewayWebhookService;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class AllPaymentTransactions extends Component
{
    use WithPagination;
    use LivewireAlert;

    // Active tab: 'gateway' or 'wallet'
    public $activeTab = 'gateway';

    // Gateway Filters
    public $search = '';
    public $status = '';
    public $method = '';
    public $provider = '';
    public $from_date = '';
    public $to_date = '';

    // Wallet Payments Filters
    public $walletSearch = '';
    public $walletMethod = '';
    public $walletFromDate = '';
    public $walletToDate = '';

    // Webhook Inspection & Repush modal state
    public $showWebhookModal = false;
    public $selectedTxId = null;
    public $selectedTxData = null;
    public $selectedDeliveries = [];
    public $overrideWebhookUrl = '';
    public $overrideAccountNumber = '';
    public $overrideCustomerPhone = '';
    public $overrideCustomerEmail = '';
    public $overrideAmount = '';
    public $previewPayloadJson = '';

    protected $queryString = [
        'activeTab' => ['except' => 'gateway'],
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'method' => ['except' => ''],
        'provider' => ['except' => ''],
        'from_date' => ['except' => ''],
        'to_date' => ['except' => ''],
        'walletSearch' => ['except' => ''],
        'walletMethod' => ['except' => ''],
        'walletFromDate' => ['except' => ''],
        'walletToDate' => ['except' => ''],
    ];

    public function setTab($tab)
    {
        if (in_array($tab, ['gateway', 'wallet'], true)) {
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
        if ($this->activeTab === 'gateway') {
            $this->reset(['search', 'status', 'method', 'provider', 'from_date', 'to_date']);
        } else {
            $this->reset(['walletSearch', 'walletMethod', 'walletFromDate', 'walletToDate']);
        }
        $this->resetPage();
    }

    public function repushWebhook(int $transactionId)
    {
        $tx = GatewayTransaction::with('merchant')->find($transactionId);

        if (!$tx) {
            $this->alert('error', 'Transaction not found.');
            return;
        }

        $service = app(GatewayWebhookService::class);
        $result = $service->repushForTransaction($tx);

        if ($result['success']) {
            $this->alert('success', $result['message']);
        } elseif (!empty($result['delivery'])) {
            $this->alert('warning', $result['message']);
        } else {
            $this->alert('error', $result['message']);
        }

        if ($this->showWebhookModal && $this->selectedTxId === $transactionId) {
            $this->openWebhookModal($transactionId);
        }
    }

    public function checkLiveStatus(int $transactionId)
    {
        $tx = GatewayTransaction::with('merchant')->find($transactionId);

        if (!$tx) {
            $this->alert('error', 'Transaction not found.');
            return;
        }

        $service = app(GatewayPaymentStatusService::class);
        $result = $service->checkAndRefreshStatus($tx);

        if ($result['status'] === 'success') {
            $this->alert('success', $result['message']);
        } elseif ($result['status'] === 'failed') {
            $this->alert('error', $result['message']);
        } else {
            $this->alert('info', $result['message']);
        }

        if ($this->showWebhookModal && $this->selectedTxId === $transactionId) {
            $this->openWebhookModal($transactionId);
        }
    }

    public function syncProcessingTransactions()
    {
        $txs = GatewayTransaction::whereIn('status', ['processing', 'paying', 'pending', 'unpaid'])
            ->where('created_at', '>=', now()->subDays(3))
            ->latest('id')
            ->take(25)
            ->get();

        if ($txs->isEmpty()) {
            $this->alert('info', 'No active processing transactions to sync.');
            return;
        }

        $service = app(GatewayPaymentStatusService::class);
        $successCount = 0;
        $failedCount = 0;
        $stillProcessing = 0;

        foreach ($txs as $tx) {
            $res = $service->checkAndRefreshStatus($tx);
            if ($res['status'] === 'success') {
                $successCount++;
            } elseif ($res['status'] === 'failed') {
                $failedCount++;
            } else {
                $stillProcessing++;
            }
        }

        $msg = "Synced {$txs->count()} transactions: {$successCount} Succeeded, {$failedCount} Failed, {$stillProcessing} Processing.";
        $this->alert('success', $msg);
    }

    public function openWebhookModal(int $transactionId)
    {
        $this->selectedTxId = $transactionId;
        $tx = GatewayTransaction::with('merchant')->find($transactionId);

        if (!$tx) {
            $this->alert('error', 'Transaction not found.');
            return;
        }

        $merchant = $tx->merchant;
        $defaultUrl = (string) ($tx->merchant_webhook_url ?: ($merchant?->merchant_webhook_url ?? ''));
        $this->overrideWebhookUrl = $defaultUrl;

        // Build base payload to resolve account and customer fields accurately
        $service = app(GatewayWebhookService::class);
        $resolvedPayload = $service->buildTransactionPayload($tx);

        $this->overrideAccountNumber = (string) ($resolvedPayload['account_number'] ?? $tx->bank_transfer_account ?? '');
        $this->overrideCustomerPhone = (string) ($resolvedPayload['customer_phone'] ?? $tx->customer_phone ?? '');
        $this->overrideCustomerEmail = (string) ($resolvedPayload['customer_email'] ?? $tx->customer_email ?? '');
        $this->overrideAmount = (string) number_format(((float) ($tx->amount ?? 0)) / 100, 2, '.', '');

        $this->selectedTxData = [
            'id' => $tx->id,
            'reference' => $tx->reference,
            'amount' => $tx->amount,
            'fee' => $tx->fee,
            'currency' => $tx->currency,
            'status' => $tx->status,
            'method' => $tx->method,
            'provider' => $tx->provider,
            'provider_reference' => $tx->provider_reference,
            'customer_name' => $tx->customer_name,
            'customer_email' => $tx->customer_email,
            'customer_phone' => $tx->customer_phone,
            'bank_transfer_account' => $tx->bank_transfer_account,
            'bank_transfer_bank' => $tx->bank_transfer_bank,
            'merchant_id' => $tx->merchant_id,
            'merchant_name' => $merchant ? trim($merchant->first_name . ' ' . $merchant->last_name) : 'Merchant #' . $tx->merchant_id,
            'merchant_email' => $merchant?->email ?? '',
            'merchant_webhook_url' => $defaultUrl,
            'created_at' => optional($tx->created_at)->format('d M Y, H:i:s'),
            'paid_at' => optional($tx->paid_at)->format('d M Y, H:i:s'),
        ];

        $deliveries = GatewayWebhookDelivery::query()
            ->where(function ($q) use ($tx) {
                $q->where('gateway_transaction_id', $tx->id);
                if (!empty($tx->reference)) {
                    $q->orWhere('reference', $tx->reference);
                }
            })
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        $this->selectedDeliveries = $deliveries->map(function ($d) {
            return [
                'id' => $d->id,
                'url' => $d->url,
                'event' => $d->event,
                'attempts' => $d->attempts,
                'response_status' => $d->response_status,
                'response_body' => $d->response_body,
                'error_message' => $d->error_message,
                'payload' => $d->payload,
                'delivered_at' => optional($d->delivered_at)->format('d M Y, H:i:s'),
                'last_attempt_at' => optional($d->last_attempt_at)->format('d M Y, H:i:s'),
                'created_at' => optional($d->created_at)->format('d M Y, H:i:s'),
            ];
        })->toArray();

        $this->updatePayloadPreview();
        $this->showWebhookModal = true;
    }

    public function updatedOverrideAccountNumber()
    {
        $this->updatePayloadPreview();
    }

    public function updatedOverrideCustomerPhone()
    {
        $this->updatePayloadPreview();
    }

    public function updatedOverrideCustomerEmail()
    {
        $this->updatePayloadPreview();
    }

    public function updatedOverrideAmount()
    {
        $this->updatePayloadPreview();
    }

    public function updatePayloadPreview()
    {
        if (!$this->selectedTxId) {
            return;
        }

        $tx = GatewayTransaction::with('merchant')->find($this->selectedTxId);
        if (!$tx) {
            return;
        }

        $service = app(GatewayWebhookService::class);
        $payload = $service->buildTransactionPayload($tx, null, [
            'account_number' => trim($this->overrideAccountNumber),
            'customer_phone' => trim($this->overrideCustomerPhone),
            'customer_email' => trim($this->overrideCustomerEmail),
            'amount' => is_numeric($this->overrideAmount) ? (float) $this->overrideAmount : null,
        ]);

        $this->previewPayloadJson = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    public function setPresetUrl(string $preset)
    {
        if ($preset === 'vtuloaded_payzenter') {
            $this->overrideWebhookUrl = 'https://vtuloaded.com.ng/account/payzenter_webhook';
        } elseif ($preset === 'vtuloaded_process') {
            $this->overrideWebhookUrl = 'https://vtuloaded.com.ng/account/process_webhook';
        } elseif ($preset === 'vtuloaded_paymentpoint') {
            $this->overrideWebhookUrl = 'https://vtuloaded.com.ng/account/paymentpoint_webhook';
        } elseif ($preset === 'merchant_default' && $this->selectedTxId) {
            $tx = GatewayTransaction::with('merchant')->find($this->selectedTxId);
            $this->overrideWebhookUrl = (string) ($tx?->merchant_webhook_url ?: ($tx?->merchant?->merchant_webhook_url ?? ''));
        }
    }

    public function closeWebhookModal()
    {
        $this->showWebhookModal = false;
        $this->selectedTxId = null;
        $this->selectedTxData = null;
        $this->selectedDeliveries = [];
        $this->overrideWebhookUrl = '';
        $this->overrideAccountNumber = '';
        $this->overrideCustomerPhone = '';
        $this->overrideCustomerEmail = '';
        $this->overrideAmount = '';
        $this->previewPayloadJson = '';
    }

    public function repushFromModal()
    {
        if (!$this->selectedTxId) {
            return;
        }

        $tx = GatewayTransaction::with('merchant')->find($this->selectedTxId);
        if (!$tx) {
            $this->alert('error', 'Transaction not found.');
            return;
        }

        $url = trim($this->overrideWebhookUrl);
        if ($url === '' || !filter_var($url, FILTER_VALIDATE_URL)) {
            $this->alert('error', 'Please enter a valid webhook URL (must start with http:// or https://).');
            return;
        }

        $customOverrides = [
            'account_number' => trim($this->overrideAccountNumber),
            'customer_phone' => trim($this->overrideCustomerPhone),
            'customer_email' => trim($this->overrideCustomerEmail),
            'amount' => is_numeric($this->overrideAmount) ? (float) $this->overrideAmount : null,
        ];

        $service = app(GatewayWebhookService::class);
        $result = $service->repushForTransaction($tx, $url, $customOverrides);

        if ($result['success']) {
            $this->alert('success', $result['message']);
        } elseif (!empty($result['delivery'])) {
            $this->alert('warning', $result['message']);
        } else {
            $this->alert('error', $result['message']);
        }

        $this->openWebhookModal($this->selectedTxId);
    }

    public function repushDelivery(int $deliveryId)
    {
        $delivery = GatewayWebhookDelivery::find($deliveryId);
        if (!$delivery) {
            $this->alert('error', 'Webhook delivery record not found.');
            return;
        }

        $service = app(GatewayWebhookService::class);
        $delivery = $service->attempt($delivery);

        if ($delivery->delivered_at) {
            $this->alert('success', "Delivery #{$delivery->id} repushed successfully! Target returned HTTP {$delivery->response_status}.");
        } elseif ($delivery->response_status) {
            $this->alert('warning', "Delivery #{$delivery->id} repushed, but server returned HTTP {$delivery->response_status}. " . ($delivery->error_message ? "({$delivery->error_message})" : ''));
        } else {
            $this->alert('error', "Delivery #{$delivery->id} failed: " . ($delivery->error_message ?: 'Connection error or timeout.'));
        }

        if ($this->showWebhookModal && $this->selectedTxId) {
            $this->openWebhookModal($this->selectedTxId);
        }
    }

    protected function filteredGatewayQuery()
    {
        $query = GatewayTransaction::query()->with(['merchant', 'latestWebhookDelivery']);

        if ($this->search !== '') {
            $term = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('reference', 'like', $term)
                    ->orWhere('customer_email', 'like', $term)
                    ->orWhere('customer_name', 'like', $term)
                    ->orWhere('customer_phone', 'like', $term)
                    ->orWhere('provider_reference', 'like', $term)
                    ->orWhereHas('merchant', function ($m) use ($term) {
                        $m->where('first_name', 'like', $term)
                            ->orWhere('last_name', 'like', $term)
                            ->orWhere('email', 'like', $term);
                    });
            });
        }

        if ($this->status !== '') {
            if ($this->status === 'processing') {
                $query->whereIn('status', ['processing', 'paying', 'pending', 'unpaid']);
            } elseif ($this->status === 'failed') {
                $query->whereIn('status', ['failed', 'closed', 'canceled', 'cancelled', 'fail']);
            } elseif ($this->status === 'success') {
                $query->whereIn('status', ['success', 'successful']);
            } else {
                $query->where('status', $this->status);
            }
        }

        if ($this->method !== '') {
            $query->where('method', $this->method);
        }

        if ($this->provider !== '') {
            $query->where('provider', $this->provider);
        }

        if ($this->from_date !== '') {
            $query->whereDate('created_at', '>=', $this->from_date);
        }

        if ($this->to_date !== '') {
            $query->whereDate('created_at', '<=', $this->to_date);
        }

        return $query;
    }

    protected function filteredWalletQuery()
    {
        $query = PaymentTransaction::query()->with('user');

        if ($this->walletSearch !== '') {
            $term = '%' . trim($this->walletSearch) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('transaction_reference', 'like', $term)
                    ->orWhere('transaction_id', 'like', $term)
                    ->orWhere('payment_method', 'like', $term)
                    ->orWhereHas('user', function ($u) use ($term) {
                        $u->where('first_name', 'like', $term)
                            ->orWhere('last_name', 'like', $term)
                            ->orWhere('email', 'like', $term);
                    });
            });
        }

        if ($this->walletMethod !== '') {
            $query->where('payment_method', $this->walletMethod);
        }

        if ($this->walletFromDate !== '') {
            $query->whereDate('created_at', '>=', $this->walletFromDate);
        }

        if ($this->walletToDate !== '') {
            $query->whereDate('created_at', '<=', $this->walletToDate);
        }

        return $query;
    }

    public function render()
    {
        $gatewayBase = $this->filteredGatewayQuery();
        $walletBase = $this->filteredWalletQuery();

        // Overall counts for tabs badge
        $gatewayTotalCount = GatewayTransaction::count();
        $walletTotalCount = PaymentTransaction::count();

        // Gateway stats
        $gatewayStats = [
            'count' => (clone $gatewayBase)->count(),
            'successCount' => (clone $gatewayBase)->whereIn('status', ['success', 'successful'])->count(),
            'processingCount' => (clone $gatewayBase)->whereIn('status', ['processing', 'paying', 'pending', 'unpaid'])->count(),
            'failedCount' => (clone $gatewayBase)->whereIn('status', ['failed', 'closed', 'canceled', 'cancelled'])->count(),
            'volume' => ((float) (clone $gatewayBase)->whereIn('status', ['success', 'successful'])->sum('amount')) / 100,
            'fees' => ((float) (clone $gatewayBase)->whereIn('status', ['success', 'successful'])->sum('fee')) / 100,
        ];

        // Wallet stats
        $walletStats = [
            'count' => (clone $walletBase)->count(),
            'volume' => (float) (clone $walletBase)->sum('amount'),
            'fees' => (float) (clone $walletBase)->sum('charge_fee'),
            'uniqueUsers' => (clone $walletBase)->distinct('user_id')->count('user_id'),
        ];

        $gatewayTransactions = $this->activeTab === 'gateway'
            ? $gatewayBase->orderByDesc('id')->paginate(15)
            : null;

        $walletTransactions = $this->activeTab === 'wallet'
            ? $walletBase->orderByDesc('id')->paginate(15)
            : null;

        return view('livewire.admin.all-payment-transactions', [
            'gatewayTransactions' => $gatewayTransactions,
            'walletTransactions' => $walletTransactions,
            'gatewayStats' => $gatewayStats,
            'walletStats' => $walletStats,
            'gatewayTotalCount' => $gatewayTotalCount,
            'walletTotalCount' => $walletTotalCount,
        ])->extends('layouts.admin');
    }
}
