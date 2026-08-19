<?php

namespace App\Livewire;

use App\Models\UserVirtualAccount;
use App\Services\UserService;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class CreateVirtualAccount extends Component
{
    use LivewireAlert;

    public function render()
    {
        return view('livewire.create-virtual-account');
    }

    public function create()
    {
        $userService = new UserService();

        $response = $userService->generateDotVirtualAccount(auth()->user());

        if ($response['status'] == 'error') {
            return $this->alert('error', $response['message']);
        }

        $virtual_account = $response['data'];


        $user = auth()->user();

        DB::beginTransaction();

        UserVirtualAccount::create([
            'user_id' => $user->id,
            'account_number' => $virtual_account['accountNo'],
            "external_id" => $virtual_account['externalId'],
            "account_name" => $virtual_account["firstname"] . " " . $virtual_account["lastname"],
            "bank_code" => "999357",
            "bank_name" => "DOT MFB"
        ]);

        DB::commit();

        return $this->flash('success', 'Virtual Account created successfully', [], route('home'));
    }
}
