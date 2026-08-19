<?php

namespace App\Livewire;

use Flutterwave\Util\Currency;
use Livewire\Component;

class AddCardDetails extends Component
{
    public $name;
    public $card_number;
    public $expiry_date;
    public $cvv;

    public function chargeCard()
    {
        $data = $this->validate([
            'name' => 'required|string',
            'card_number' => 'required|numeric|min_digits:16',
            'expiry_date' => 'required',
            'cvv' => 'required|numeric|digits:3',
        ]);

        $data = [
            "amount" => 2000,
            "currency" => Currency::NGN,
            "tx_ref" => "TEST-" . uniqid() . time(),
            "redirectUrl" => "https://www.example.com",
            "additionalData" => [
                "subaccounts" => [
                    ["id" => "RSA_345983858845935893"]
                ],
                "meta" => [
                    "unique_id" => uniqid() . uniqid()
                ],
                "preauthorize" => false,
                "payment_plan" => null,
                "card_details" => [
                    "card_number" => $this->card_number,
                    "cvv" => $this->cvv,
                    "expiry_month" => substr($this->expiry_date, 0, 2),
                    "expiry_year" => substr($this->expiry_date, 2, 2),
                ]
            ],
        ];


        $cardpayment = \Flutterwave\Flutterwave::create("card");
        $customerObj = $cardpayment->customer->create([
            "full_name" => auth()->user()->name,
            "email" => auth()->user()->email,
            "phone" => auth()->user()->phone,
        ]);
        $data['customer'] = $customerObj;

        $payload  = $cardpayment->payload->create($data);


        $result = $cardpayment->initiate($payload);

        dd($result);

        //         $paymentData = [
        //             "card_number" => $this->card_number,
        //             "expiry_month" => substr($this->expiry_date, 0, 2),
        //             "expiry_year" => substr($this->expiry_date, 2, 2),
        //             "cvv" => $this->cvv,
        //             "currency" => "NGN",
        //             "amount" => "200",
        //             "email" => auth()->user()->email,
        //             "fullname" => $this->name,
        //             "phone_number" => auth()->user()->phone,
        //             "tx_ref" => "TEST-" . uniqid() . time(),
        //             "redirect_url" => route('add-card-details'),
        //         ];

        //         $encryptedData = $this->encrypt($paymentData);

        //         $res = Http::withHeaders([
        //             'Content-Type' => 'application/json',
        //             'Authorization' => 'Bearer ' . env("FLW_SECRET_KEY")
        //         ])->post(env("FLW_API_URL") . "/charges?type=card
        // ", $encryptedData);

        //         $data = $res->json();
    }

    public function render()
    {
        return view('livewire.add-card-details')->title('Add Card Details');
    }
}
