<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupSavingsMembers extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'group_savings_members';

    const STATUS_INVITED = 'invited';
    const STATUS_ACTIVE = 'active';
    const STATUS_DECLINED = 'declined';
    const STATUS_REMOVED = 'removed';

    const ROLE_ADMIN = 'admin';
    const ROLE_MEMBER = 'member';


    // group donations
    public function donations($group_id)
    {
        return GroupSavingsDonation::where('group_savings_id', $group_id)
            ->where('user_id', $this->user_id)
            ->get();
    }
}
