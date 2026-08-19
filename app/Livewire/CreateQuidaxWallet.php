<?php

namespace App\Livewire;

use App\Services\UserService;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class CreateQuidaxWallet extends Component
{
    use LivewireAlert;

    public function render()
    {
        return view('livewire.create-quidax-wallet');
    }

    public function create()
    {
        $userService = new UserService();

        $response = $userService->generateQuidaxWallet(auth()->user());

        if ($response['status'] == 'error') {
            return $this->alert('error', $response['message']);
        }

        $quidax_wallet_id = $response['data']['id'];
        $quidax_sn = $response['data']['sn'];
        $quidax_email = $response['data']['email'];

        $user = auth()->user();

        DB::beginTransaction();


        $user->update([
            'quidax_id' => $quidax_wallet_id,
            'quidax_sn' => $quidax_sn,
            'quidax_email' => $quidax_email,
        ]);

        DB::commit();

        return $this->flash('success', 'Crypto wallet created successfully', [], route('home'));
    }
}
