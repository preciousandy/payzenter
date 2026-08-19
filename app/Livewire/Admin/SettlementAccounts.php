<?php

namespace App\Livewire\Admin;

use App\Models\MerchantKyb;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class SettlementAccounts extends Component
{
    use LivewireAlert;
    use WithPagination;

    public $search = '';
    public $status_filter = 'all';

    // Edit modal properties
    public $editingKybId = null;
    public $edit_bank_name = '';
    public $edit_bank_id = '';
    public $edit_account_number = '';
    public $edit_account_name = '';
    public $edit_is_verified = false;

    // Banks list
    public $banks = [];

    // Delete modal properties
    public $deletingKybId = null;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->banks = cache()->remember('banks', 60 * 60 * 24 * 2, function () {
            return getDotBanks();
        });

        // Ensure OPay, PalmPay, Moniepoint MFB, and Kuda Bank are always available in the list
        $fintechs = [
            ['code' => '999032', 'name' => 'OPay (OPay Digital Services Limited)'],
            ['code' => '100033', 'name' => 'PalmPay (PalmPay Limited)'],
            ['code' => '090551', 'name' => 'Moniepoint Microfinance Bank'],
            ['code' => '090267', 'name' => 'Kuda Bank (Kuda Microfinance Bank)'],
        ];

        $allBanks = array_merge($fintechs, $this->banks ?? []);
        $uniqueBanks = [];
        $seenCodes = [];
        foreach ($allBanks as $b) {
            if ($b && !empty($b['code']) && !in_array($b['code'], $seenCodes, true)) {
                $seenCodes[] = $b['code'];
                $uniqueBanks[] = $b;
            }
        }

        // Sort alphabetically by bank name
        usort($uniqueBanks, function ($a, $b) {
            return strcasecmp($a['name'], $b['name']);
        });

        $this->banks = $uniqueBanks;
    }

    public function updatedEditBankId($code)
    {
        $selectedBank = collect($this->banks)->firstWhere('code', $code);
        if ($selectedBank) {
            $this->edit_bank_name = $selectedBank['name'];
        } else {
            $this->edit_bank_name = '';
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function openEditModal($id)
    {
        $kyb = MerchantKyb::with('user')->find($id);
        if (!$kyb) {
            $this->alert('error', 'Settlement record not found.');
            return;
        }

        $this->editingKybId = $kyb->id;
        $this->edit_bank_name = $kyb->settlement_bank_name ?? '';
        $this->edit_bank_id = $kyb->settlement_bank_id ?? '';
        $this->edit_account_number = $kyb->settlement_account_number ?? '';
        $this->edit_account_name = $kyb->settlement_account_name ?? '';
        $this->edit_is_verified = (bool) $kyb->settlement_verified_at;
    }

    public function closeEditModal()
    {
        $this->editingKybId = null;
        $this->reset(['edit_bank_name', 'edit_bank_id', 'edit_account_number', 'edit_account_name', 'edit_is_verified']);
    }

    public function saveEdit()
    {
        $this->validate([
            'edit_bank_name' => 'required|string|max:150',
            'edit_bank_id' => 'required|string|max:50',
            'edit_account_number' => 'required|numeric|digits:10',
            'edit_account_name' => 'required|string|max:255',
        ]);

        if (!$this->editingKybId) {
            return;
        }

        $kyb = MerchantKyb::find($this->editingKybId);
        if (!$kyb) {
            $this->alert('error', 'Settlement account not found.');
            return;
        }

        $kyb->update([
            'settlement_bank_name' => $this->edit_bank_name,
            'settlement_bank_id' => $this->edit_bank_id,
            'settlement_account_number' => $this->edit_account_number,
            'settlement_account_name' => $this->edit_account_name,
            'settlement_verified_at' => $this->edit_is_verified ? ($kyb->settlement_verified_at ?? now()) : null,
        ]);

        $this->closeEditModal();
        $this->alert('success', 'Settlement account updated successfully.');
    }

    public function toggleSuspend($id)
    {
        $kyb = MerchantKyb::find($id);
        if (!$kyb) {
            $this->alert('error', 'Settlement account not found.');
            return;
        }

        $newStatus = !$kyb->is_settlement_suspended;
        $kyb->update([
            'is_settlement_suspended' => $newStatus,
        ]);

        $statusLabel = $newStatus ? 'suspended' : 'unsuspended';
        $this->alert('success', "Settlement account has been {$statusLabel}.");
    }

    public function confirmDelete($id)
    {
        $this->deletingKybId = $id;
    }

    public function closeDeleteModal()
    {
        $this->deletingKybId = null;
    }

    public function deleteSettlementAccount()
    {
        if (!$this->deletingKybId) {
            return;
        }

        $kyb = MerchantKyb::find($this->deletingKybId);
        if ($kyb) {
            $kyb->update([
                'settlement_bank_id' => null,
                'settlement_bank_name' => null,
                'settlement_account_number' => null,
                'settlement_account_name' => null,
                'settlement_verified_at' => null,
                'is_settlement_suspended' => false,
            ]);
        }

        $this->closeDeleteModal();
        $this->alert('success', 'Settlement account details deleted/cleared.');
    }

    public function render()
    {
        $query = MerchantKyb::query()
            ->with('user')
            ->where(function ($q) {
                $q->whereNotNull('settlement_account_number')
                  ->orWhereNotNull('settlement_bank_name')
                  ->orWhereNotNull('settlement_account_name');
            });

        if (!empty($this->search)) {
            $s = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($s) {
                $q->where('settlement_account_number', 'like', $s)
                  ->orWhere('settlement_account_name', 'like', $s)
                  ->orWhere('settlement_bank_name', 'like', $s)
                  ->orWhere('business_name', 'like', $s)
                  ->orWhereHas('user', function ($uq) use ($s) {
                      $uq->where('name', 'like', $s)
                        ->orWhere('email', 'like', $s);
                  });
            });
        }

        if ($this->status_filter === 'suspended') {
            $query->where('is_settlement_suspended', true);
        } elseif ($this->status_filter === 'active') {
            $query->where('is_settlement_suspended', false);
        } elseif ($this->status_filter === 'verified') {
            $query->whereNotNull('settlement_verified_at');
        } elseif ($this->status_filter === 'unverified') {
            $query->whereNull('settlement_verified_at');
        }

        $accounts = $query->orderByDesc('id')->paginate(15);

        return view('livewire.admin.settlement-accounts', [
            'accounts' => $accounts,
        ])->extends('layouts.admin')->title('Manage Settlement Accounts');
    }
}
