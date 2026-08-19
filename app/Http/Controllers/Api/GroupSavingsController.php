<?php

namespace App\Http\Controllers\Api;

use App\Helpers\SettingHelper;
use App\Http\Controllers\Controller;
use App\Models\GroupSavings;
use App\Models\GroupSavingsDonation;
use App\Models\GroupSavingsMembers;
use App\Models\UserTransaction;
use App\Notifications\GroupSavingsInviteAccepted;
use App\Notifications\GroupSavingsInviteDeclined;
use App\Services\HashIdService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class GroupSavingsController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        try {
            $data['total_group_savings_amount'] =  auth()->user()->groupSavingsDonations->sum('amount');
            $data['group_savings'] = auth()->user()->groupSavings()->withCount('members')->get();

            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function history(Request $request)
    {
        try {
            $data = auth()->user()->closedGroupSavings()->withCount('members')->get();

            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function create(Request $request)
    {
        try {
            $data['group_savings_types'] = [
                GroupSavings::TYPE_STRICT,
                GroupSavings::TYPE_FLEXIBLE
            ];

            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:5|max:255',
            'type' => 'required|string|in:strict,flexible',
            'description' => 'required|string|min:5',
            'end_date' => 'required|date',
            'target_amount' => 'nullable|numeric|min:100',
            'member_target_amount' => 'nullable|numeric|min:100',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        // if (!Hash::check($request->pin, auth()->user()->pin)) {
        //     $validator->errors()->add('pin', 'Incorrect PIN');
        //     return $this->validationError($validator);
        // }

        try {
            DB::beginTransaction();

            $groupSavings = GroupSavings::create([
                'name' => $request->name,
                'description' => $request->description,
                'type' => $request->type,
                'end_date' => $request->end_date,
                'target_amount' => $request->target_amount ?? null,
                'member_target_amount' => $request->member_target_amount ?? null,
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

            return $this->success($groupSavings,  'Group Savings created successfully');
        } catch (\Exception $th) {
            return $this->error($th->getMessage());
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $groupSavings = GroupSavings::find($id);

            if (!$groupSavings) {
                return $this->error('Group Savings not found', 404);
            }

            $data['is_admin'] = $groupSavings->members->find(auth()->id())->pivot->role === GroupSavingsMembers::ROLE_ADMIN;
            $data['withdrawal_fee'] = (int) SettingHelper::get(SettingHelper::GROUP_SAVINGS_WITHDRAWAL_FEE, 0);
            $data['group_savings'] = $groupSavings->makeHidden('members');
            $data['members'] = $groupSavings->members()->get()->each(function ($member) use ($groupSavings) {
                $member->total_donations = GroupSavingsDonation::where('group_savings_id', $groupSavings->id)
                    ->where('user_id', $member->id)
                    ->sum('amount');
            });
            $data['activities'] = $groupSavings->activities()->latest()->get();


            return $this->success($data, 'Group savings fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function invitation(Request $request, $invite_code)
    {
        try {
            $groupSavings = (new HashIdService)->getRecord($invite_code, GroupSavings::class);

            if (!$groupSavings) {
                return $this->error('Group Savings not found', 404);
            }

            $data['group_savings'] = $groupSavings->load('members');

            return $this->success($data, 'Group savings invitation details fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function respondToInvitation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_savings_id' => 'required|exists:group_savings,id',
            'response' => 'required|in:accept,decline',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $groupSavings = GroupSavings::find($request->group_savings_id);

            if (!$groupSavings) {
                return $this->error('Group Savings not found', 404);
            }

            if ($request->response === 'accept') {
                DB::beginTransaction();

                GroupSavingsMembers::updateOrCreate([
                    'user_id' => auth()->id(),
                    'group_savings_id' => $groupSavings->id
                ], [
                    'status' => GroupSavingsMembers::STATUS_ACTIVE,
                    'role' => GroupSavingsMembers::ROLE_MEMBER
                ]);

                $groupSavings->activities()->create([
                    'message' => auth()->user()->name . " accepted the group savings invite",
                ]);

                DB::commit();

                Notification::send($groupSavings->members, new GroupSavingsInviteAccepted(auth()->user(), $groupSavings));

                return $this->success($groupSavings, 'Group savings invitation accepted successfully');
            }

            if ($request->response === 'decline') {
                $member  =  GroupSavingsMembers::updateOrCreate([
                    'user_id' => auth()->id(),
                    'group_savings_id' => $groupSavings->id
                ], [
                    'status' => GroupSavingsMembers::STATUS_DECLINED,
                    'role' => GroupSavingsMembers::ROLE_MEMBER
                ]);

                Notification::send($groupSavings->members, new GroupSavingsInviteDeclined(auth()->user(), $groupSavings));

                return $this->success($groupSavings, 'Group savings invitation rejected successfully');
            }
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function addMoney(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_savings_id' => 'required|exists:group_savings,id',
            'amount' => 'required|numeric|min:100',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }


        if ($request->amount > auth()->user()->wallet_balance) {
            $validator->errors()->add('amount', 'Insufficient funds');
            return $this->validationError($validator);
        }


        try {
            $groupSavings = GroupSavings::find($request->group_savings_id);

            if (!$groupSavings) {
                return $this->error('Group Savings not found', 404);
            }

            if ($groupSavings->type === GroupSavings::TYPE_STRICT) {
                $amountDonated = $groupSavings->donations()->where('user_id', auth()->id())->sum('amount');

                $errorMessage = 'You are exceeding the member  target amount, pls add amount less than ' . number_format($groupSavings->member_target_amount - $amountDonated);

                if ($amountDonated + $request->amount > $groupSavings->member_target_amount) {
                    return $this->error($errorMessage);
                }
            }


            DB::beginTransaction();

            $groupSavings->increment('balance', $request->amount);

            auth()->user()->decrement('wallet_balance', $request->amount);


            // record activity
            $groupSavings->activities()->create([
                'message' => auth()->user()->name .  ' added ₦' . number_format($request->amount) . ' to the group savings',
                'user_id' => auth()->id()
            ]);

            // record user transaction
            auth()->user()->transactions()->create([
                'amount' => $request->amount,
                'type' => UserTransaction::TYPE_DEBIT,
                'message' => 'Added money my into ' . $groupSavings->name . ' group savings account',
                'status' => UserTransaction::STATUS_SUCCESSFUL,
                'source' => UserTransaction::SOURCE_GROUP_SAVINGS,
                'fee' => 0
            ]);

            $groupSavings->donations()->create([
                'user_id' => auth()->id(),
                'amount' => $request->amount
            ]);

            DB::commit();

            return $this->success($groupSavings, 'Money added to group savings successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function withdrawMoney(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'group_savings_id' => 'required|exists:group_savings,id',
            'amount' => 'required|numeric|min:100',
            'reason' => 'required|string|min:5',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }



        try {
            $groupSavings = GroupSavings::find($request->group_savings_id);

            if (!$groupSavings) {
                return $this->error('Group Savings not found', 404);
            }

            if ($request->amount > $groupSavings->balance) {
                $validator->errors()->add('amount', 'Insufficient funds in group savings');
                return $this->validationError($validator);
            }

            if ($groupSavings->members->find(auth()->id())->pivot->role !== GroupSavingsMembers::ROLE_ADMIN) {
                return $this->error('Only the group admin can withdraw from the group savings');
            }

            $withdrawalFee = (int) SettingHelper::get(SettingHelper::GROUP_SAVINGS_WITHDRAWAL_FEE, 0);

            $amountToWithdraw = $request->amount - $withdrawalFee;

            DB::beginTransaction();

            $groupSavings->decrement('balance', $request->amount);

            $groupSavings->activities()->create([
                'message' => auth()->user()->name . " withdrew ₦{$request->amount} from the group savings account due to: {$request->reason}",
                'user_id' => auth()->id()
            ]);

            auth()->user()->increment('wallet_balance', $amountToWithdraw);

            auth()->user()->transactions()->create([
                'amount' => $amountToWithdraw,
                'type' => UserTransaction::TYPE_CREDIT,
                'message' => 'Withdrew ' . $request->amount . ' from ' . $groupSavings->name .  ' group  savings account',
                'status' => UserTransaction::STATUS_SUCCESSFUL,
                'source' => UserTransaction::SOURCE_GROUP_SAVINGS,
                'fee' => $withdrawalFee
            ]);

            DB::commit();

            return $this->success($groupSavings, 'Money withdrawn from group savings successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function updateGroupSavings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_savings_id' => 'required|exists:group_savings,id',
            'name' => 'required|min:5|max:255',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $groupSavings = GroupSavings::find($request->group_savings_id);

            if (!$groupSavings) {
                return $this->error('Group Savings not found', 404);
            }

            if ($groupSavings->members->find(auth()->id())->pivot->role !== GroupSavingsMembers::ROLE_ADMIN) {
                return $this->error('Only the group admin can update the group savings');
            }

            $groupSavings->update([
                'name' => $request->name
            ]);

            return $this->success($groupSavings, 'Group savings updated successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function makeMemberAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_savings_id' => 'required|exists:group_savings,id',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $groupSavings = GroupSavings::find($request->group_savings_id);

            if (!$groupSavings) {
                return $this->error('Group Savings not found', 404);
            }

            if ($groupSavings->members->find(auth()->id())->pivot->role !== GroupSavingsMembers::ROLE_ADMIN) {
                return $this->error('Only the group admin can make a member an admin');
            }

            $groupSavings->members()->updateExistingPivot($request->user_id, ['role' => GroupSavingsMembers::ROLE_ADMIN]);

            return $this->success($groupSavings, 'User is now an admin');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function removeMember(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_savings_id' => 'required|exists:group_savings,id',
            'user_id' => 'required|exists:users,id',
            'reason' => 'required|string|min:5',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $groupSavings = GroupSavings::find($request->group_savings_id);

            if (!$groupSavings) {
                return $this->error('Group Savings not found', 404);
            }

            if ($groupSavings->members->find(auth()->id())->pivot->role !== GroupSavingsMembers::ROLE_ADMIN) {
                return $this->error('Only the group admin can remove a member');
            }

            $user = $groupSavings->members->find($request->user_id);

            // $groupSavings->members()->detach($request->user_id);

            DB::beginTransaction();
            $groupSavings->members()->updateExistingPivot($user->id, ['status' => GroupSavingsMembers::STATUS_REMOVED]);

            $groupSavings->activities()->create([
                'message' => $user->name . ' was removed from the group savings by ' . auth()->user()->name  . ' due to: ' . $request->reason,
                'user_id' => auth()->id()
            ]);

            DB::commit();

            Notification::send($user, new \App\Notifications\GroupSavingsMemberRemoved($groupSavings));

            return $this->success($groupSavings, 'User removed from group savings');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function endGroupSavings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_savings_id' => 'required|exists:group_savings,id',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }



        try {
            $groupSavings = GroupSavings::find($request->group_savings_id);

            if (!$groupSavings) {
                return $this->error('Group Savings not found', 404);
            }

            if ($groupSavings->members->find(auth()->id())->pivot->role !== GroupSavingsMembers::ROLE_ADMIN) {
                return $this->error('Only the group admin can end the group savings');
            }

            if ($groupSavings->balance > 0) {
                return $this->error('You cannot close this group savings because it has a balance');
            }

            DB::beginTransaction();

            $groupSavings->update([
                'status' => GroupSavings::STATUS_CLOSED
            ]);

            $groupSavings->activities()->create([
                'message' => auth()->user()->name . ' closed this group',
                'user_id' => auth()->id()
            ]);

            DB::commit();

            return $this->success($groupSavings, 'Group savings closed successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
