<?php

namespace App\Livewire\Gateway;

use App\Helpers\SettingHelper;
use App\Models\GatewayTransaction;
use Illuminate\Support\Str;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentLinks extends Component
{
    use LivewireAlert;
    use WithPagination;

    public $amount;
    public $currency = 'NGN';
    public $method = 'card';
    public $return_url;
    public $merchant_webhook_url;
    public $createdLink;

    public function mount()
    {
        $this->merchant_webhook_url = auth()->user()->merchant_webhook_url;
    }

    public function updating($name, $value)
    {
        $this->resetPage();
    }

    public function createLink()
    {
        $validated = $this->validate([
            'amount' => 'required|numeric|min:100',
            'currency' => 'nullable|string|max:10',
            'method' => 'required|string|in:card,transfer,ussd',
            'return_url' => 'required|url|max:500',
            'merchant_webhook_url' => 'nullable|url|max:500',
        ]);

        $amountKobo = (int) round(((float) $validated['amount']) * 100);
        if ($amountKobo < 10000) {
            $this->alert('error', 'Minimum amount is 100 NGN');
            return;
        }

        $provider = (string) SettingHelper::get('gateway_provider', 'flutterwave');
        $provider = $provider !== '' ? strtolower($provider) : 'flutterwave';
        if (!in_array($provider, ['flutterwave', 'palmpay'], true)) {
            $provider = 'flutterwave';
        }

        $reference = 'plink_' . Str::lower(Str::random(24));
        while (GatewayTransaction::query()->where('reference', $reference)->exists()) {
            $reference = 'plink_' . Str::lower(Str::random(24));
        }

        $merchantWebhookUrl = $validated['merchant_webhook_url'] ?? auth()->user()->merchant_webhook_url;

        $tx = GatewayTransaction::create([
            'merchant_id' => auth()->id(),
            'reference' => $reference,
            'amount' => $amountKobo,
            'currency' => $validated['currency'] ?? 'NGN',
            'method' => $validated['method'],
            'status' => 'unpaid',
            'provider' => $provider,
            'provider_reference' => $reference,
            'return_url' => $validated['return_url'],
            'merchant_webhook_url' => $merchantWebhookUrl,
        ]);

        $this->createdLink = route('gateway.pay', ['reference' => $tx->reference], true);
        $this->alert('success', 'Payment link created');
    }

    public function render()
    {
        $links = GatewayTransaction::query()
            ->where('merchant_id', auth()->id())
            ->where('reference', 'like', 'plink_%')
            ->orderByDesc('id')
            ->paginate(15);

        return view('livewire.gateway.payment-links', [
            'links' => $links,
        ])->title('Payment Links');
    }
}
