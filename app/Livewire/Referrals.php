<?php

namespace App\Livewire;

use App\Helpers\SettingHelper;
use App\Models\UserReferral;
use Livewire\Component;

class Referrals extends Component
{
    public $referralBonus;
    public $link;
    public $referrals;
    public $referralCode;
    public $amounEarned;

    public function mount()
    {
        $this->referralBonus = SettingHelper::get(SettingHelper::REFERRAL_BONUS);
        $this->link = route('register', ['referral' => auth()->user()->hash_id]);

        $this->referrals = auth()->user()->referrals;

        $this->referralCode = auth()->user()->hash_id;

        $this->amounEarned = auth()->user()->referrals()->wherePivot('status', UserReferral::STATUS_CLAIMED)->sum('amount');
    }

    public function render()
    {
        return view('livewire.referrals')->title('Referrals');
    }
}
