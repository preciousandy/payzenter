<?php

namespace App\Livewire\SwiftPay;

use App\Helpers\SettingHelper;
use App\Models\Beneficiary;
use App\Models\Transfer;
use App\Models\UserTransaction;
use App\Models\MerchantKyb;
use App\Services\HashIdService;
use App\Services\TransferService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use KingFlamez\Rave\Facades\Rave as Flutterwave;


class Bank extends Component
{
    use LivewireAlert;

    public $banks = [];
    public $amount, $pin;
    public $bank_code = "";
    public $bank_id;
    public $account_name = "";
    public $bank_account_number = "";
    public $showPreviewForm = false;
    public $bank_name = "";
    public $description = "";
    public $fee;
    public $feeAmount;
    public $totalAmount;

    public $bank;

    public $ifAddtoBeneficiary = false;

    public $isABeneficiary = false;

    public $recentTransfers  = [];

    public $source_link;
    public $settlement_only = true;


    public function mount()
    {
        $userTransfers = auth()->user()->bankTransfers()->where('type', Transfer::TYPE_BANK)->latest()->limit(5)->get();
        $transferService = new TransferService();

        foreach ($userTransfers as $tx) {
            if ($tx->status === Transfer::STATUS_PENDING && ($tx->transfer_id || $tx->reference)) {
                $provider = strtolower((string) data_get($tx->response_data, 'provider', 'palmpay'));
                try {
                    if ($provider === 'palmpay') {
                        $res = $transferService->checkPalmPayTransferStatus((string) $tx->transfer_id, (string) $tx->reference);
                    } elseif ($provider === 'flutterwave') {
                        $res = $transferService->checkFlutterwaveTransferStatus((string) $tx->transfer_id, (string) $tx->reference);
                    } else {
                        $res = $transferService->checkDotTransferStatus((string) $tx->transfer_id, (string) $tx->reference);
                    }

                    if (isset($res['state']) && in_array($res['state'], [Transfer::STATUS_SUCCESS, Transfer::STATUS_FAILED], true)) {
                        $tx->update([
                            'status' => $res['state'],
                            'last_status_check_at' => now(),
                        ]);
                    }
                } catch (\Throwable $e) {
                    Log::warning("Error syncing transfer status: " . $e->getMessage());
                }
            }
        }

        $this->recentTransfers = auth()->user()->bankTransfers()->where('type', Transfer::TYPE_BANK)->latest()->limit(5)->get()->unique('account_number');

        $this->source_link = request()->query('source_link');

        if ($this->settlement_only) {
            $kyb = auth()->user()->merchantKyb;
            if (!$kyb || !$kyb->settlement_bank_id || !$kyb->settlement_account_number || !$kyb->settlement_account_name) {
                $this->flash('error', 'Please complete your settlement account on onboarding', [], route('onboarding'));
                return;
            }

            if ($kyb->is_settlement_suspended) {
                $this->flash('error', 'Your settlement account has been suspended by an administrator. Please contact support.', [], route('dashboard'));
                return;
            }

            $this->bank_id = $kyb->settlement_bank_id;
            $this->bank_code = $kyb->settlement_bank_id;
            $this->bank_account_number = $kyb->settlement_account_number;
            $this->account_name = $kyb->settlement_account_name;
            $this->bank_name = $kyb->settlement_bank_name ?? '';
            $this->bank = [
                'code' => $kyb->settlement_bank_id,
                'name' => $kyb->settlement_bank_name ?? '',
            ];
        } else {
            // get beneficiary details from route params
            if (request()->has('beneficiary')) {
                $beneficiary = (new HashIdService)->getRecord(request('beneficiary'), Beneficiary::class);

                $this->bank_account_number = $beneficiary->acct_number;
                $this->account_name = $beneficiary->acct_name;
                $this->bank_name = $beneficiary->bank_name;
                $this->bank_code = $beneficiary->bank_code;
                $this->bank = [
                    'code' => $beneficiary->bank_code,
                    'name' => $beneficiary->bank_name,
                ];

                $this->isABeneficiary = auth()->user()->beneficiaries()->where('type', Beneficiary::TYPE_BANK)->where('acct_number', $this->bank_account_number)->exists();
            }

            // get transfer details from route params
            if (request()->has('transfer')) {
                $transfer = (new HashIdService)->getRecord(request('transfer'), Transfer::class);

                $this->bank_account_number = $transfer->account_number;
                $this->account_name = $transfer->account_name;
                $this->bank_name = $transfer->bank_name;
                $this->bank_code = $transfer->bank_code;
                $this->bank_id = $transfer->bank_code;
                $this->bank = [
                    'code' => $transfer->bank_code,
                    'name' => $transfer->bank_name,
                ];
            }
        }

        // cache banks for 2 days
        $this->banks  = cache()->remember('banks', 60 * 60 * 24 * 2, function () {
            return getDotBanks();
        });


        // for testing
        // $this->amount = 15_000;
        // $this->totalAmount = 15_000;
        // $this->description = "Testing bank transfer";
    }
    public function render()
    {
        return view('livewire.swift-pay.bank')->title('Bank Transfer');
    }

