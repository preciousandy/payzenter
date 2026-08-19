<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use App\Models\SwiftpayTransfer;
use App\Models\User;
use App\Models\UserTransaction;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SwiftPayTransferController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        try {
            $data['recent_transfers'] = auth()->user()->swiftpayTransfers()->latest()->limit(5)->get()->unique('beneficiary_id');




            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $th) {
            return $this->error($th->getMessage());
        }
    }


    public function getBeneficiairies(Request $request)
    {
        try {

            $favoritesUsernames = Beneficiary::where('user_id', auth()->id())
                ->where('type', Beneficiary::TYPE_SWIFTPAY)
                ->get()->pluck('swiftpay_username')->toArray();

            $favourites = User::whereIn('username', $favoritesUsernames)->select('id', 'first_name', 'last_name', 'email', 'username')->get();

            $data['favorites'] = $favourites;

            $data['recent_transfers'] = SwiftpayTransfer::where('user_id', auth()->id())
                ->latest()
                ->with('receiver')
                ->get()
                ->unique('beneficiary_id')
                ->each(function ($transfer) use ($favourites) {
                    $transfer->if_favorite = in_array($transfer->beneficiary_id, $favourites->pluck('id')->toArray());
                })
                ->values();


            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $th) {
            return $this->error($th->getMessage());
        }
    }

    public function toggleFavoriteBeneficiary(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $user = User::find($request->user_id);

            $beneficiary = Beneficiary::where('user_id', auth()->id())
                ->where('swiftpay_username', $user->username)
                ->first();

            if ($beneficiary) {
                $beneficiary->delete();
                return  $this->success([], 'User removed from favourites',);
            }

            Beneficiary::create([
                'user_id' => auth()->id(),
                'swiftpay_username' => $user->username,
                'type' => Beneficiary::TYPE_SWIFTPAY,
                'acct_name' => $user->name,
            ]);

            return $this->success([], 'User added to favourites',);
        } catch (\Exception $th) {
            return $this->error($th->getMessage());
        }
    }

    public function verifyUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|exists:users,username',
        ], [
            'username.exists' => 'User not found',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        if (strtolower($request->username) == strtolower(auth()->user()->username)) {
            $validator->errors()->add('username', 'You cannot transfer to yourself');
            return $this->validationError($validator);
        }

        try {
            $data['user'] = User::where('username', $request->username)->first();

            return $this->success($data, 'User verified successfully');
        } catch (\Exception $th) {
            return $this->error($th->getMessage());
        }
    }

    public function transferMoney(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|exists:users,username',
            'amount' => ['required', 'numeric', 'min:100'],
            'description' => 'nullable|string|max:255',
            'source_link' => 'nullable|string',
        ], [
            'username.exists' => 'User not found',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        if ($request->amount > auth()->user()->wallet_balance) {
            $validator->errors()->add('amount', 'Insufficient funds');
            return $this->validationError($validator);
        }


        try {
            DB::beginTransaction();

            $user = User::where('username', $request->username)->first();

            $user->wallet_balance += $request->amount;
            $user->save();

            auth()->user()->update([
                'wallet_balance' => auth()->user()->wallet_balance - $request->amount
            ]);

            auth()->user()->transactions()->create([
                'amount' => $request->amount,
                'type' => UserTransaction::TYPE_DEBIT,
                'status' => UserTransaction::STATUS_SUCCESSFUL,
                'message' => 'Transfered ' . $request->amount . ' to ' . $user->name,
                'source' => UserTransaction::SOURCE_SWIFTPAY_TRANSFER,
                'fee' => 0
            ]);

            $user->transactions()->create([
                'amount' => $request->amount,
                'type' => UserTransaction::TYPE_CREDIT,
                'status' => UserTransaction::STATUS_SUCCESSFUL,
                'message' => 'Received ' . $request->amount . ' from ' . auth()->user()->name,
                'source' => UserTransaction::SOURCE_SWIFTPAY_TRANSFER,
                'fee' => 0
            ]);


            $transaction =   SwiftpayTransfer::create([
                'user_id' => auth()->id(),
                'beneficiary_id' => $user->id,
                'reference' => 'SWPYTRNFR' . now()->timestamp,
                'description' => 'SWPY/' . $request->description,
                'amount' => $request->amount,
                'status' => SwiftpayTransfer::STATUS_SUCCESSFUL,
                'source_link' => $request->source_link ?? null
            ]);

            DB::commit();

            return $this->success($transaction, 'Transaction successful');
        } catch (\Exception $th) {
            return $this->error($th->getMessage());
        }
    }
}
