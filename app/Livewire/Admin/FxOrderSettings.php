<?php

namespace App\Livewire\Admin;

use App\Models\ExchangeRate;
use App\Models\FxFormField;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class FxOrderSettings extends Component
{
    use LivewireAlert;

    public $buyFields = [];
    public $sellFields = [];
    public $availableCurrencies = [];

    // Modal properties
    public $showModal = false;
    public $editingField = null;
    public $fieldLabel = '';
    public $fieldType = 'text';
    public $fieldRequired = false;
    public $fieldFormType = 'buy';
    public $selectedCurrencies = [];

    public function mount()
    {
        $this->loadFields();
        $this->loadAvailableCurrencies();
    }

    public function loadFields()
    {
        $this->buyFields = FxFormField::forFormType('buy')->get()->toArray();
        $this->sellFields = FxFormField::forFormType('sell')->get()->toArray();
    }

    public function loadAvailableCurrencies()
    {
        $this->availableCurrencies = ExchangeRate::distinct()->pluck('code')->toArray();
    }

    public function openModal($formType, $fieldId = null)
    {
        $this->fieldFormType = $formType;
        $this->showModal = true;

        if ($fieldId) {
            $this->editingField = $fieldId;
            $field = FxFormField::find($fieldId);
            $this->fieldLabel = $field->label;
            $this->fieldType = $field->type;
            $this->fieldRequired = $field->required;
            $this->selectedCurrencies = $field->currency_codes ?? [];
        } else {
            $this->resetFieldForm();
        }
    }

    public function resetFieldForm()
    {
        $this->editingField = null;
        $this->fieldLabel = '';
        $this->fieldType = 'text';
        $this->fieldRequired = false;
        $this->selectedCurrencies = [];
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetFieldForm();
    }

    public function saveField()
    {
        $this->validate([
            'fieldLabel' => 'required|string|max:255',
            'fieldType' => 'required|in:text,file,date,time,email,number',
            'selectedCurrencies' => 'required|array|min:1',
            'selectedCurrencies.*' => 'string|exists:exchange_rates,code'
        ]);

        $data = [
            'label' => $this->fieldLabel,
            'type' => $this->fieldType,
            'required' => $this->fieldRequired,
            'form_type' => $this->fieldFormType,
            'currency_codes' => $this->selectedCurrencies
        ];

        if ($this->editingField) {
            FxFormField::find($this->editingField)->update($data);
            $this->alert('success', 'Field updated successfully');
        } else {
            FxFormField::create($data);
            $this->alert('success', 'Field created successfully');
        }

        $this->loadFields();
        $this->closeModal();
    }

    public function deleteField($fieldId)
    {
        FxFormField::find($fieldId)->delete();
        $this->loadFields();
        $this->alert('success', 'Field deleted successfully');
    }

    public function render()
    {
        return view('livewire.admin.fx-order-settings');
    }
}
