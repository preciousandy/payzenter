<?php

namespace App\Livewire\Savings\GroupSavings;

use App\Models\GroupSavings;
use App\Models\GroupSavingsMembers;
use App\Models\User;
use App\Notifications\GroupSavingsInvite;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Create extends Component
{
    use LivewireAlert;


    public $name, $description,  $end_date, $target_amount;

    public $type = 'strict';

    public $members = [];

    public $username, $member_target_amount;

    public function addMember()
    {
        $this->validate(
            [
                'username' => 'required|exists:users,username'
            ],
            [
                'username.exists' => 'The user does not exist'
            ]
        );

        $user = User::firstWhere('username', $this->username);

        $this->members[] = $user;

        $this->username = '';
    }


    public function render()
    {
        return view('livewire.savings.group-savings.create')->title('Create Group Savings');
    }

    public function create()
    {
        $data =  $this->validate([
            'name' => 'required|min:5|max:255',
            'description' => 'required|string|min:5',
            'type' => 'required|string',
            // 'members' => 'required|array'
        ]);

        DB::beginTransaction();

        $groupSavings = GroupSavings::create([
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'end_date' => $this->end_date,
            'target_amount' => $this->target_amount,
            'member_target_amount' => $this->member_target_amount,
            'balance' => 0,
            'status' => GroupSavings::STATUS_ACTIVE,
        ]);

        GroupSavingsMembers::create([
            'user_id' => auth()->id(),
            'group_savings_id' => $groupSavings->id,
            'status' => GroupSavingsMembers::STATUS_ACTIVE,
            'role' => GroupSavingsMembers::ROLE_ADMIN,
        ]);



        DB::commit();

        return $this->flash('success', 'Group savings created successfully', [], route('savings.group-savings.success', $groupSavings->hash_id));
    }
}
