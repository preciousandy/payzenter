<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MerchantKyb extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'pep_declaration' => 'boolean',
        'is_settlement_suspended' => 'boolean',
        'settlement_verified_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getIdentityDocumentUrlAttribute()
    {
        return $this->identity_document_path ? asset('storage/' . $this->identity_document_path) : null;
    }

    public function getProofOfAddressUrlAttribute()
    {
        return $this->proof_of_address_path ? asset('storage/' . $this->proof_of_address_path) : null;
    }

    public function getBusinessLogoUrlAttribute()
    {
        return $this->business_logo_path ? asset('storage/' . $this->business_logo_path) : null;
    }

    public function getBusinessResolutionUrlAttribute()
    {
        return $this->business_resolution_path ? asset('storage/' . $this->business_resolution_path) : null;
    }

    public function getCompanyProfileUrlAttribute()
    {
        return $this->company_profile_path ? asset('storage/' . $this->company_profile_path) : null;
    }

    public function getStatusReportUrlAttribute()
    {
        return $this->status_report_path ? asset('storage/' . $this->status_report_path) : null;
    }

    public function getBusinessAddressVerificationUrlAttribute()
    {
        return $this->business_address_verification_path ? asset('storage/' . $this->business_address_verification_path) : null;
    }
}


