<?php

namespace App\Livewire\Admin;

use App\Models\ErrorLog;
use App\Models\Kyc;
use App\Models\User;
use Livewire\Component;

class AdminSideBar extends Component
{
    public $pendingKycCount = 0;

    public $openErrorsCount = 0;

    public function mount()
    {
        $this->pendingKycCount = User::whereHas('kyc')->where('kyc_status', Kyc::STATUS_PENDING)->count();
        $this->openErrorsCount = ErrorLog::where('status', ErrorLog::STATUS_OPEN)->count();
    }

    public function render()
    {
        return view('livewire.admin.admin-side-bar');
    }
}
