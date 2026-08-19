<?php

namespace App\Livewire\BuyFx;

use App\Helpers\SettingHelper;
use App\Models\BuyFxOrder;
use App\Models\ExchangeRate;
use App\Models\FxFormField;
use App\Models\User;
use App\Models\UserTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;

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

        $this->fee = SettingHelper::get(SettingHelper::BUREAU_DA_CHANGE_BUY_FEE, 10);

        // Get fields specific to this currency for buy form
        $this->formFields = FxFormField::forFormType('buy')
            ->forCurrency($currencyCode)
            ->get()
            ->toArray();

        foreach ($this->formFields as $field) {
            $this->dynamicFields[$field['label']] = '';
        }
    }

    public function render()
    {
        return view('livewire.buy-fx.show');
    }

    public function test()
    {
        dd($this->formFields);
    }


    public function updatedAmount($value)
    {

        if ($value) {

            $this->nairaAmount = $value * $this->currency->price;
        } else {
            $this->nairaAmount = 0;
        }
    }

    public function validateDetails()
    {
        $this->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        if ($this->amount > $this->currency->volume) {
            $this->addError('amount', 'This amount is more than the available volume');
            return;
        }

        $this->feeAmount = ($this->fee / 100) * $this->nairaAmount;


        $this->totalAmount = $this->nairaAmount + $this->feeAmount;

        if ($this->totalAmount > auth()->user()->wallet_balance) {
            $this->alert('error', 'Insufficient balance');
            return;
        }


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

        // dd($this->dynamicFields);

        DB::beginTransaction();

        $order_number = $this->currency->code . now()->format('YmdHis');

        // Process the order
        $order = BuyFxOrder::create([
            'user_id' => auth()->id(),
            'currency_name' => $this->currency->currency,
            'currency_code' => $this->currency->code,
            'amount' => $this->amount,
            'naira_amount' => $this->totalAmount,
            'status' => BuyFxOrder::STATUS_PENDING,
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

        auth()->user()->decrement('wallet_balance', $this->totalAmount);


        $formattedAmount = number_format($this->amount, 2);
        $formattedTotalAmount = number_format($this->totalAmount, 2);

        $transaction_message = "You bought {$formattedAmount} {$this->currency->currency} for N{$formattedTotalAmount}";

        if ($this->dynamicFields && count($this->dynamicFields) > 0 && isset($this->dynamicFields['Account Name']) && isset($this->dynamicFields['Account Number']) && isset($this->dynamicFields['Bank Name'])) {
            $transaction_message .= " (Account Name: {$this->dynamicFields['Account Name']}, Account Number: {$this->dynamicFields['Account Number']}, Bank: {$this->dynamicFields['Bank Name']})";
        }

        auth()->user()->transactions()->create([
            'type' => UserTransaction::TYPE_DEBIT,
            'amount' => $this->totalAmount,
            'message' => $transaction_message,
            'source' => UserTransaction::SOURCE_BUREAU_DA_CHANGE,
            'status' => UserTransaction::STATUS_SUCCESSFUL
        ]);

        DB::commit();

        return $this->flash('success', 'Order created successfully', [], route('bureau-da-change.buy-fx'));
    }
}
