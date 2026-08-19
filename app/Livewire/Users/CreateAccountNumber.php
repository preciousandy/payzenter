<?php

namespace App\Livewire\Users;

use App\Services\UserService;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class CreateAccountNumber extends Component
{
    use LivewireAlert;

    public $email, $bvn;

    public function mount()
    {

        if (auth()->user()->virtual_account_number) {
            return redirect()->route('home')->with('error', 'Account number already created');
        }
        $this->fill([
            'email' => auth()->user()->email,
            'bvn' => auth()->user()->bvn,
        ]);
    }

    public function submit(UserService $userService)
    {
        $this->validate([
            'email' => 'required|email',
            'bvn' => 'required|digits:11',
        ]);

        $data = $userService->generateAccountNumber(auth()->user(), $this->bvn);

        if ($data['status'] == 'error') {
            return $this->alert('error', $data['message']);
        }


        $account_number = $data['data']['account_number'];
        $bank_name = $data['data']['bank_name'];

        $response = $userService->generateQuidaxWallet(auth()->user());

        if ($response['status'] == 'error') {
            return $this->alert('error', $response['message']);
        }


        $quidax_wallet_id = $response['data']['id'];
        $quidax_sn = $response['data']['sn'];

        DB::beginTransaction();

        auth()->user()->update([
            'virtual_account_number' => $account_number,
            'virtual_bank_name' => $bank_name,
            'quidax_id' => $quidax_wallet_id,
            'quidax_sn' => $quidax_sn
        ]);

        DB::commit();

        return $this->flash('success', 'Account number created successfully', [], route('home'));
    }

    public function render()
    {
        return view('livewire.users.create-account-number')->extends('layouts.auth')->title('Create Account Number');
    }
}
