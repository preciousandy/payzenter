<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\UserTransaction;
use App\Rules\StrongPassword;
use App\Traits\ApiResponseTrait;
use App\Utils\ResponseMessage;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use ApiResponseTrait;

    public function user(Request $request)
    {

        try {
            $data = auth()->user()->load('kyc');

            $data['user_level_badge'] = asset(
                'badges/' . $request->user()->level . '.png'
            );

            return $this->success($data, 'User fetched successfully');
        } catch (Exception $e) {

            return $this->error($e->getMessage());
        }
    }


    public function transactionHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {

            $data['sources'] =  [
                'wallet_funded',
                'airtime',
                'data',
                'cable',
                'electricity',
                'foreign_to_local_transfer',
                'local_to_foreign_transfer',
                'holdings',
                'investments',
                'bureau_de_change',
                'crypto',
                'gift_card',
                'bank_transfer',
                'swiftpay_transfer',
                'save_with_interest',
                'group_savings',
                'ajo_savings',
                'ajo_contribution',
                'international_transfer',
                'multiple_bank_transfer',
                'multiple_swiftpay_transfer',
            ];


            // Get the transactions with date range filtering
            $transactions = UserTransaction::query()
                ->where('user_id', auth()->id())
                ->when($request->has('source'), function ($query) use ($request) {
                    return $query->where('source', $request->source);
                })
                ->when($request->has('amount'), function ($query) use ($request) {
                    return $query->orderBy('amount', $request->amount);
                })
                ->when($request->has('start_date'), function ($query) use ($request) {
                    $startDate = $request->start_date;
                    $endDate = $request->has('end_date') ? $request->end_date : now()->toDateString();
                    return $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                })
                ->when($request->has('order'), function ($query) use ($request) {
                    return $query->orderBy('created_at', $request->order);
                }, function ($query) {
                    return $query->latest();
                })
                ->paginate(15);

            $data['transactions'] = $transactions;

            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function verifyTransactionPin(Request $request)
    {
        $request->validate([
            'pin' => 'required|numeric|digits:4'
        ]);

        try {
            if (Hash::check($request->pin, request()->user()->pin)) {
                return $this->success([], 'PIN verified successfully');
            } else {
                return $this->error('Incorrect PIN');
            }
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    // public function getUserPin(Request $request)
    // {


    //     try {
    //         if (Hash::check($request->pin, request()->user()->pin)) {
    //             return $this->success([], 'PIN verified successfully');
    //         }
    //     } catch (\Exception $e) {
    //         return $this->error($e->getMessage());
    //     }
    // }

    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email,' . $request->user()->id,
            'phone' => 'required|numeric|unique:users,phone,' . $request->user()->id,
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $request->user()->update([
                'email' => $request->email,
                'phone' => $request->phone
            ]);

            return $this->success($request->user(), 'User details updated successfully');
        } catch (Exception $e) {

            return $this->error($e->getMessage());
        }
    }


    public function updateProfilePhoto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4048',
        ]);


        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {

            $new_photo = $request->photo->store('profile-photos');

            $request->user()->update([
                'profile_image' => $new_photo
            ]);

            return $this->success($request->user(), 'User photo updated successfully');
        } catch (Exception $e) {

            return $this->error($e->getMessage());
        }
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|min:8|current_password',
            'password' => ['required', 'confirmed', new StrongPassword],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }


        try {
            $request->user()->update([
                'password' => Hash::make($request->password)
            ]);

            return $this->success($request->user(), 'Password updated successfully');
        } catch (Exception $e) {

            return $this->error($e->getMessage());
        }
    }

    public function changeTransactionPin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_pin' => 'required|numeric|digits:4',
            'new_pin' => 'required|numeric|digits:4',
            'confirm_pin' => 'required|numeric|digits:4|same:new_pin',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        if (!Hash::check($request->current_pin, $request->user()->pin)) {
            return $this->error('Incorrect PIN');
        }

        try {
            $request->user()->update([
                'pin' => Hash::make($request->new_pin)
            ]);

            return $this->success($request->user(), 'PIN updated successfully');
        } catch (Exception $e) {

            return $this->error($e->getMessage());
        }
    }


    public function changeSwiftPayTag(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:users,username,' . $request->user()->id,
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $request->user()->update([
                'username' => $request->username
            ]);

            return $this->success($request->user(), 'SwiftPay Tag updated successfully');
        } catch (Exception $e) {

            return $this->error($e->getMessage());
        }
    }

    public function deleteAccount(Request $request)
    {


        try {

            $this->deleteAllRelatedData($request->user());

            $request->user()->delete();

            return $this->success([], 'User account deleted successfully');
        } catch (Exception $e) {

            return $this->error($e->getMessage());
        }
    }

    public function deleteAllRelatedData($user)
    {

        DB::beginTransaction();
        // Delete transactions
        $user->transactions()->delete();

        // Delete savings
        $user->savings()->delete();

        // Remove user from group savings
        $user->groupSavings()->detach();

        // Delete group savings donations
        $user->groupSavingsDonations()->delete();

        // Delete ajo savings
        $ajoSavings =   $user->ajoSavings;


        $ajoSavings->each(function ($ajoSaving) {
            $ajoSaving->transactions()->delete();
            $ajoSaving->delete();
        });



        // Remove user from ajo contributions
        $user->ajoContributions()->detach();

        // Delete beneficiaries
        $user->beneficiaries()->delete();

        // Delete holdings
        $user->holdings()->delete();

        // Delete investments
        $user->investments()->delete();

        // Remove referrals
        $user->referrals()->detach();

        // Delete KYC
        if ($user->kyc) {
            $user->kyc->delete();
        }

        // Delete live support messages
        $user->liveSupportMessages()->delete();

        // Delete international transfers
        $user->internationalTransfers()->delete();

        $user->swiftpayTransfers()->delete();

        $user->bankTransfers()->delete();

        $user->sellFxOrders()->delete();

        $user->buyFxOrders()->delete();

        DB::commit();
    }

    public function getNotifications(Request $request)
    {
        try {
            $data['topics'] =  [
                'wallet_funded',
                'airtime',
                'data',
                'cable',
                'electricity',
                'foreign_to_local_transfer',
                'local_to_foreign_transfer',
                'holdings',
                'investments',
                'bureau_de_change',
                'crypto',
                'gift_card',
                'bank_transfer',
                'swiftpay_transfer',
                'save_with_interest',
                'group_savings',
                'ajo_savings',
                'ajo_contribution',
                'international_transfer',
                'multiple_bank_transfer',
                'multiple_swiftpay_transfer',
            ];


            $data['notifications'] = auth()->user()->notifications()->paginate(15);

            return $this->success($data, 'Data fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }


    public function markNotificationsAsRead(Request $request, Notification $notification)
    {
        try {
            $notification->update([
                'read_at' => now()
            ]);

            return $this->success([], 'Notification marked as read successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function markAllNotificationsAsRead(Request $request)
    {
        try {
            $user = $request->user();

            $user->unreadNotifications->markAsRead();

            return $this->success([], 'All notifications marked as read successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
    public function getUserBankDetails(Request $request)
    {
        try {
            $user = $request->user();

            $bankDetails = $user->virtualAccount ?? null;

            // get only account number and bank code
            if ($bankDetails) {
                $bankDetails = [
                    'account_number' => $bankDetails->account_number,
                    'bank_code' => $bankDetails->bank_code,
                    'bank_name' => $bankDetails->bank_name,
                    'account_name' => $bankDetails->account_name,
                ];
            } else {
                $bankDetails = null;
            }

            return $this->success($bankDetails, 'User bank details fetched successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
