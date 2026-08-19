<?php

namespace App\Livewire\Admin;

use App\Models\ApiKey;
use App\Models\BuyCryptoOrder;
use App\Models\CryptoCurrency;
use App\Models\ExchangeRate;
use App\Models\SellCryptoOrder;
use App\Models\UserTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class AllCryptoCurrency extends Component
{
    use LivewireAlert;
    public $rejectMessage;


    public function render()
    {
        return view('livewire.admin.all-crypto-currency', [
            'currencies' => CryptoCurrency::all(),
            'dollarRate' => ExchangeRate::firstWhere('code', 'USD')->rate,
            'saleOrders' => SellCryptoOrder::latest()->get(),
            'buyOrders' => BuyCryptoOrder::latest()->get(),
        ])->extends('layouts.admin');
    }

    public function refreshRates()
    {

        try {
            $res = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'X-RapidAPI-Key' => ApiKey::getApiKey(ApiKey::RAPID_API_KEY),
                'X-RapidAPI-Host' => env('COIN_RANKING_API_HOST')
            ])->get(env("COIN_RANKING_API_URL") . "/coins", [
                'referenceCurrencyUuid' => 'yhjMzLPhuIDl',
                'timePeriod' => '24h',
                'tiers[0]' => 1,
                'limit' => 100,
            ]);

            $data = $res->json();

            if ($res->successful()) {

                $coins = $data['data']['coins'];


                foreach ($coins as $coin) {
                    CryptoCurrency::updateOrCreate(
                        ['uuid' => $coin['uuid']],
                        [
                            'name' => $coin['name'],
                            'symbol' => $coin['symbol'],
                            'icon' => $coin['iconUrl'],
                            'price_in_usd' => $coin['price'],
                            'changes' => array_slice($coin['sparkline'], 0, -1),
                            'status' => CryptoCurrency::STATUS_OPEN,
                            'change_percentage' => $coin['change'],
                        ]
                    );
                }

                $this->flash('success', 'Rates refreshed successfully', [], route('admin.crypto-currency'));
            } else {
                throw new \Exception($data['message']);
            }
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage());
        }
    }

    public function approveSale($id)
    {
        $order = SellCryptoOrder::findOrFail($id);

        DB::beginTransaction();

        $order->update([
            'status' => SellCryptoOrder::STATUS_COMPLETED,
        ]);

        $order->user->update([
            'wallet_balance' => $order->user->wallet_balance + $order->amount_in_naira,
        ]);

        $order->user->transactions()->create([
            'amount' => $order->amount_in_naira,
            'type' => 'credit',
            'message' => "Crypto sale of {$order->crypto_name} approved",
            'status' => UserTransaction::STATUS_SUCCESSFUL,
        ]);

        DB::commit();

        return redirect()->route('admin.crypto-currency')->with('success', "Sale approved successfully");
    }

    public function rejectSale($id)
    {
        $this->validate([
            'rejectMessage' => 'required',
        ]);

        $order = SellCryptoOrder::findOrFail($id);

        DB::beginTransaction();

        $order->update([
            'status' => SellCryptoOrder::STATUS_FAILED,
            'status_message' => $this->rejectMessage,
        ]);

        $order->user->transactions()->create([
            'amount' => $order->amount_in_naira,
            'type' => UserTransaction::TYPE_CREDIT,
            'message' => "Crypto sale of {$order->crypto_name} was rejected",
            'status' => UserTransaction::STATUS_FAILED,
        ]);

        DB::commit();

        return redirect()->route('admin.crypto-currency')->with('success', "Sale rejected successfully");
    }

    public function approveBuyOrder($id)
    {
        $order = BuyCryptoOrder::findOrFail($id);

        DB::beginTransaction();

        $order->update([
            'status' => BuyCryptoOrder::STATUS_COMPLETED,
        ]);



        $order->user->transactions()->create([
            'amount' => $order->amount_in_naira,
            'type' => UserTransaction::TYPE_DEBIT,
            'message' => "Buying Crypto of {$order->crypto_name} approved",
            'status' => UserTransaction::STATUS_SUCCESSFUL,
        ]);

        DB::commit();

        return redirect()->route('admin.crypto-currency')->with('success', "Buying of Crypto approved successfully");
    }

    public function rejectBuyOrder($id)
    {
        $this->validate([
            'rejectMessage' => 'required',
        ]);

        $order = BuyCryptoOrder::findOrFail($id);

        DB::beginTransaction();

        $order->update([
            'status' => BuyCryptoOrder::STATUS_FAILED,
            'status_message' => $this->rejectMessage,
        ]);

        $order->user->increment('wallet_balance', $order->amount_in_naira);

        $order->user->transactions()->create([
            'amount' => $order->amount_in_naira,
            'type' => UserTransaction::TYPE_CREDIT,
            'message' => "Buying of {$order->crypto_name} was rejected",
            'status' => UserTransaction::STATUS_FAILED,
        ]);

        DB::commit();

        return redirect()->route('admin.crypto-currency')->with('success', "Buying Crypto was rejected");
    }
}
