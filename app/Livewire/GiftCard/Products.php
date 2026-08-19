<?php

namespace App\Livewire\GiftCard;

use Illuminate\Support\Facades\Http;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Url;
use Livewire\Component;

class Products extends Component
{
    use LivewireAlert;

    public $gift_cards = [];
    public $gift_cards_copy = [];

    #[Url()]
    public $query;

    public function mount($countryCode)
    {
        try {
            $accessToken = getGiftcardsAccessToken();

            if (!$accessToken) {
                throw new \Exception('Failed to retrieve access token for gift cards. Please try again later.');
            }


            $res =   Http::withHeaders([
                'Accept' => 'application/com.reloadly.giftcards-v1+json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $accessToken
            ])->get(env("RELOADLY_API_URL") . "/countries/" . $countryCode . "/products");

            $data = $res->json();

            if (isset($data['errorCode'])) {
                throw new \Exception($data['message']);
            }

            $this->gift_cards = $data;
            $this->gift_cards_copy = $data;
        } catch (\Throwable $th) {
            return $this->flash('error', $th->getMessage(), [], route('gift-cards.countries'));
        }
    }

    public function updatedQuery()
    {
        $this->gift_cards = collect($this->gift_cards_copy)->filter(function ($item) {
            return stripos($item['productName'], $this->query) !== false;
        })->toArray();
    }


    public function render()
    {
        return view('livewire.gift-card.products')->title('Gift Card Products');
    }
}
