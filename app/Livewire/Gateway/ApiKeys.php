<?php

namespace App\Livewire\Gateway;

use App\Models\MerchantApiKey;
use Illuminate\Support\Str;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class ApiKeys extends Component
{
    use LivewireAlert;

    public $name;
    public $createdToken;

    public function createKey()
    {
        $validated = $this->validate([
            'name' => 'nullable|string|max:100',
        ]);

        $plain = 'mk_' . Str::random(48);
        $hash = hash('sha256', $plain);

        $key = MerchantApiKey::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'] ?? null,
            'token_prefix' => substr($plain, 0, 12),
            'token_hash' => $hash,
        ]);

        $this->createdToken = $plain;
        $this->name = null;

        $this->alert('success', 'API key created');
    }

    public function revokeKey(int $id)
    {
        $key = MerchantApiKey::query()
            ->where('user_id', auth()->id())
            ->where('id', $id)
            ->first();

        if (!$key) {
            $this->alert('error', 'API key not found');
            return;
        }

        $key->update(['revoked_at' => now()]);
        $this->alert('success', 'API key revoked');
    }

    public function render()
    {
        $keys = MerchantApiKey::query()
            ->where('user_id', auth()->id())
            ->orderByDesc('id')
            ->get();

        return view('livewire.gateway.api-keys', [
            'keys' => $keys,
        ])->title('API Keys');
    }
}

