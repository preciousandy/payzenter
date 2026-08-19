<?php

namespace App\Livewire\Admin;

use App\Models\ApiKey;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class ApiKeys extends Component
{
    use LivewireAlert;

    public $apiKeys = [];
    public $values = [];

    public function mount()
    {
        $this->loadApiKeys();
    }

    public function loadApiKeys()
    {
        // Get all existing API keys from database
        $existingKeys = ApiKey::all()->keyBy('name');

        // Get all available constants
        $constants = ApiKey::getApiKeyConstants();

        $this->apiKeys = [];
        $index = 0;

        foreach ($constants as $key => $displayName) {
            $existingKey = $existingKeys->get($key);

            $this->apiKeys[] = [
                'id' => $existingKey->id ?? null,
                'name' => $key,
                'display_name' => $displayName,
                'value' => $existingKey->value ?? '',
                'description' => $existingKey->description ?? ''
            ];

            $this->values[$index] = [
                'name' => $key,
                'value' => $existingKey->value ?? '',
                'description' => $existingKey->description ?? ''
            ];

            $index++;
        }
    }

    public function updateApiKey($index)
    {
        $this->validate([
            "values.{$index}.value" => 'required|string',
            "values.{$index}.description" => 'nullable|string'
        ], [
            "values.{$index}.value.required" => 'The API key value is required.'
        ]);

        $apiKeyData = $this->values[$index];

        ApiKey::updateOrCreate(
            ['name' => $apiKeyData['name']],
            [
                'value' => $apiKeyData['value'],
                'description' => $apiKeyData['description']
            ]
        );

        $this->alert('success', 'API key updated successfully!');
        $this->loadApiKeys(); // Refresh the data
    }

    public function render()
    {
        return view('livewire.admin.api-keys')->extends('layouts.admin');
    }
}