    // public function updatedBank()
    // {
    //     dd('yes');
    // }

    public function test()
    {
        $transferService = new TransferService();

        $response = $transferService->checkDotTransferStatus('5197853', '090470250523204535296879509796');

        dd($response);
    }

    public function confirmUser(TransferService $transferService)
    {
        if ($this->settlement_only) {
            return;
        }

        $this->validate([
            'bank' => 'required',
            'bank_account_number' => 'required|numeric|digits:10',
        ]);

        $this->bank_name = $this->bank['name'];
        // $this->bank = collect($this->banks)->firstWhere('id', $this->bank_id);

        // $this->bank_name = $this->bank['name'];
        // $this->bank_code = $this->bank['code'];

        // $this->validate([
        //     'bank_code' => 'required',
        //     'bank_account_number' => 'required|numeric|digits:10',
        // ]);

        $response = $transferService->getDotAccount($this->bank_id, $this->bank_account_number);

        $this->isABeneficiary = auth()->user()->beneficiaries()->where('type', Beneficiary::TYPE_BANK)->where('acct_number', $this->bank_account_number)->exists();

        if ($response && $response['status'] == 'success') {
            $this->account_name = $response['data']['accountName'];
        } else {
            $errorMessage = $response['message'] ?? 'Unable to verify account details';
            $this->alert('error', $errorMessage);
        }
    }

    public function submit()
    {
        $this->validate([
            'bank_code' => 'nullable',
            'bank_id' => 'required',
            'bank_account_number' => 'required|numeric|digits:10',
            'amount' => 'required|numeric|min:100',
            'description' => 'nullable',
        ]);


        $this->feeAmount = getTotalFee(
            $this->amount,
            SettingHelper::get(SettingHelper::WITHDRAWAL_FIXED_FEE, 0),
            SettingHelper::get(SettingHelper::WITHDRAWAL_PERCENTAGE_FEE, 0)
        );

        $this->totalAmount = $this->amount + $this->feeAmount;

        if ($this->totalAmount > auth()->user()->wallet_balance) {
            $this->addError('amount', 'Insufficient funds');
            return;
        }


        $this->showPreviewForm = true;
    }


