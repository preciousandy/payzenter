<?php

namespace App\Livewire\BuDaChange;

use App\Helpers\SettingHelper;
use App\Models\CryptoAddress;
use App\Models\CryptoCurrency;
use App\Models\ExchangeRate;
use App\Models\SellCryptoOrder;
use App\Models\UserTransaction;
use App\Services\CryptoService;
use App\Services\UserService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class SellCrypto extends Component
{
    use LivewireAlert;

    public $currency;
    public $amount, $nairaAmount;

    public $dollarRate;

    public $quidax_wallet;
    public $quidax_wallet_address;
    public $quidax_wallet_balance;
    public $networks = [];

    public $nairaRate;
    public $totalAmount;
    public $fee;
    public $feeAmount;

    public $pin;

    public $showPreviewForm = false;

    public $showModal = false;

    public $remainingTime = 0; // Initial countdown time in seconds
    public bool $isButtonDisabled = true;

    public $destination_tag;


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


    public function mount($crypto)
    {
        $this->currency = CryptoCurrency::firstWhere('uuid', $crypto);

        $this->dollarRate = ExchangeRate::firstWhere('code', 'USD')->rate;

        $response = (new UserService)->getQuidaxCurrencyWallet(auth()->user()->quidax_id, strtolower($this->currency->symbol));

        if ($response['status'] == 'error') {

            // dd($response);
            return $this->flash('error', $response['message'], [], route('bureau-da-change.sell-crypto.index'));
        }

        $res = (new CryptoService)->getCurrencySwapRate('ngn', strtolower($this->currency->symbol));

        if ($res['status'] == 'error') {
            // dd("swap rate", $res);

            return $this->flash('error', $res['message'], [], route('bureau-da-change.sell-crypto.index'));
        }

        $this->nairaRate = round($res['data']['quoted_price'], 2);

        $this->quidax_wallet = $response['data'];

        $this->quidax_wallet_balance  = $this->quidax_wallet['balance'];

        $networks = $this->quidax_wallet['networks'];

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


    public function render()
    {
        return view('livewire.bu-da-change.sell-crypto')->title("Bureau De Change Sell Crypto Details");
    }

    public function updatedAmount($value)
    {

        if (is_numeric($value) && $value > 0) {
            $this->nairaAmount = $value * $this->nairaRate;
        }
    }

    public function createOrder()
    {
        $this->validate([
            'amount' => 'required|numeric|min:0.0001'
        ]);

        if ($this->amount > $this->quidax_wallet_balance) {
            $this->alert('error', 'Insufficient Crypto balance');
            return;
        }

        $this->fee = SettingHelper::get(SettingHelper::SELL_CRYPTO_FEE, 0);

        $this->feeAmount = getPercentageAmount($this->nairaAmount, $this->fee);


        $this->totalAmount = $this->nairaAmount - $this->feeAmount;


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

        // $swapQuotation = $cryptoService->generateUserSwapQuotation(auth()->user()->quidax_id, strtolower($this->currency->symbol), $this->amount);

        // if ($swapQuotation['status'] == 'error') {
        //     return $this->alert('error', $swapQuotation['message']);
        // }

        // $quotationId = $swapQuotation['data']['id'];

        // $swapOrder = $cryptoService->confirmUserSwapOrder(auth()->user()->quidax_id, $quotationId);

        // if ($swapOrder['status'] == 'error') {
        //     return $this->alert('error', $swapOrder['message']);
        // }

        $uniqueId = 'SELL_CRYPTO' . uniqid();

        $res = $cryptoService->getCryptoFromUser(auth()->user()->quidax_id, strtolower($this->currency->symbol), $this->amount, $uniqueId);

        if ($res['status'] == 'error') {
            return $this->alert('error', $res['message']);
        }


        $order =  SellCryptoOrder::create([
            'user_id' => auth()->id(),
            'crypto_name' => $this->currency->name,
            'crypto_symbol' => $this->currency->symbol,
            'amount_in_naira' => $this->totalAmount,
            'amount_in_crypto' => $this->amount,
            'quotation_id' => $uniqueId,
            'status' => SellCryptoOrder::STATUS_PENDING,
            'crypto_rate' => $this->nairaRate,
        ]);

        // Create user transaction for sell crypto
        auth()->user()->transactions()->create([
            'type' => UserTransaction::TYPE_CREDIT,
            'amount' => $this->totalAmount,
            'message' => "Sold {$this->amount} {$this->currency->symbol} for {$this->nairaAmount} NGN",
            'source' => UserTransaction::SOURCE_SELL_CRYPTO,
            'status' => UserTransaction::STATUS_PENDING,
            'fee' => $this->feeAmount
        ]);

        $this->flash('success', 'Order placed successfully', [], route('bureau-da-change.sell-crypto.index'));
    }
}
