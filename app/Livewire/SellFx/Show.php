<?php

namespace App\Livewire\SellFx;

use App\Helpers\SettingHelper;
use App\Models\ExchangeRate;
use App\Models\FxFormField;
use App\Models\SellFxOrder;
use App\Models\UserTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class Show extends Component
{

    use LivewireAlert;
    use WithFileUploads;

    public $currency, $amount;

    public $nairaAmount =  0;

    public $acct_name, $acct_number, $acct_bank;
    public $showPreviewForm = false;
    public $pin;
    public $fee = 0;
    public $feeAmount = 0;
    public $totalAmount = 0;

    public $formFields = [];
    public $dynamicFields = [];

    public function mount($currencyCode)
    {
        $this->currency = ExchangeRate::where('code', $currencyCode)->first();

        $this->fee = SettingHelper::get(SettingHelper::BUREAU_DA_CHANGE_SELL_FEE, 10);

        // Get fields specific to this currency for sell form
        $this->formFields = FxFormField::forFormType('sell')
            ->forCurrency($currencyCode)
            ->get()
            ->toArray();

        foreach ($this->formFields as $field) {
            $this->dynamicFields[$field['label']] = '';
        }
    }



    public function test()
    {
        dd($this->formFields);
    }


    public function updatedAmount($value)
    {

        if ($value) {

            $this->nairaAmount = $value * $this->currency->sell_price;
        } else {
            $this->nairaAmount = 0;
        }
    }

    public function validateDetails()
    {
        $this->validate([
            'amount' => 'required|numeric|min:1',
        ]);


        $this->feeAmount = ($this->fee / 100) * $this->nairaAmount;


        $this->totalAmount = $this->nairaAmount - $this->feeAmount;



        $this->showPreviewForm = true;
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

        DB::beginTransaction();

        $order_number = $this->currency->code . now()->format('YmdHis');

        // Process the order
        $order = SellFxOrder::create([
            'user_id' => auth()->id(),
            'currency_name' => $this->currency->currency,
            'currency_code' => $this->currency->code,
            'amount' => $this->amount,
            'naira_amount' => $this->totalAmount,
            'status' => SellFxOrder::STATUS_PENDING,
            'order_number' => $order_number,
            'data' => $this->dynamicFields
        ]);


        foreach ($this->formFields as $field) {
            if ($field['type'] == 'file') {
                if (isset($this->dynamicFields[$field['label']]) && $this->dynamicFields[$field['label']]) {
                    $path = $this->dynamicFields[$field['label']]->store('foreign Exchange');

                    $this->dynamicFields[$field['label']] = $path;
                    $order->data = $this->dynamicFields;
                }
            }
        }

        $order->save();

        DB::commit();

        return $this->flash('success', 'Order created successfully', [], route('bureau-da-change.sell-fx'));
    }


    public function render()
    {
        return view('livewire.sell-fx.show');
    }
}