    public function transfer()
    {
        $data =   $this->validate([
            'bank_code' => 'nullable',
            'bank_id' => 'required',
            'bank_account_number' => 'required|numeric|digits:10',
            'amount' => 'required|numeric|min:100',
            'description' => 'nullable',
            'pin' => 'required|integer|digits:4',
        ]);

        if (!Hash::check($this->pin, auth()->user()->pin)) {
            $this->alert('error', 'Incorrect pin');
            return;
        }

        $reference = 'PZ_' . time() . '_' . Str::lower(Str::random(8));

        $transferService = new TransferService();

        try {
            $transferReference = null;
            $provider = $transferService->resolveTransferProvider();
            $transferStatus = Transfer::STATUS_PENDING;

            if ($provider === 'palmpay') {
                $transferReference = $transferService->initiatePalmPayTransfer(
                    $this->bank_id,
                    $this->bank_account_number,
                    $this->account_name,
                    $this->amount,
                    $reference,
                    (string) $this->description
                );

                if (($transferReference['state'] ?? null) === 'FAILED') {
                    Log::warning("PalmPay payout failed (" . ($transferReference['message'] ?? '') . "). Attempting Flutterwave fallback...");
                    if ($transferService->canUseFlutterwaveTransfer()) {
                        $transferReference = $transferService->initiateFlutterwaveTransfer(
                            $this->bank_id,
                            $this->bank_account_number,
                            $this->account_name,
                            $this->amount,
                            $reference,
                            (string) $this->description
                        );
                        $provider = 'flutterwave';
                    }
                }
            } elseif ($provider === 'dot') {
                $transferReference = $transferService->initaiteDotTransfer($this->bank_id, $this->bank_account_number, $this->account_name, $this->amount, $reference, $this->description,  $this->bank['name']);
            } else {
                $transferReference = $transferService->initiateFlutterwaveTransfer($this->bank_id, $this->bank_account_number, $this->account_name, $this->amount, $reference, (string) $this->description);
            }

            $providerState = is_array($transferReference) ? ($transferReference['state'] ?? null) : null;
            if ($providerState === null) {
                $message = is_array($transferReference) ? ($transferReference['message'] ?? null) : null;
                throw new \Exception($message ?: 'Transfer failed. Please try again later.');
            }

            if ($provider === 'dot') {
                if ($providerState === Transfer::DOT_TRANSFER_STATUS_SUCCEEDED) {
                    $transferStatus = Transfer::STATUS_SUCCESS;
                } elseif ($providerState === Transfer::DOT_TRANSFER_STATUS_FAILED || $providerState === Transfer::STATUS_FAILED) {
                    $transferStatus = Transfer::STATUS_FAILED;
                } else {
                    $transferStatus = Transfer::STATUS_PENDING;
                }
            } else {
                if ($providerState === Transfer::STATUS_SUCCESS || $providerState === 'SUCCESSFUL') {
                    $transferStatus = Transfer::STATUS_SUCCESS;
                } elseif ($providerState === Transfer::STATUS_FAILED || $providerState === 'FAILED') {
                    $transferStatus = Transfer::STATUS_FAILED;
                } else {
                    $transferStatus = Transfer::STATUS_PENDING;
                }
            }

            if ($transferStatus !== Transfer::STATUS_FAILED) {

                DB::beginTransaction();

                $transferReferenceValue = (string) ($transferReference['receiptNumber'] ?? $reference);

                $transfer = Transfer::create([
                    'user_id' => auth()->id(),
                    'reference' => $transferReferenceValue,
                    'status' => $transferStatus,
                    'amount' => $this->amount,
                    'fee' => $this->feeAmount,
                    'description' => $this->description,
                    'type' => 'bank',
                    'account_name' => $this->account_name,
                    'account_number' => $this->bank_account_number,
                    'bank_name' => $this->bank['name'],
                    'bank_code' => $this->bank_id,
                    'transfer_id' => $transferReference['requestId'] ?? null,
                    'response_data' => [
                        'provider' => $provider,
                        'raw' => $transferReference['raw'] ?? null,
                    ],
                    'source_link' => $this->source_link,
                ]);


                auth()->user()->decrement('wallet_balance', $this->totalAmount);

                auth()->user()->transactions()->create([
                    'reference' => $transferReferenceValue,
                    'type' => UserTransaction::TYPE_DEBIT,
                    'amount' => $this->totalAmount,
                    'source' => UserTransaction::SOURCE_BANK_TRANSFER,
                    'status' => $transferStatus === Transfer::STATUS_SUCCESS
                        ? UserTransaction::STATUS_SUCCESSFUL
                        : UserTransaction::STATUS_PENDING,
                    'message' => "Transfered {$this->amount} to {$this->account_name} {$this->bank_account_number}",
                    'fee' => $this->feeAmount
                ]);

                if ($this->ifAddtoBeneficiary) {
                    Beneficiary::create([
                        'user_id' => auth()->id(),
                        'type' => Beneficiary::TYPE_BANK,
                        'acct_name' => $this->account_name,
                        'acct_number' => $this->bank_account_number,
                        'bank_name' => $this->bank_name,
                        'bank_code' => $this->bank['code'],
                    ]);
                }

                DB::commit();

                $this->flash(
                    'success',
                    $transferStatus === Transfer::STATUS_SUCCESS ? 'Transfer successful' : 'Transfer initiated successfully',
                    [],
                    route('transfer.receipt', $transfer->hash_id)
                );
            } else {

                $error_message = $transferReference['message'] ?? 'Transfer failed. Please try again later.';

                throw new \Exception($error_message);

                // $this->alert('error', $error_message);
            }
        } catch (\Throwable $th) {
            $this->alert('error', $th->getMessage());
        }
    }
}
