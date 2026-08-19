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

    public function getFaceCamImageurlAttribute()
    {
        return '/storage/' . $this->face_cam_image;
    }

    public function getDocumentFrontImageUrlAttribute()
    {
        return '/storage/' . $this->document_front_image;
    }

    public function getDocumentBackImageUrlAttribute()
    {
        return '/storage/' . $this->document_back_image;
    }
}
