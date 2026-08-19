<?php

namespace App\Livewire\AjoContributions;

use App\Models\AjoContribution;
use Livewire\Component;

class History extends Component
{
    public $contributions = [];

    public function mount()
    {
        $this->contributions = auth()->user()->ajoContributions()->where('status', AjoContribution::STATUS_CLOSED)->latest()->get();
    }

    public function render()
    {
        return view('livewire.ajo-contributions.history')->title('Ajo Contributions History');
    }
}
