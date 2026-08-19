<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Network extends Model
{
    use HasFactory;

    public function dataPlans()
    {
        return $this->hasMany(DataPlan::class, 'network_code', 'vtu_code')->orderBy('price', 'asc');
    }
}
