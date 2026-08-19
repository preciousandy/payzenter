<?php

namespace App\Livewire;

use App\Models\DomiciliaryBank;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class DeleteDomiciliaryBank extends Component
{
    use LivewireAlert;

    public $bank;

    public function deleteBank($bankId)
    {
        $bank = DomiciliaryBank::findOrFail($bankId);

        $bank->delete();

        return $this->flash('success', 'Bank deleted successfully!', [], route('admin.exchange-rates'));
    }

    public function render()
    {
        return view('livewire.delete-domiciliary-bank');
    }
}
