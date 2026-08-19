<?php

namespace App\Http\Controllers\Api\Gateway;

use App\Http\Controllers\Controller;
use App\Models\MerchantApiKey;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MerchantApiKeyController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        $keys = MerchantApiKey::query()
            ->where('user_id', $request->user()->id)
            ->orderByDesc('id')
            ->get([
                'id',
                'name',
                'token_prefix',
                'last_used_at',
                'revoked_at',
                'created_at',
            ]);

        return $this->success($keys, 'Merchant API keys fetched');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:100',
        ]);

        $plain = 'mk_' . Str::random(48);
        $hash = hash('sha256', $plain);

        $key = MerchantApiKey::create([
            'user_id' => $request->user()->id,
            'name' => $validated['name'] ?? null,
            'token_prefix' => substr($plain, 0, 12),
            'token_hash' => $hash,
        ]);

        return $this->success([
            'id' => $key->id,
            'name' => $key->name,
            'token' => $plain,
        ], 'Merchant API key created');
    }

    public function revoke(Request $request, MerchantApiKey $merchantApiKey)
    {
        if ($merchantApiKey->user_id !== $request->user()->id) {
            return $this->error('Not found', 404);
        }

        $merchantApiKey->update(['revoked_at' => now()]);

        return $this->success([
            'id' => $merchantApiKey->id,
            'revoked_at' => $merchantApiKey->revoked_at,
        ], 'Merchant API key revoked');
    }
}

