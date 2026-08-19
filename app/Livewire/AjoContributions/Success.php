<?php

namespace App\Livewire\AjoContributions;

use App\Models\AjoContribution;
use App\Services\HashIdService;
use Livewire\Component;

class Success extends Component
{
    public $ajoContribution;

    public function mount($hashId)
    {
        $this->ajoContribution = (new HashIdService)->getRecord($hashId, AjoContribution::class);
    }

    public function render()
    {
        return view('livewire.ajo-contributions.success')->title('Ajo Contribution Success');
    }
}
