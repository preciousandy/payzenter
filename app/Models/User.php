<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\HashIdTrait;
use App\Traits\UserVerificationTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Models\MerchantApiKey;
use App\Models\MerchantKyb;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HashIdTrait, UserVerificationTrait;
    // use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'pin'
    ];

    const LEVEL_GREEN = 'green';
    const LEVEL_BLUE = 'blue';
    const LEVEL_BLACK = 'black';
    const LEVEL_GOLD = 'gold';

    const LEVEL_LIMITS = [
        self::LEVEL_GREEN => 100000,
        self::LEVEL_BLUE => 1000000,
        self::LEVEL_BLACK => 10000000,
        self::LEVEL_GOLD => 100000000000,
    ];

    const ACCOUNT_STATUS_ACTIVE = 'active';
    const ACCOUNT_STATUS_SUSPENDED = 'suspended';
    const ACCOUNT_STATUS_BLOCKED = 'blocked';
    const ACCOUNT_STATUS_DELETED = 'deleted';

    const PLATFORM_WEB = 'web';
    const PLATFORM_MOBILE = 'mobile';

    const OTP_VERIFICATION_TIMEOUT = 10;


    const TEST_EMAIL = "andyprecious6@gmail.com";


    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_otp_verified' => 'boolean',
        'is_bvn_verified' => 'boolean',
        'is_owned_ajo_contribution' => 'boolean',
        'if_level_changed' => 'boolean',
        'suspended_until' => 'datetime',
    ];

    protected $appends = ['name', 'profile_photo', 'hash_id'];

    // name attribute
    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getProfilePhotoAttribute()
    {
        return $this?->profile_image ? asset('/storage/' . $this->profile_image) : asset('logo/pzfav.jpg');
    }


    public function bankAccounts()
{
    return $this->hasMany(BankAccount::class);
}


    public function transactions()
    {
        return $this->hasMany(UserTransaction::class);
    }

    public function savings()
    {
        return $this->hasMany(Savings::class);
    }

    public function groupSavings()
    {
        return $this->belongsToMany(GroupSavings::class, 'group_savings_members')
            ->orderBy('created_at', 'desc')
            ->withPivot('role', 'status')
            ->wherePivot('status', GroupSavingsMembers::STATUS_ACTIVE)
            ->where('group_savings.status', GroupSavings::STATUS_ACTIVE);
    }

    // closed group savings
    public function closedGroupSavings()
    {
        return   $this->belongsToMany(GroupSavings::class, 'group_savings_members')
            ->orderBy('created_at', 'desc')
            ->withPivot('role', 'status')
            ->wherePivot('status', GroupSavingsMembers::STATUS_ACTIVE)
            ->where('group_savings.status', GroupSavings::STATUS_CLOSED);
    }

    public function groupSavingsDonations()
    {
        return $this->hasMany(GroupSavingsDonation::class);
    }

    public function ajoSavings()
    {
        return $this->hasMany(AjoSavings::class);
    }


    public function ajoContributions()
    {
        return $this->belongsToMany(AjoContribution::class, 'ajo_contribution_members');
    }

    public function beneficiaries()
    {
        return $this->hasMany(Beneficiary::class);
    }

    public function holdings()
    {
        return $this->hasMany(Holdings::class);
    }

    public function investments()
    {
        return $this->hasMany(Investment::class);
    }

    public function referrals()
    {
        return $this->belongsToMany(User::class,  'user_referrals', 'referred_by')->orderBy('created_at', 'desc')->withPivot('created_at', 'updated_at', 'amount', 'status');
    }

    public function kyc()
    {
        return $this->hasOne(Kyc::class, 'user_id');
    }

    public function liveSupportMessages()
    {
        return $this->hasMany(liveSupportMessage::class, 'user_id');
    }

    public function internationalTransfers()
    {
        return $this->hasMany(InternationalTransfer::class, 'user_id');
    }

    public function swiftpayTransfers()
    {
        return $this->belongsToMany(User::class, 'swiftpay_transfers', 'user_id', 'beneficiary_id')->withPivot('reference', 'description', 'amount', 'status', 'created_at', 'updated_at')->orderBy('created_at', 'desc');
    }

    public function sellFxOrders()
    {
        return $this->hasMany(SellFxOrder::class, 'user_id');
    }

    public function buyFxOrders()
    {
        return $this->hasMany(BuyFxOrder::class, 'user_id');
    }

    public function bankTransfers()
    {
        return $this->hasMany(Transfer::class, 'user_id');
    }

    public function virtualAccount()
    {
        return $this->hasOne(UserVirtualAccount::class);
    }

    public function notificationTokens()
    {
        return $this->hasMany(UserNotificationToken::class);
    }

    public function merchantApiKeys()
    {
        return $this->hasMany(MerchantApiKey::class);
    }

    public function merchantKyb()
    {
        return $this->hasOne(MerchantKyb::class);
    }

    /**
     * Check if user's suspension has expired and automatically activate account
     */
    public function checkSuspensionExpiry()
    {
        if (
            $this->account_status === self::ACCOUNT_STATUS_SUSPENDED &&
            $this->suspended_until &&
            now()->greaterThan($this->suspended_until)
        ) {

            $this->update([
                'account_status' => self::ACCOUNT_STATUS_ACTIVE,
                'suspended_until' => null,
                'suspension_duration_days' => null,
                'suspension_reason' => null
            ]);

            // Send reactivation email
            try {
                \Mail::to($this->email)->send(new \App\Mail\AccountReactivatedMail($this));
            } catch (\Exception $e) {
                logger('Failed to send reactivation email: ' . $e->getMessage());
            }

            return true;
        }

        return false;
    }

    /**
     * Check if user is currently suspended (and not expired)
     */
    public function isSuspended()
    {
        return $this->account_status === self::ACCOUNT_STATUS_SUSPENDED &&
            ($this->suspended_until === null || now()->lessThan($this->suspended_until));
    }
}
