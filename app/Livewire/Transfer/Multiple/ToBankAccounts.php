<?php

namespace App\Livewire\Transfer\Multiple;

use App\Helpers\SettingHelper;
use App\Jobs\TransferToBank;
use App\Livewire\Transfer\Multiple;
use App\Models\MultipleTransfer;
use App\Models\Transfer;
use App\Models\UserTransaction;
use App\Services\TransferService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use KingFlamez\Rave\Facades\Rave as Flutterwave;
use PhpParser\Node\Expr\AssignOp\Mul;

class ToBankAccounts extends Component
{
    use LivewireAlert;

    public $bankAccounts = [];

    public $amount, $description;

    public $totalAmount = 0;
    public $fee;
    public $feeAmount;
    public $query;
    public $banks = [];

    public $bank;
    public $account_number;
    public $bank_code = "";
    public $bank_id;
    public $account_name = "";
    public $bank_account_number = "";
    public $bank_name = "";

    // dfs23fhr7ntg0293039_PMCKDU_4

    public $pin;
    public $showPreviewForm = false;

    public $showSuccessfullModal = false;

    public $transaction;

    public $source_link;

    public $totalAndFee = 0;


    public function mount()
    {
        // cache banks for 2 days
        $this->banks  = cache()->remember('banks', 60 * 60 * 24 * 2, function () {
            return getDotBanks();
        });

        $this->fee = SettingHelper::get(SettingHelper::MULTIPLE_TRANSFER_FIXED_FEE);


        $this->source_link = request()->query('source_link');
        // dd($this->banks);
    }


    public function confirmUser(TransferService $transferService)
    {

        $this->validate([
            'bank' => 'required',
            'account_number' => 'required|numeric|digits:10',
        ]);



        $this->bank_name = $this->bank['name'];


        $response = $transferService->getDotAccount($this->bank_id, $this->account_number);



        if ($response && $response['status'] == 'success') {
            $this->account_name = $response['data']['accountName'];
        } else {
            $errorMessage = $response['message'] ?? 'Unable to verify account details';
            $this->alert('error', $errorMessage);
        }
    }

    public function clearDetails()
    {
        $this->reset(['account_number', 'account_name', 'bank_code', 'bank_id', 'amount', 'description']);
    }

    public function addAccount()
    {
        $this->validate([
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:255',
        ]);

        // check if account details is already added
        $account = collect($this->bankAccounts)->where('account_number', $this->account_number)->where('bank_code', $this->bank_code)->first();


        if ($account) {
            return $this->alert('error', 'Account already added');
        }

        $this->bankAccounts[] = [
            'account_number' => $this->account_number,
            'account_name' => $this->account_name,
            'bank_code' => $this->bank_id,
            'bank_name' => $this->bank['name'],
            'amount' => $this->amount,
            'description' => "SWPY/" . $this->description,
            'fee' => $this->fee,
        ];

        $this->reset(['account_number', 'account_name', 'amount', 'description']);

        $this->totalAmount = collect($this->bankAccounts)->sum('amount');

        $this->feeAmount  = collect($this->bankAccounts)->sum('fee');

        $this->dispatch('close-modal');
    }

    public function confirm()
    {
        if (count($this->bankAccounts) < 2) {
            return $this->alert('error', 'You need to add at least 2 bank accounts');
        }

        $this->totalAndFee  = $this->totalAmount + $this->feeAmount;


        if ($this->totalAmount > auth()->user()->wallet_balance) {
            $this->alert('error', 'Insufficient funds');
            return;
        }


        $this->showPreviewForm = true;
    }

    public function removeAccount($accountNumber)
    {

        $this->bankAccounts = collect($this->bankAccounts)->filter(function ($bank) use ($accountNumber) {
            return $bank['account_number'] != $accountNumber;
        });

        $this->totalAmount = collect($this->bankAccounts)->sum('amount');
    }

    public function transfer()
    {
        $this->validate([
            'pin' => 'required|numeric|digits:4',
        ]);

        if (!Hash::check($this->pin, auth()->user()->pin)) {
            $this->alert('error', 'Incorrect pin');
            return;
        }


        $bulk_data = collect($this->bankAccounts)->map(function ($account, $index) {
            return [
                'amount' => $account['amount'],
                'description' => $account['description'],
                'bank_code' => $account['bank_code'],
                'account_number' => $account['account_number'],
                'reference' => "BKMTPL{$index}"  . time(),
                // 'currency' => 'NGN',
                'bank_name' => $account['bank_name'],
                'account_name' => $account['account_name'],
                'swiftpay_fee' => $account['fee'],

            ];
        })->toArray();

        // dd($bulk_data, $this->bankAccounts);


        try {


            DB::beginTransaction();

            $bulk_transfer_id = uniqid();

            foreach ($bulk_data as $bank) {
                $transfer = Transfer::create([
                    'user_id' => auth()->id(),
                    'reference' => $bank['reference'],
                    'status' => Transfer::STATUS_PENDING,
                    'amount' => $bank['amount'],
                    'fee' => $bank['swiftpay_fee'],
                    'description' => $bank['description'],
                    'type' => MultipleTransfer::TYPE_BANK,
                    'account_name' => $bank['account_name'],
                    'account_number' => $bank['account_number'],
                    'bank_name' => $bank['bank_name'],
                    'bank_code' => $bank['bank_code'],
                    'bulk_transfer_id' => $bulk_transfer_id
                ]);
            }


            auth()->user()->decrement('wallet_balance', $this->totalAndFee);

            auth()->user()->transactions()->create([
                'amount' => $this->totalAndFee,
                'message' => "Transferred N{$this->totalAmount} to " . $this->bankAccounts[0]['account_name'] . " and " . count($this->bankAccounts) - 1 .  " other accounts",
                'type' => UserTransaction::TYPE_DEBIT,
                'status' => UserTransaction::STATUS_SUCCESSFUL,
                'source' => UserTransaction::SOURCE_MULTIPLE_BANK_TRANSFER,
                'fee' => $this->feeAmount
            ]);

            $this->transaction = MultipleTransfer::create([
                'user_id' => auth()->id(),
                'accounts' => implode(',', array_column($this->bankAccounts, 'account_name')),
                'type' => MultipleTransfer::TYPE_BANK,
                'total_amount' => $this->totalAndFee,
                'status' => MultipleTransfer::STATUS_SUCCESSFUL,
                'reference' => "BKMTPL" . time(),
                'source_link' => $this->source_link
            ]);

            DB::commit();

            foreach ($bulk_data as $bank) {
                TransferToBank::dispatch($bank, auth()->user(),);
            }

            $this->showSuccessfullModal = true;
        } catch (\Throwable $th) {
            return $this->alert('error', $th->getMessage());
        }
    }



    public function render()
    {
        return view('livewire.transfer.multiple.to-bank-accounts');
    }
}
