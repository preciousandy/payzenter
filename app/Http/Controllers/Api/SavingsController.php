<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class SavingsController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        try {
            $data['wallet_balance'] = $request->user()->wallet_balance;
            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
