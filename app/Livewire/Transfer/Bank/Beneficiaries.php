<?php

namespace App\Livewire\Transfer\Bank;

use App\Models\Beneficiary;
use App\Models\Transfer;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Beneficiaries extends Component
{
    use LivewireAlert;

    public $recentTransfers = [];
    public $recentTransfersCopy = [];

    public $favorites = [];

    public $query = '';

    public $favoritesUsernames = [];

    public function mount()
    {
        $recentTransfers = Transfer::where('user_id', auth()->id())
            ->latest()
            ->get()
            ->unique('account_number');

        $this->recentTransfers = $recentTransfers->each(function ($transfer) {
            $transfer->is_beneficiary = Beneficiary::where('user_id', auth()->id())
                ->where('acct_number', $transfer->account_number)
                ->where('bank_code', $transfer->bank_code)
                ->exists();
        });


        $this->recentTransfersCopy = $this->recentTransfers;

        $this->favorites = Beneficiary::where('user_id', auth()->id())
            ->where('type', Beneficiary::TYPE_BANK)
            ->get();
    }

    public function toggleBeneficiary($transferId)
    {
        $transfer = Transfer::find($transferId);

        $beneficiary = Beneficiary::where('user_id', auth()->id())
            ->where('acct_number', $transfer->account_number)
            ->where('bank_code', $transfer->bank_code)
            ->first();

        if ($beneficiary) {
            $beneficiary->delete();
            return  $this->flash('success', 'Beneficiary removed successfully', [], route('transfer.bank.beneficiaries'));
        }

        Beneficiary::create([
            'user_id' => auth()->id(),
            'acct_number' => $transfer->account_number,
            'bank_code' => $transfer->bank_code,
            'type' => Beneficiary::TYPE_BANK,
            'acct_name' => $transfer->account_name,
            'bank_name' => $transfer->bank_name,
        ]);

        return $this->flash('success', 'Beneficiary added successfully', [], route('transfer.bank.beneficiaries'));
    }

    public function updatedQuery()
    {
        $this->recentTransfers = $this->recentTransfersCopy->filter(function ($transfer) {
            return strpos(strtolower($transfer->account_name), strtolower($this->query)) !== false;
        });
    }


    public function render()
    {
        return view('livewire.transfer.bank.beneficiaries')->title('Bank Beneficiaries');
    }
}
