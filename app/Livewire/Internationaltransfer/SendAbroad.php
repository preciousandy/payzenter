<?php

namespace App\Livewire\Internationaltransfer;

use App\Helpers\SettingHelper;
use App\Models\ExchangeRate;
use App\Models\InternationalTransfer;
use App\Models\TransferRate;
use App\Models\UserTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use KingFlamez\Rave\Facades\Rave as Flutterwave;


class SendAbroad extends Component
{
    use LivewireAlert;


    public  $currencies = [
        'GBP' => [
            'UK'
        ],
        'USD' => [
            'Nigeria (domiciliary account)',
            'US'
        ],
        'EUR' => [
            'Europe'
        ]
    ];

    public $selectedCurrency = 'USD';
    public $country = "US"; // only US is supported for now
    public $amount;
    public $totalAmount;
    public $account_number;
    public $account_name;
    public $bank_name;
    public $swift_code;
    public $routing_number;
    public $description;
    public $totalFee;
    public $price;

    public $fixedFee;
    public $percentageFee;
    public $showPreviewForm = false;
    public $domiciliaryBanks = [];
    public $bank_code;
    public $pin;

    public $postal_code;
    public $street_name;
    public $street_number;
    public $city;

    public $currencyLogos = [];

    public $foreignCurrencyFee = 0;

    public function mount()
    {
        $this->fixedFee = SettingHelper::get(SettingHelper::INTERNATIONAL_TRANFER_FEE_FIXED);
        $this->percentageFee = SettingHelper::get(SettingHelper::INTERNATIONAL_TRANFER_FEE_PERCENTAGE);

        $this->domiciliaryBanks  = [
            "214" => "First City Monument Bank",
            "101" => "Providus Bank",
            "044" => "Other Banks"
        ];

        $this->currencyLogos = [
            'USD' => ExchangeRate::where('code', 'USD')->first()->logo_url,
            'GBP' => ExchangeRate::where('code', 'GBP')->first()->logo_url,
            'EUR' => ExchangeRate::where('code', 'EUR')->first()->logo_url,
        ];
    }

    public function confirmTransfer()
    {
        $this->validate([
            'amount' => 'required|numeric|min:0',
            'account_number' => 'required|string|max:20',
            'account_name' => 'required|string|max:100',
            'bank_name' => 'required|string|max:100',
            'swift_code' => 'nullable|string|max:11', // SWIFT codes are usually 8 or 11 characters
            'routing_number' => 'nullable|string|max:9',
            'selectedCurrency' => 'required|string',
            'country' => 'required|string',
            'description' => 'required|string',
        ]);

        if ($this->selectedCurrency == 'EUR' || $this->selectedCurrency == 'GBP') {
            $this->validate([
                'postal_code' => 'required|string',
                'street_name' => 'required|string',
                'street_number' => 'required|string',
                'city' => 'required|string',
            ]);
        }



        $rate = TransferRate::where('currency', $this->selectedCurrency)->first();

        $price = $this->amount * $rate->rate;

        $this->price = $price;

        $this->totalFee = getTotalFee($price, $this->fixedFee, $this->percentageFee);

        $this->totalAmount = $price + $this->totalFee;

        $foreignCurrencyPercentageFeeValue = getPercentageAmount($this->amount, $rate->percentage_fee);

        $this->foreignCurrencyFee = $foreignCurrencyPercentageFeeValue + $rate->fixed_fee;

        $fee_rate = $rate->rate * $this->foreignCurrencyFee;

        $this->totalAmount += $fee_rate;



        $this->showPreviewForm = true;
    }

