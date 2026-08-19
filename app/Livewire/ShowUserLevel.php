<?php

namespace App\Livewire;

use Livewire\Component;

class ShowUserLevel extends Component
{
    public $levels = [
        "green" => [
            "min" => 0,
            "max" => 100000
        ],
        "blue" => [
            "min" => 100000,
            "max" => 1000000
        ],
        "black" => [
            "min" => 1000000,
            "max" => 10000000
        ],
        "gold" => [
            "min" => 10000000,
            "max" => null
        ]
    ];

    public $current_level;
    public $today_transactions_total = 0;

    public $amount_remaining;
    public $percentage_remaining;

    public function mount()
    {
        $this->current_level = auth()->user()->level;

        $this->today_transactions_total = auth()->user()->transactions()
            ->whereDate('created_at', now())
            ->sum('amount');

        $this->getAmountAndPercentageRemaining();
    }

    public function getAmountAndPercentageRemaining()
    {
        $current_level = $this->current_level;
        $today_transactions_total = $this->today_transactions_total;

        $min = $this->levels[$current_level]['min'];
        $max = $this->levels[$current_level]['max'];

        $amount_remaining = $max - $today_transactions_total;
        $percentage_remaining = $max > 0 ? ($today_transactions_total / $max) * 100 : 0;

        $this->amount_remaining = $amount_remaining;
        $this->percentage_remaining = $percentage_remaining;
    }

    public function closeModal()
    {
        auth()->user()->update([
            'if_level_changed' => false
        ]);

        return redirect()->route('home');
    }


    public function render()
    {
        return view('livewire.show-user-level', [
            'user' => auth()->user(),
        ]);
    }
}
