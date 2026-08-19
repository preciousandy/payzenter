<?php

namespace App\Http\Controllers\Api;

use App\Helpers\SettingHelper;
use App\Http\Controllers\Controller;
use App\Models\UserReferral;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    use ApiResponseTrait;

    public function referralPage(Request $request)
    {

        try {
            $data = [];

            $data['referral_bonus_amount'] = (float) SettingHelper::get(SettingHelper::REFERRAL_BONUS);

            $data['referral_link'] = route('register', ['referral' => auth()->user()->hash_id]);

            $data['ajo_savings_referral_code'] = auth()->user()->hash_id;

            $data['amount_earned'] = auth()->user()->referrals()->wherePivot('status', UserReferral::STATUS_CLAIMED)->sum('amount');

            $data['referrals'] = auth()->user()->referrals;

            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
