<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kyc extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'date_of_birth' => 'date'
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_VERIFIED = 'verified';
    const STATUS_UNVERIFIED = 'unverified';
    const STATUS_REJECTED = 'rejected';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFaceCamImageUrlAttribute()
    {
        return $this->face_cam_image ? getPublicStorageUrl($this->face_cam_image) : null;
    }

    public function getDocumentFrontImageUrlAttribute()
    {
        return $this->document_front_image ? getPublicStorageUrl($this->document_front_image) : null;
    }

    public function getDocumentBackImageUrlAttribute()
    {
        return $this->document_back_image ? getPublicStorageUrl($this->document_back_image) : null;
    }
}
