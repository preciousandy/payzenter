<?php

namespace App\Livewire;

use App\Models\UserTransaction;
use Livewire\Attributes\Url;
use Livewire\Component;

class TransactionHistory extends Component
{
    public $perPage = 20;

    #[Url]
    public $source = '';

    #[Url]
    public $order = '';

    #[Url]
    public $amount = '';

    public $sources = [
        'wallet_funded',
        'airtime',
        'data',
        'cable',
        'electricity',
        'foreign_to_local_transfer',
        'local_to_foreign_transfer',
        'holdings',
        'investments',
        'bureau_de_change',
        'crypto',
        'gift_card',
        'bank_transfer',
        'swiftpay_transfer',
        'save_with_interest',
        'group_savings',
        'ajo_savings',
        'ajo_contribution',
        'international_transfer',
        'multiple_bank_transfer',
        'multiple_swiftpay_transfer',
    ];

    public $showFilterBySourceModal = false;
    public $showSortModal = false;


    public function loadMore()
    {
        $this->perPage += 10;
    }


    public function filterBySource($source)
    {
        $this->source  = $source;

        $this->showFilterBySourceModal = false;
    }

    public function sortByDate($value)
    {

        $this->amount  = '';

        $this->order = $value;

        $this->showSortModal = false;
    }

    public function sortByAmount($value)
    {
        $this->order = '';

        $this->amount = $value;

        $this->showSortModal = false;
    }

    public function render()
    {
        $transactions = UserTransaction::query()
            ->where('user_id', auth()->id())
            ->when($this->source, function ($query) {
                return $query->where('source', $this->source);
            })
            ->when($this->amount, function ($query) {
                return $query->orderBy('amount', $this->amount);
            })
            ->when($this->order, function ($query) {
                return $query->orderBy('created_at', $this->order);
            }, function ($query) {
                return $query->latest();
            })
            ->paginate($this->perPage);

        return view('livewire.transaction-history', [
            'transactions' => $transactions
        ])->title('Transaction History');
    }
}