    public function transfer()
    {
        $data =   $this->validate([
            'pin' => 'required|integer|digits:4',
        ]);

        if (!Hash::check($this->pin, auth()->user()->pin)) {
            $this->alert('error', 'Incorrect pin');
            return;
        }

        if ($this->totalAmount > auth()->user()->wallet_balance) {
            $this->alert('error', 'Insufficient funds');
            return;
        }

        $rate = TransferRate::where('currency', $this->selectedCurrency)->first();

        $data = [];

        if ($this->selectedCurrency == 'USD' && $this->country == "US") {
            $data = [
                "amount" => $this->amount,
                "narration" => $this->description,
                "currency" => "USD",
                "beneficiary_name" => $this->account_name,
                "meta" => [
                    [
                        "account_number" => $this->account_number,
                        "routing_number" => $this->routing_number,
                        "swift_code" => $this->swift_code,
                        "bank_name" => $this->bank_name,
                        "beneficiary_name" => $this->account_name,
                        "beneficiary_country" => "US"
                    ]
                ]
            ];
        }

        // domiciliary transfer not supported for now
        // if ($this->selectedCurrency == 'USD' && $this->country == "Nigeria (domiciliary account)") {
        //     $data = [
        //         "amount" => $this->amount,
        //         "narration" => $this->description,
        //         "currency" => "USD",
        //         "beneficiary_name" => $this->account_name,
        //         "account_bank" => $this->bank_code ?? "044",
        //         "account_number" => $this->account_number,
        //         "meta" => [
        //             [
        //                 "beneficiary_country" => "NG",
        //                 "sender" => auth()->user()->name,
        //                 "first_name" => auth()->user()->first_name,
        //                 "last_name" => auth()->user()->last_name,
        //                 "email" => auth()->user()->email,
        //                 "mobile_number" => auth()->user()->phone,
        //                 "merchant_name" => config('app.name'),
        //                 "beneficiary_country" => "NG"
        //             ]
        //         ]
        //     ];
        // }

        if ($this->selectedCurrency == 'GBP') {
            $data = [
                "amount" => $this->amount,
                "narration" => $this->description,
                "currency" => "GBP",
                "beneficiary_name" => $this->account_name,
                "meta" => [
                    [
                        "account_number" => $this->account_number,
                        "routing_number" => $this->routing_number,
                        "swift_code" => $this->swift_code,
                        "bank_name" => $this->bank_name,
                        "beneficiary_name" => $this->account_name,
                        "beneficiary_country" => "UK",
                        "postal_code" => $this->postal_code,
                        "street_name" => $this->street_name,
                        "street_number" => $this->street_number,
                        "city" => $this->city
                    ]
                ]
            ];
        }

        if ($this->selectedCurrency == 'EUR') {
            $data = [
                "amount" => $this->amount,
                "narration" => $this->description,
                "currency" => "EUR",
                "beneficiary_name" => $this->account_name,
                "meta" => [
                    [
                        "account_number" => $this->account_number,
                        "routing_number" => $this->routing_number,
                        "swift_code" => $this->swift_code,
                        "bank_name" => $this->bank_name,
                        "beneficiary_name" => $this->account_name,
                        "beneficiary_country" => "DE",
                        "postal_code" => $this->postal_code,
                        "street_name" => $this->street_name,
                        "street_number" => $this->street_number,
                        "city" => $this->city
                    ]
                ]
            ];
        }

        try {
            $reference = Flutterwave::generateReference();

            $data['reference'] = $reference;

            $transaction = Flutterwave::transfers()->initiate($data);

            DB::beginTransaction();

            if ($transaction['status'] == 'success') {
                $transfer = InternationalTransfer::create([
                    'user_id' => auth()->id(),
                    'reference' => $transaction['data']['reference'],
                    'status' => InternationalTransfer::STATUS_PENDING,
                    'recipient_name' => $this->account_name,
                    'recipient_account_number' => $this->account_number,
                    'recipient_bank' => $this->bank_name,
                    'bank_swift_code' => $this->swift_code,
                    'bank_routing_number' => $this->routing_number,
                    'recipient_amount' => $this->amount,
                    'recipient_currency' => $this->selectedCurrency,
                    'description' => $this->description,
                    'amount_deducted_in_local_currency' => $this->totalAmount,
                    'bank_code' => $this->bank_code,
                    'postal_code' => $this->postal_code,
                    'street_name' => $this->street_name,
                    'street_number' => $this->street_number,
                    'city' => $this->city,
                ]);

                auth()->user()->decrement('wallet_balance', $this->totalAmount);

                UserTransaction::create([
                    'user_id' => auth()->id(),
                    'amount' => $this->totalAmount,
                    'type' => UserTransaction::TYPE_DEBIT,
                    'message' => 'International transfer to ' . $this->account_name,
                    'status' => UserTransaction::STATUS_PENDING,
                    'source' => UserTransaction::SOURCE_INTERNATIONAL_TRANSFER,
                    'fee' => $this->totalFee + ($rate->rate * $this->foreignCurrencyFee)
                ]);

                DB::commit();

                $this->flash('success', 'Transfer initiated successfully', [], route('international-transfer.index'));
            } else {
                throw new \Exception($transaction['message']);
            }
        } catch (\Throwable $th) {
            $this->alert('error', 'An error occurred' . $th->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.internationaltransfer.send-abroad')->title("Send Abroad");
    }
}
