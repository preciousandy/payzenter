<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PushNotificationController extends Controller
{
    use ApiResponseTrait;

    /**
     * Store the device token for push notifications.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function handleStoreToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'device_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $user = auth()->user();

            $user->notificationTokens()->updateOrCreate(
                ['device_name' => $request->device_name, 'user_id' => $user->id],
                ['token' => $request->token, 'updated_at' => now()]
            );

            return $this->success([], 'Push Notification token stored successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
