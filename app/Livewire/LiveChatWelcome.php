<?php

namespace App\Livewire;

use App\Models\liveSupportTopic;
use Livewire\Component;

class LiveChatWelcome extends Component
{

    public $topics = [];
    public $topic;

    public function mount()
    {

        $nameMap = [
            'ajo contribution' => 'API & Integrations',
            'ajo savings' => 'Virtual Accounts',
            'bills' => 'Payments',
            'transfer' => 'Withdrawals',
            'general' => 'General Support',
        ];

        $sortOrder = [
            'Payments' => 1,
            'Virtual Accounts' => 2,
            'Withdrawals' => 3,
            'API & Integrations' => 4,
            'General Support' => 5,
        ];

        $this->topics = liveSupportTopic::orderBy('name')->get()->each(function ($topic) use ($nameMap) {
            $key = strtolower(trim((string) $topic->name));
            if (array_key_exists($key, $nameMap)) {
                $topic->name = $nameMap[$key];
            }
            $topic->messages_count = $topic->messages()->where('user_id', auth()->id())->count();
        })->sortBy(function ($topic) use ($sortOrder) {
            return $sortOrder[$topic->name] ?? 999;
        })->values();
    }


    public function render()
    {
        return view('livewire.live-chat-welcome')->title('Welcome to Payment Gateway Ng Live Chat');
    }
}
