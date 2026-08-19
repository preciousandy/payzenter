<?php

namespace App\Livewire\GiftCard;

use App\Helpers\SettingHelper;
use App\Models\UserTransaction;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Buy extends Component
{
    use LivewireAlert;

    public $gift_card = [];

    public $amount, $quantity, $name, $email;

    public $showModal = false;

    public $price = 0;


    public $fee = 0;
    public $feeAmount = 0;
    public $totalAmount = 0;
    public $pin;



    public function mount($productID)
    {
        $this->fill([
            'name' => auth()->user()->name,
            'email' => auth()->user()->email
        ]);

        $this->fee = SettingHelper::get(SettingHelper::BUY_GIFT_CARD_FEE, 0);

        try {
            $accessToken = getGiftcardsAccessToken();

            if (!$accessToken) {
                throw new \Exception('Failed to retrieve access token for gift cards. Please try again later.');
            }


            $res =   Http::withHeaders([
                'Accept' => 'application/com.reloadly.giftcards-v1+json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $accessToken
            ])->get(env("RELOADLY_API_URL") . "/products/" . $productID);

            $data = $res->json();

            $this->gift_card = $data;
        } catch (\Throwable $th) {
            throw $th->getMessage();
        }
    }

    public function validateOrder()
    {
        $data =  $this->validate([
            'amount' => 'required',
            'quantity' => 'required|numeric',
            'name' => 'required',
            'email' => 'required|email'
        ]);

        // dd($this->gift_card, $this->amount);

        if ($this->gift_card['denominationType'] == 'RANGE' && $data['amount'] < $this->gift_card['minRecipientDenomination']) {
            return $this->addError('amount', 'Amount must be greater than ' . $this->gift_card['minRecipientDenomination']);
        }

        if ($this->gift_card['denominationType'] == 'RANGE' && $data['amount'] > $this->gift_card['maxRecipientDenomination']) {
            return $this->addError('amount', 'Amount must be less than ' . $this->gift_card['maxRecipientDenomination']);
        }

        if ($this->gift_card['denominationType'] == 'FIXED') {
            $this->price = $this->gift_card['fixedRecipientToSenderDenominationsMap'][$data['amount']] * $data['quantity'] + $this->gift_card['senderFee'];
        } else {
            $conversionRate = $this->gift_card['minSenderDenomination'] / $this->gift_card['minRecipientDenomination'];
            $this->price = $data['amount'] * $conversionRate * $data['quantity'] + $this->gift_card['senderFee'];
        }


        $this->feeAmount = ($this->price * $this->fee) / 100;

        $this->totalAmount = round($this->price + $this->feeAmount, 2);

        $this->showModal = true;
    }

    public function submitOrder()
    {
        $this->validate([
            'price' => 'required|numeric',
            'pin' => 'required|numeric|min:4'

        ]);

        if (!Hash::check($this->pin, auth()->user()->pin)) {
            $this->alert('error', 'Incorrect pin');
            return;
        }


        if ($this->totalAmount > auth()->user()->wallet_balance) {
            return $this->alert('error', 'Insufficient funds');
        }

        $data = [
            'productId' => $this->gift_card['productId'],
            'countryCode' => $this->gift_card['country']['isoName'],
            'quantity' => $this->quantity,
            'unitPrice' => $this->amount,
            'currencyCode' => $this->gift_card['recipientCurrencyCode'],
            'customIdentifier' => 'gift-card-' . time(),
            'senderName' => $this->name,
            'recipientEmail' => $this->email,
        ];
        try {
            $accessToken = getGiftcardsAccessToken();

            if (!$accessToken) {
                throw new \Exception('Failed to retrieve access token for gift cards. Please try again later.');
            }

            $res = Http::withHeaders([
                'Accept' => 'application/com.reloadly.giftcards-v1+json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $accessToken
            ])->post(env("RELOADLY_API_URL") . "/orders", $data);

            $data = $res->json();


            if ($res->successful() && !$data['errorCode']) {
                auth()->user()->decrement('wallet_balance', $this->totalAmount);

                UserTransaction::create([
                    'user_id' => auth()->id(),
                    'amount' => $this->price,
                    'type' => UserTransaction::TYPE_DEBIT,
                    'message' => 'Bought ' . $this->quantity . ' of ' . $this->gift_card['productName'] . ' gift card',
                    'status' => UserTransaction::STATUS_SUCCESSFUL,
                    'source' => UserTransaction::SOURCE_GIFT_CARD,
                    'fee' => $this->feeAmount
                ]);

                $this->showModal = false;

                return $this->flash('success', 'Order placed successfully', [], route('home'));
            } else {
                throw new \Exception($data['message']);
            }
        } catch (\Throwable $th) {
            $this->alert('error', $th->getMessage());
            return;
        }
    }

    public function render()
    {
        return view('livewire.gift-card.buy')->title('Buy Gift Card');
    }
}
