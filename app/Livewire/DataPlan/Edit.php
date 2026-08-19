<?php

namespace App\Livewire\DataPlan;

use App\Models\DataPlan;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Edit extends Component
{
    use LivewireAlert;

    public $dataPlan;
    public $amount;
    public $price;
    public $code;

    public function mount($id)
    {
        $this->dataPlan = DataPlan::findOrFail($id);
        $this->amount = $this->dataPlan->amount;
        $this->price = $this->dataPlan->price;

        $this->code = $this->dataPlan->item_code;
    }


    public function render()
    {
        return view('livewire.data-plan.edit');
    }


    public function update()
    {
        $this->validate([
            'amount' => 'required|integer',
            'price' => 'required|integer',
        ]);

        $this->dataPlan->update([
            'amount' => $this->amount,
            'price' => $this->price,
            'item_code' => $this->code,
        ]);

        return $this->flash(
            'success',
            'Data plan updated successfully',
            [],
            route('admin.data-plans')
        );
    }
}
