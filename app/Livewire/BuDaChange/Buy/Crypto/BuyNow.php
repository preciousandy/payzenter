<?php

namespace App\Livewire\BuDaChange\Buy\Crypto;

use App\Helpers\SettingHelper;
use App\Models\BuyCryptoOrder;
use App\Models\CryptoAddress;
use App\Models\CryptoCurrency;
use App\Models\ExchangeRate;
use App\Models\UserTransaction;
use App\Services\CryptoService;
use App\Services\UserService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class BuyNow extends Component
{
    use LivewireAlert;
    public $currency;
    public $amount;
    public $nairaRate;
    public $nairaAmount = 0.00;
    public $wallet_address;
    public $crypto_network;

    public $dollarRate;
    public $showPreviewForm = false;
    public $fee;
    public $feeAmount;
    public $totalAmount;
    public $pin;

    public $quidax_wallet;
    public $quidax_wallet_address;
    public $networks = [];

    public $showModal = false;

    public $crypto_fee = 0;
    public $crypto_fee_in_naira = 0;

    public $remainingTime = 0; // Initial countdown time in seconds
    public bool $isButtonDisabled = true;

    public $destination_tag;



    public function mount($crypto)
    {
        // dd('here');

        $this->currency = CryptoCurrency::firstWhere('uuid', $crypto);

        $this->dollarRate = ExchangeRate::firstWhere('code', 'USD')->rate;

        $this->nairaRate  = $this->currency->price_in_usd * $this->dollarRate;

        $this->fee = SettingHelper::get(SettingHelper::BUY_CRYPTO_FEE, 0);

        $this->quidax_wallet_address = CryptoAddress::where('user_id', auth()->id())->where('currency_code', strtolower($this->currency->symbol))->first();

        $response = (new UserService)->getQuidaxCurrencyWallet(auth()->user()->quidax_id, strtolower($this->currency->symbol));

        if ($response['status'] == 'error') {
            // dd($response);

            return $this->flash('error', $response['message'], [], route('bureau-da-change.buy-crypto'));
        }

        $res = (new CryptoService)->getCurrencySwapRate(strtolower($this->currency->symbol));

        if ($res['status'] == 'error') {
            return $this->flash('error', $res['message'], [], route('bureau-da-change.buy-crypto'));
        }


        $this->nairaRate = round($res['data']['quoted_price'], 2);


        $this->quidax_wallet = $response['data'];

        $networks = $this->quidax_wallet['networks'];

        // dd($networks);

        $this->networks = collect($networks)->map(function ($net) {
            $address = CryptoAddress::where('user_id', auth()->id())
                ->where('currency_code', strtolower($this->currency->symbol))
                ->where('network', $net['id'])
                ->first();

            $net['payment_address'] = $address ? $address->address : null;
            $net['destination_tag'] = $address ? $address->destination_tag : null;

            return $net;
        })->toArray();
    }

    public function startCountdown()
    {
        $this->showModal = true;

        $this->remainingTime = 5;
    }

    public function decrement()
    {
        if ($this->remainingTime > 0) {
            $this->remainingTime--;
        }
    }

    public function render()
    {

        return view('livewire.bu-da-change.buy.crypto.buy-now')->title("Bureau De Change Buy Crypto Details");
    }

    public function updatedAmount($value)
    {
        // $amountInDollar = $value / $this->dollarRate;


        $this->nairaAmount = $value ? round($value * $this->nairaRate, 2) : 0.00;
    }

    public function setCryptoAddress($network, $address, $destination_tag = null)
    {
        $this->wallet_address = $address;
        $this->crypto_network = $network;
        $this->destination_tag = $destination_tag;
    }

    public function createOrder()
    {
        $this->validate([
            'wallet_address' => 'required',
            'amount' => 'required|numeric'
        ]);

        $this->feeAmount = ($this->nairaAmount * $this->fee) / 100;

        $this->totalAmount = $this->nairaAmount + $this->feeAmount;


        $res = (new CryptoService)->getCryptoFees(strtolower($this->currency->symbol), $this->crypto_network, $this->amount);

        if ($res['status'] == 'error') {
            $this->alert('error', $res['message']);
            return;
        }

        $this->crypto_fee = $res['data'];

        // dd($this->crypto_fee);
        $this->crypto_fee_in_naira = $this->crypto_fee * $this->nairaRate;

        $this->totalAmount += $this->crypto_fee_in_naira;

        if ($this->totalAmount > auth()->user()->wallet_balance) {
            $this->alert('error', 'Insufficient funds');
            return;
        }


        $this->showPreviewForm = true;
    }

    public function generatePaymentAddress($nework, $currency)
    {
        $response = (new UserService)->getQuidaxCurrencyPaymentAddress(auth()->user()->quidax_id, $nework, strtolower($currency));

        if ($response['status'] == 'error') {
            return $this->flash('error', $response['message'], [], route('bureau-da-change.buy-crypto'));
        }

        $this->startCountdown();
    }


    // public function test()
    // {
    //     BuyCryptoOrder::create(
    //         [
    //             'user_id' => auth()->id(),
    //             'crypto_name' => $this->currency->name,
    //             'crypto_symbol' => $this->currency->symbol,
    //             'amount_in_naira' => (int) floor($this->nairaAmount ?? 1000),
    //             'amount_in_crypto' => 12.12,
    //             'wallet_address' => $this->wallet_address,
    //             'crypto_network' => $this->crypto_network,
    //             'quotation_id' => $quotationId ?? 'jsjsjsix8sujssisi',
    //             'status' => BuyCryptoOrder::STATUS_PENDING
    //         ]
    //     );

    //     $this->alert('success', 'Order placed successfully');
    // }

    public function submitOrder()
    {

        $this->validate([
            'pin' => 'required|numeric|min:4'
        ]);

        if (!Hash::check($this->pin, auth()->user()->pin)) {
            $this->alert('error', 'Incorrect pin');
            return;
        }

        $cryptoService = new CryptoService;



        $swapQuotation = $cryptoService->generateSwapQuotation(strtolower($this->currency->symbol), $this->nairaAmount + $this->crypto_fee_in_naira  + BuyCryptoOrder::ALLOWANCE_AMOUNT);

        if ($swapQuotation['status'] == 'error') {
            return $this->alert('error', $swapQuotation['message']);
        }

        $quotationId = $swapQuotation['data']['id'];


        $swapOrder = $cryptoService->confirmSwapOrder($quotationId);

        if ($swapOrder['status'] == 'error') {
            return $this->alert('error', $swapOrder['message']);
        }


        DB::beginTransaction();

        BuyCryptoOrder::create(
            [
                'user_id' => auth()->id(),
                'crypto_name' => $this->currency->name,
                'crypto_symbol' => $this->quidax_wallet['currency'],
                'amount_in_naira' => (int) floor($this->totalAmount),
                'amount_in_crypto' => $this->amount,
                'wallet_address' => $this->wallet_address,
                'crypto_network' => $this->crypto_network,
                'quotation_id' => $quotationId,
                'destination_tag' => $this->destination_tag,
                'status' => BuyCryptoOrder::STATUS_PENDING,
                'crypto_fee' => $this->crypto_fee,
                'crypto_rate' => $this->nairaRate,
            ]
        );

        auth()->user()->decrement('wallet_balance', $this->totalAmount);

        auth()->user()->transactions()->create([
            'type' => UserTransaction::TYPE_DEBIT,
            'amount' => $this->totalAmount,
            'message' => "Bought {$this->amount} {$this->currency->symbol} for {$this->nairaAmount} NGN",
            'source' => UserTransaction::SOURCE_BUY_CRYPTO,
            'status' => UserTransaction::STATUS_SUCCESSFUL,
            'fee' => $this->feeAmount
        ]);

        DB::commit();


        $this->flash('success', 'Order placed successfully', [], route('bureau-da-change.buy-crypto'));
    }
}
