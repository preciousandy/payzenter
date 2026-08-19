<?php

namespace App\Livewire\CablePackage;

use App\Models\CablePackage;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Edit extends Component
{
    use LivewireAlert;

    public $package;
    public $amount;
    public $price;

    public function mount($id)
    {
        $package = CablePackage::findOrFail($id);
        $this->package = $package;
        $this->amount = $package->amount;
        $this->price = $package->price;
    }

    public function render()
    {
        return view('livewire.cable-package.edit');
    }

    public function update()
    {
        $this->validate([
            'amount' => 'required|integer',
            'price' => 'required|integer',
        ]);

        $this->package->update([
            'amount' => $this->amount,
            'price' => $this->price,
        ]);

        return $this->flash(
            'success',
            'Cable package updated successfully',
            [],
            route('admin.cable-packages')
        );
    }
}
