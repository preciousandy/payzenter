<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kyc;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KycController extends Controller
{
    use ApiResponseTrait;

    public function submitKycStep1(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'other_names' => 'nullable|string',
            'date_of_birth' => 'required|date',
            'address' => 'required|string',
            'occupation' => 'required|string',
            'gender' => 'required|string',
            'bvn' => 'required|digits:11',
        ]);


        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $existingKyc = Kyc::where('user_id', auth()->id())->first();

            if ($existingKyc) {
                $existingKyc->delete();
            }

            DB::beginTransaction();

            Kyc::create(
                [
                    'user_id' => auth()->id(),
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'other_names' => $request->other_names,
                    'date_of_birth' => $request->date_of_birth,
                    'address' => $request->address,
                    'gender' => $request->gender,
                    'occupation' => $request->occupation,
                ]
            );

            $request->user()->update([
                'bvn' => $request->bvn,
            ]);

            DB::commit();

            return $this->success([], 'KYC step 1 submitted successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function submitKycStep2(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'document_type' => 'required|string',
            'document_number' => 'required|string',
            'document_front_upload' => 'required|image',
            'document_back_upload' => 'required|image',
        ]);


        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $existingKyc = Kyc::where('user_id', auth()->id())->first();

            $frontImage = $request->file('document_front_upload')->store('kyc', ['disk' => 'public']);
            $backImage = $request->file('document_back_upload')->store('kyc', ['disk' => 'public']);

            $existingKyc->update(
                [
                    'document_type' => $request->document_type,
                    'document_number' => $request->document_number,
                    'document_front_image' => $frontImage,
                    'document_back_image' => $backImage,
                ]
            );

            return $this->success([], 'KYC step 2 submitted successfully');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function submitKycStep3(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'face_image' => 'required|image|file',
        ]);


        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $existingKyc = Kyc::where('user_id', auth()->id())->first();

            $faceImage = $request->file('face_image')->store('kyc', ['disk' => 'public']);


            DB::beginTransaction();


            $existingKyc->face_cam_image = $faceImage;

            $existingKyc->save();

            auth()->user()->update([
                'kyc_status' => Kyc::STATUS_PENDING
            ]);

            DB::commit();

            return $this->success($existingKyc, 'KYC step details submitted successfully and is pending approval');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
