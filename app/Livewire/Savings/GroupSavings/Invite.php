<?php

namespace App\Livewire\Savings\GroupSavings;

use App\Models\GroupSavings;
use App\Models\GroupSavingsMembers;
use App\Notifications\GroupSavingsInviteAccepted;
use App\Notifications\GroupSavingsInviteDeclined;
use App\Services\HashIdService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Invite extends Component
{
    use LivewireAlert;

    public $savings;

    public $isAleardyAMember = false;


    public function mount($hashId)
    {
        $this->savings = (new HashIdService)->getRecord($hashId, GroupSavings::class);

        $this->isAleardyAMember = $this->savings->members->contains(auth()->id());


        // $this->isAleardyAMember = $this->savings->members->contains(auth()->id()) && $this->savings->members->firstWhere('id', auth()->id())->pivot->status === GroupSavingsMembers::STATUS_INVITED;
    }

    public function render()
    {
        return view('livewire.savings.group-savings.invite')->title('Group Savings Invite');
    }

    public function accept()
    {

        DB::beginTransaction();

        GroupSavingsMembers::updateOrCreate([
            'user_id' => auth()->id(),
            'group_savings_id' => $this->savings->id
        ], [
            'status' => GroupSavingsMembers::STATUS_ACTIVE,
            'role' => GroupSavingsMembers::ROLE_MEMBER
        ]);

        $this->savings->activities()->create([
            'message' => auth()->user()->name . " accepted the group savings invite",
        ]);

        DB::commit();

        Notification::send($this->savings->members, new GroupSavingsInviteAccepted(auth()->user(), $this->savings));

        return $this->flash('success', "Group savings invite accepted successfully", [], route('savings.group-savings'));
    }

    public function decline()
    {
        $member  =  GroupSavingsMembers::updateOrCreate([
            'user_id' => auth()->id(),
            'group_savings_id' => $this->savings->id
        ], [
            'status' => GroupSavingsMembers::STATUS_DECLINED,
            'role' => GroupSavingsMembers::ROLE_MEMBER
        ]);

        // if ($member) {
        //     $member->delete();
        // }

        Notification::send($this->savings->members, new GroupSavingsInviteDeclined(auth()->user(), $this->savings));

        return $this->flash('success', "Group savings invite declined", [], route('savings.group-savings'));
    }
}
