<?php

namespace App\Livewire\Beneficiaries;

use App\Models\Beneficiary;
use App\Models\User;
use Livewire\Component;

class Index extends Component
{
    public $query = '';
    public function render()
    {
        return view('livewire.beneficiaries.index', [
            'beneficiaries' => Beneficiary::where('user_id', auth()->id())
                ->where(function ($query) {
                    $query->where('acct_name', 'like', '%' . $this->query . '%')
                        ->orWhere('acct_number', 'like', '%' . $this->query . '%');
                })
                ->latest()
                ->get(),
        ]);
    }

    public function delete($beneficiaryID)
    {
        $beneficiary = Beneficiary::findOrfail($beneficiaryID);

        $beneficiary->delete();

        return redirect()->route('beneficiaries.index')->with('success', 'Beneficiary deleted successfully');
    }

    public function transferMoney($beneficiaryID)
    {
        $beneficiary = Beneficiary::findOrfail($beneficiaryID);

        if ($beneficiary->type == Beneficiary::TYPE_SWIFTPAY) {
            return redirect()->route('beneficiaries.index')->with('error', 'Internal transfers have been disabled');
        }

        if ($beneficiary->type == Beneficiary::TYPE_BANK) {
            return redirect()->route('transfer.bank', [
                'beneficiary' => $beneficiary->hash_id,
            ]);
        }

        return redirect()->route('transfer.index', $beneficiary->hash_id);
    }
}
