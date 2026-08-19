<?php

namespace App\Livewire\Admin;

use App\Models\ElectricProvider;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;

class AllElectricProviders extends Component
{
    use LivewireAlert;
    use WithFileUploads;

    public $logo_upload;

    public function updateElectricityProvider($provider_id)
    {
        $this->validate([
            'logo_upload' => 'nullable|image|max:5024', // 1MB Max
        ]);

        $provider = ElectricProvider::findOrFail($provider_id);

        if ($this->logo_upload) {
            $provider->logo = $this->logo_upload->store('electricity_providers', 'public');
        }

        $provider->save();

        return $this->flash('success', 'Electricity provider updated successfully!', [], route('admin.electricity-providers'));
    }

    public function render()
    {
        return view('livewire.admin.all-electric-providers', [
            'providers' => ElectricProvider::orderBy('name')->get(),
        ])->extends('layouts.admin');
    }
}
