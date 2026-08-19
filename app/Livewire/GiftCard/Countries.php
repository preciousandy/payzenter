<?php

namespace App\Livewire\GiftCard;

use App\Models\ReloadlyCountry;
use Livewire\Attributes\Url;
use Livewire\Component;

class Countries extends Component
{
    #[Url()]
    public $query;

    public function test()
    {
        $token = getGiftcardsAccessToken();

        dd($token);
    }


    public function render()
    {

        $countries =  ReloadlyCountry::orderBy('name')
            ->when($this->query, function ($query) {
                return $query->where('name', 'like', '%' . $this->query . '%');
            })
            ->get();
        return view('livewire.gift-card.countries', [
            'countries' => $countries
        ])->title('Gift Card Countries');
    }
}
