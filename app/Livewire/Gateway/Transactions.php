<?php

namespace App\Livewire\Gateway;

use App\Models\GatewayTransaction;
use Livewire\Component;
use Livewire\WithPagination;

class Transactions extends Component
{
    use WithPagination;

    public $status = '';
    public $method = '';
    public $provider = '';
    public $from_date = '';
    public $to_date = '';

    protected $queryString = [
        'status' => ['except' => ''],
        'method' => ['except' => ''],
        'provider' => ['except' => ''],
        'from_date' => ['except' => ''],
        'to_date' => ['except' => ''],
    ];

    public function updating($name, $value)
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['status', 'method', 'provider', 'from_date', 'to_date']);
        $this->resetPage();
    }

    public function render()
    {
        $enableFlutterwave = \App\Helpers\SettingHelper::isFlutterwaveEnabled();
        $enablePalmPay = \App\Helpers\SettingHelper::isPalmPayEnabled();
        $enableUdara360 = \App\Helpers\SettingHelper::isUdara360Enabled();

        if ($this->provider === 'flutterwave' && !$enableFlutterwave) {
            $this->provider = '';
        }
        if ($this->provider === 'udara360' && !$enableUdara360) {
            $this->provider = '';
        }
        if ($this->provider === 'palmpay' && !$enablePalmPay) {
            $this->provider = '';
        }

        $query = GatewayTransaction::query()
            ->where('merchant_id', auth()->id())
            ->orderByDesc('id');

        if ($this->status !== '') {
            $query->where('status', $this->status);
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

        $transactions = $query->paginate(15);

        return view('livewire.gateway.transactions', [
            'transactions' => $transactions,
            'enableFlutterwave' => $enableFlutterwave,
            'enablePalmPay' => $enablePalmPay,
            'enableUdara360' => $enableUdara360,
        ])->title('Transactions');
    }
}

