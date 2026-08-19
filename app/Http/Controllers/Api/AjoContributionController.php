<?php

namespace App\Http\Controllers\Api;

use App\Helpers\SettingHelper;
use App\Http\Controllers\Controller;
use App\Models\AjoContribution;
use App\Models\AjoContributionDonation;
use App\Models\AjoContributionMember;
use App\Models\AjoContributionRound;
use App\Models\User;
use App\Models\UserTransaction;
use App\Notifications\NotifyUser;
use App\Services\HashIdService;
use App\Traits\ApiResponseTrait;
use App\Utils\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AjoContributionController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        try {
            $data['ajo_contribution_balance'] = (float) auth()->user()->ajo_contribution_wallet;

            $data['active_ajo_contributions'] = auth()->user()->ajoContributions()->active()->latest()->get();

            return $this->success($data, 'Ajo contributions fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'type' => 'required|in:personal,business',
            'frequency' => 'required|in:weekly,monthly,yearly',
            'amount' => 'required|numeric|min:100',
            'no_of_members' => 'required|numeric|min:2',
            'start_date' => 'required|date',
            'cover_image' => 'nullable|image',
        ]);


        if ($validator->fails()) {
            return $this->validationError($validator);
        }


        try {
            $cover_image = null;
            if ($request->cover_image) {
                $cover_image = $request->cover_image->store('ajo-contributions', 'public');
            }

            DB::beginTransaction();

            $ajo = AjoContribution::create([
                'name' => $request->name,
                'type' => $request->type,
                'frequency' => $request->frequency,
                'no_of_members' => $request->no_of_members,
                'amount' => $request->amount,
                'start_date' => $request->start_date,
                'current_round' => 1,
                'cover_image' => $cover_image,
                'description' => $request->description
            ]);

            if ($request->type === AjoContribution::TYPE_PERSONAL) {
                $ajo->update([
                    'next_round_date' => match ($request->frequency) {
                        AjoContribution::FREQUENCY_WEEKLY => $ajo->start_date->addWeek(),
                        AjoContribution::FREQUENCY_MONTHLY => $ajo->start_date->addMonth(),
                        default => $ajo->start_date->addYear()
                    }
                ]);
            }

            AjoContributionMember::create([
                'ajo_contribution_id' => $ajo->id,
                'user_id' => auth()->id(),
                'role' => Roles::ADMIN,
                'is_contributing' => $request->type === 'personal' ? true : false,
                'order_number' => $request->type === 'personal' ? 1 : null,
            ]);

            AjoContributionRound::create([
                'ajo_contribution_id' => $ajo->id,
                'user_id' => $request->type === 'personal' ? auth()->id() : null,
                'order' => 1,
                'balance' => 0,
                'created_at' => $request->start_date,
                'status' => AjoContributionRound::STATUS_ON_GOING
            ]);

            DB::commit();

            return $this->success($ajo, 'Ajo contributions fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ajo_contribution_id' => 'required|exists:ajo_contributions,id'
        ]);


        if ($validator->fails()) {
            return $this->validationError($validator);
        }
        try {
            $ajoContribution = AjoContribution::with('members', 'rounds')->find($request->ajo_contribution_id);

            $data['is_user_contributing'] = (bool) $ajoContribution->members()->where('user_id', auth()->id())->first()->pivot->is_contributing;

            $data['has_user_donated'] = (bool)  $ajoContribution->activeRound()->donations()->where('user_id', auth()->id())->exists();

            $data['if_auto_save'] = (bool) $ajoContribution->members()->where('user_id', auth()->id())->first()->pivot->if_auto_save ? true : false;

            $data['is_admin'] = (bool) $ajoContribution->members()->where('user_id', auth()->id())->first()->pivot->role == AjoContributionMember::ROLE_ADMIN;

            $data['if_started'] = (bool) $ajoContribution->start_date->lte(now());

            $data['is_current_round_pending'] = $ajoContribution->activeRound()->status == 'pending';

            $data['contribution_total_amount'] = (float) $ajoContribution->type === AjoContribution::TYPE_PERSONAL ? $ajoContribution->amount * $ajoContribution->members()->count() : $ajoContribution->amount *  ($ajoContribution->members()->count() - 1);

            $data['admin_user'] = $ajoContribution->members()->where('role', AjoContributionMember::ROLE_ADMIN)->first();

            $data['active_round'] = $ajoContribution->activeRound();

            $data['contribution_total_amount'] = (float) $ajoContribution->type === AjoContribution::TYPE_PERSONAL ? $ajoContribution->amount * $ajoContribution->members()->count() : $ajoContribution->amount *  ($ajoContribution->members()->count() - 1);

            $data['ajo_contribution'] = $ajoContribution->makeHidden('members', 'rounds');

            $data['rounds'] = $ajoContribution->rounds()->whereNotNull('user_id')->get();

            $activeRound = $data['active_round'];
            $adminUser = $data['admin_user'];
            $authUserId = auth()->id();

            $data['members'] =  $ajoContribution->members()
                ->orderByPivot('created_at', 'asc')
                ->get()
                ->map(function ($user) use ($ajoContribution, $activeRound, $adminUser, $authUserId) {
                    $roundStatuses = $ajoContribution->rounds->map(function ($round) use ($user, $activeRound, $adminUser, $authUserId) {
                        $hasDonated = $round->donations()->where('user_id', $user->id)->exists();
                        $isCurrentUser = $user->id == $authUserId;
                        $isAdminUser = $user->id == $adminUser->id;
                        $joinedLate = $round->order < $activeRound->order && $round->updated_at < $user->pivot->created_at;
                        $isAdmin = $user->pivot->role === 'admin'; // Assuming 'admin' is the role name

                        $status = 'future'; // Default status

                        if ($round->order > $activeRound->order) {
                            $status = 'future'; // Yellow
                        } elseif ($joinedLate) {
                            $status = 'joined_late'; // Gray
                        } elseif ($hasDonated) {
                            $status = 'paid'; // Green
                        } elseif ($round->order <= $activeRound->order && !$hasDonated) {
                            if ($isAdmin || $isCurrentUser || $isAdminUser) {
                                $status = 'unpaid_non_reportable'; // Red but can't report
                            } else {
                                $status = 'unpaid_reportable'; // Red with report option
                            }
                        }

                        return [
                            'round_number' => $round->order,
                            'status' => $status,
                            'can_report' => $status === 'unpaid_reportable'
                        ];
                    });

                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'profile_photo' => $user->profile_photo,
                        'role' => $user->pivot->role,
                        'order_number' => $user->pivot->order_number,
                        'is_contributing' => $user->pivot->is_contributing,
                        'is_current_user' => $user->id == $authUserId,
                        'is_admin' => $user->pivot->role === 'admin',
                        'round_statuses' => $roundStatuses
                    ];
                });

            $data['member_rounds_statuses'] = [
                'future' => 'The round is yet to start',
                'joined_late' => 'The user joined the Ajo contribution late',
                'paid' => 'The user has paid for this round',
                'unpaid_non_reportable' => 'The user has not paid for this round but cannot be reported',
                'unpaid_reportable' => 'The user has not paid for this round and can be reported'
            ];



            return $this->success($data, 'Ajo contributions saved successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function depositMoney(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ajo_contribution_id' => 'required|exists:ajo_contributions,id',
        ]);


        if ($validator->fails()) {
            return $this->validationError($validator);
        }




        $ajoContribution = AjoContribution::find($request->ajo_contribution_id);

        if ($ajoContribution->amount > auth()->user()->wallet_balance) {
            return $this->error("Insufficient funds");
        }

        try {

            $currentRound = AjoContributionRound::where([
                'ajo_contribution_id' => $ajoContribution->id,
                'order' => $ajoContribution->current_round
            ])->first();

            if ($currentRound->donations()->where('user_id', auth()->id())->exists()) {
                return $this->error("You have already contributed to this round");
            }


            DB::beginTransaction();

            $amount = $ajoContribution->amount;

            $beneficiary = $currentRound->beneficiary;

            $beneficiary->increment('ajo_contribution_wallet', $amount);

            $beneficiary->transactions()->create([
                'amount' => $amount,
                'type' => UserTransaction::TYPE_CREDIT,
                'message' => $amount . ' was added to your Ajo Contribution wallet from ' . $ajoContribution->name . ' Ajo contribution',
                'source' => UserTransaction::SOURCE_AJO_CONTRIBUTION,
                'status' => UserTransaction::STATUS_SUCCESSFUL,
                'fee' => 0
            ]);

            $contribution =   AjoContributionDonation::create([
                'user_id' => auth()->id(),
                'ajo_contribution_round_id' => $currentRound->id,
                'amount' =>  $ajoContribution->amount,
                'status' => UserTransaction::STATUS_SUCCESSFUL
            ]);

            auth()->user()->decrement('wallet_balance', $ajoContribution->amount);

            auth()->user()->transactions()->create([
                'amount' => $ajoContribution->amount,
                'type' => UserTransaction::TYPE_DEBIT,
                'status' => UserTransaction::STATUS_SUCCESSFUL,
                'message' => 'Added ' . $ajoContribution->amount . ' to ' . $ajoContribution->name . ' ajo contribution',
                'source' => UserTransaction::SOURCE_AJO_CONTRIBUTION,
                'fee' => 0
            ]);

            $currentRound->increment('balance', $ajoContribution->amount);

            // check if the user is still owing ajo contribution
            if ($ajoContribution->activeRound()->status === AjoContributionRound::STATUS_PENDING) {
                $is_still_owing_ajo_contribution = false;

                auth()->user()->ajoContributions->each(function ($ajo) use (&$is_still_owing_ajo_contribution) {
                    if ($ajo->activeRound()->status !== AjoContributionRound::STATUS_PENDING) {
                        return;
                    }

                    $defaulters =  $ajo->getActiveRoundDefaulters()->pluck('id')->toArray();

                    if (in_array(auth()->id(), $defaulters)) {
                        $is_still_owing_ajo_contribution = true;
                    }
                });

                if (!$is_still_owing_ajo_contribution) {
                    auth()->user()->update([
                        'is_owing_ajo_contribution' => false
                    ]);
                }
            }

            DB::commit();

            return $this->success($contribution, 'Money deposited successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ajo_contribution_id' => 'required|exists:ajo_contributions,id',
            'name' => 'required',
            'no_of_members' => 'required|numeric|min:2',
            'cover_image' => 'nullable|image',
        ]);


        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $ajoContribution = AjoContribution::find($request->ajo_contribution_id);

        try {


            DB::beginTransaction();

            $ajoContribution->update([
                'name' => $request->name,
                'no_of_members' => $request->no_of_members,
                'cover_image' => $request->cover_image ? $request->cover_image->store('ajo-contributions', 'public') : $ajoContribution->cover_image
            ]);

            DB::commit();

            return $this->success($ajoContribution, 'Ajo contribution updated successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }


    public function toggleAutoSave(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ajo_contribution_id' => 'required|exists:ajo_contributions,id',
        ]);


        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $ajoContribution = AjoContribution::find($request->ajo_contribution_id);

        try {

            $currentValue = $ajoContribution->members()->where('user_id', auth()->id())->first()->pivot->if_auto_save;

            $ajoContribution->members()->updateExistingPivot(auth()->id(), [
                'if_auto_save' => !$currentValue
            ]);

            $message = !$currentValue ? 'Auto save enabled' : 'Auto save disabled';

            return $this->success($ajoContribution, $message);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function endAjoContribution(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ajo_contribution_id' => 'required|exists:ajo_contributions,id',
        ]);


        if ($validator->fails()) {
            return $this->validationError($validator);
        }



        $ajoContribution = AjoContribution::find($request->ajo_contribution_id);

        try {

            if ($ajoContribution->members()->where('user_id', auth()->id())->first()->pivot->role !== AjoContributionMember::ROLE_ADMIN) {
                return $this->error('You are not authorized to perform this action');
            }

            if ($ajoContribution->getActiveRoundDefaulters()->count() > 0) {
                $this->error('You can\'t end the Ajo Contribution with active round defaulters');
                return;
            }

            DB::beginTransaction();

            $ajoContribution->update([
                'status' => AjoContribution::STATUS_CLOSED
            ]);

            DB::commit();

            return $this->success($ajoContribution, "Ajo contribution ended successfully");
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }


    public function reportMember(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ajo_contribution_id' => 'required|exists:ajo_contributions,id',
            'member_id' => 'required|exists:users,id',
            'round_number' => 'required|numeric',

        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $ajoContribution = AjoContribution::find($request->ajo_contribution_id);

        try {

            if ($ajoContribution->members()->where('user_id', auth()->id())->first()->pivot->role == AjoContributionMember::ROLE_ADMIN) {
                return $this->error('You can not report a member as an admin');
            }

            $user = User::findOrFail($request->member_id);


            $admin = $ajoContribution->members()->where('role', AjoContributionMember::ROLE_ADMIN)->first();

            $message = $user->name . ' was reported  for not paying ' . $ajoContribution->name . ' Ajo Contribution for round ' . $request->round_number;

            $admin->notify(new NotifyUser($message, UserTransaction::SOURCE_AJO_CONTRIBUTION));

            return $this->success($ajoContribution, "Member reported successfully");
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function history(Request $request)
    {
        try {
            $data['closed_ajo_contributions'] = auth()->user()->ajoContributions()->where('status', AjoContribution::STATUS_CLOSED)->latest()->get();


            return $this->success($data, 'Ajo contributions history fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function withdraw(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:100',
        ]);


        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        if ($request->amount > auth()->user()->ajo_contribution_wallet) {
            $validator->errors()->add('cost', 'Insufficient funds in your Ajo Contribution Wallet');
            return $this->validationError($validator);
        }



        try {

            $balance = auth()->user()->ajo_contribution_wallet;

            $fee = getTotalFee($balance, SettingHelper::get(SettingHelper::AJO_CONTRIBUTION_FEE), SettingHelper::get(SettingHelper::AJO_CONTRIBUTION_PERCENTAGE));

            if ($fee > $request->amount) {
                return $this->error('Withdrawal amount cannot be less than the fee');
            }

            DB::beginTransaction();

            $amount = $request->amount - $fee;

            auth()->user()->increment('wallet_balance', $amount);

            auth()->user()->decrement('ajo_contribution_wallet', $request->amount);

            UserTransaction::create([
                'user_id' => auth()->id(),
                'amount' => $amount,
                'type' => UserTransaction::TYPE_CREDIT,
                'message' => $amount . ' was added to your wallet  from your Ajo Contribution Wallet',
                'source' => UserTransaction::SOURCE_AJO_CONTRIBUTION,
                'status' => UserTransaction::STATUS_SUCCESSFUL,
                'fee' => $fee
            ]);

            DB::commit();

            $data = auth()->user();

            return $this->success($data, 'Withdrawal successful');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function join(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'invite_code' => 'required|string',
        ]);


        if ($validator->fails()) {
            return $this->validationError($validator);
        }
        try {
            $ajoContribution = (new HashIdService)->getRecord($request->invite_code, AjoContribution::class);

            $data['ajo_contribution'] = $ajoContribution->loadCount('members', 'rounds');


            return $this->success($data, 'Ajo contributions details fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function acceptInvitation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ajo_contribution_id' => 'required|exists:ajo_contributions,id',
        ]);


        if ($validator->fails()) {
            return $this->validationError($validator);
        }
        try {
            $ajoContribution = AjoContribution::find($request->ajo_contribution_id);

            $previuosOrderNumber  = (int) $ajoContribution->members()->first()->pivot->order_number;

            if ($ajoContribution->type == AjoContribution::TYPE_PERSONAL && $ajoContribution->no_of_members === $ajoContribution->members()->count()) {
                return $this->error('This Ajo Contribution is already full');
            }

            // the -1 is to remove the admin from the count
            if ($ajoContribution->type == AjoContribution::TYPE_BUSINESS && $ajoContribution->no_of_members  === $ajoContribution->members()->count() - 1) {
                return $this->error('This Ajo Contribution is already full ');
            }

            if ($ajoContribution->members()->where('user_id', auth()->id())->exists()) {
                return  $this->error('You are already a member of this Ajo Contribution');
            }

            DB::beginTransaction();


            $lastOrderNumber = $ajoContribution->members()->orderByPivot('order_number', 'desc')->first()->pivot->order_number;


            $member =  AjoContributionMember::create([
                'ajo_contribution_id' => $ajoContribution->id,
                'user_id' => auth()->id(),
                'is_contributing' => true,
                'role' => AjoContributionMember::ROLE_MEMBER,
                'order_number' => $lastOrderNumber + 1
            ]);


            if ($ajoContribution->type === AjoContribution::TYPE_BUSINESS) {

                if ($ajoContribution->members->count() === 1) {
                    AjoContributionRound::where([
                        'ajo_contribution_id' => $ajoContribution->id,
                        'order' => 1
                    ])->update([
                        'user_id' => auth()->id()
                    ]);


                    $ajoContribution->update([
                        'next_round_date' => match ($ajoContribution->frequency) {
                            AjoContribution::FREQUENCY_WEEKLY => $ajoContribution->start_date->addWeek(),
                            AjoContribution::FREQUENCY_MONTHLY => $ajoContribution->start_date->addMonth(),
                            default => $ajoContribution->start_date->addYear()
                        }
                    ]);
                } else {
                    AjoContributionRound::create([
                        'ajo_contribution_id' => $ajoContribution->id,
                        'user_id' => auth()->id(),
                        'order' => $lastOrderNumber + 1,
                        'balance' => 0,
                        'status' => AjoContributionRound::STATUS_AWAITING
                    ]);
                }
            } else {
                AjoContributionRound::create([
                    'ajo_contribution_id' => $ajoContribution->id,
                    'user_id' => auth()->id(),
                    'order' => $lastOrderNumber + 1,
                    'balance' => 0,
                    'status' => AjoContributionRound::STATUS_AWAITING
                ]);
            }

            DB::commit();


            return $this->success($member, 'You have joined this Ajo contributions successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
