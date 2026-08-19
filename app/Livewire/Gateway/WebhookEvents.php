<?php

namespace App\Livewire\Gateway;

use App\Models\GatewayWebhookDelivery;
use App\Services\GatewayWebhookService;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class WebhookEvents extends Component
{
    use WithPagination;
    use LivewireAlert;

    public $merchant_webhook_url = '';
    public $status = '';
    public $event = '';

    protected $queryString = [
        'status' => ['except' => ''],
        'event' => ['except' => ''],
    ];

    public function mount()
    {
        $this->merchant_webhook_url = (string) (auth()->user()->merchant_webhook_url ?? '');
    }

    public function updating($name, $value)
    {
        if (in_array($name, ['status', 'event'], true)) {
            $this->resetPage();
        }
    }

    public function saveWebhookUrl()
    {
        $validated = $this->validate([
            'merchant_webhook_url' => 'nullable|url|max:500',
        ]);

        auth()->user()->update([
            'merchant_webhook_url' => $validated['merchant_webhook_url'] ?: null,
        ]);

        $this->merchant_webhook_url = (string) ($validated['merchant_webhook_url'] ?? '');
        $this->alert('success', 'Webhook URL saved');
    }

    public function repush(GatewayWebhookService $service, int $deliveryId)
    {
        $delivery = GatewayWebhookDelivery::query()
            ->where('merchant_id', auth()->id())
            ->where('id', $deliveryId)
            ->first();

        if (!$delivery) {
            $this->alert('error', 'Webhook delivery not found');
            return;
        }

        $service->attempt($delivery);

        $this->alert('success', 'Webhook repush attempted');
    }

    public function render()
    {
        $query = GatewayWebhookDelivery::query()
            ->where('merchant_id', auth()->id())
            ->with('transaction')
            ->orderByDesc('id');

        if ($this->status === 'delivered') {
            $query->whereNotNull('delivered_at');
        }

        if ($this->status === 'failed') {
            $query->whereNull('delivered_at')->whereNotNull('last_attempt_at');
        }

        if ($this->event !== '') {
            $query->where('event', $this->event);
        }

        $deliveries = $query->paginate(15);

        return view('livewire.gateway.webhook-events', [
            'deliveries' => $deliveries,
        ])->title('Webhook Events');
    }
}
