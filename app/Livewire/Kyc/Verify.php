<?php

namespace App\Livewire\Kyc;

use App\Models\Kyc;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;

class Verify extends Component
{
    use WithFileUploads;
    use LivewireAlert;


    public $first_name;
    public $last_name;
    public $other_names;
    public $date_of_birth;
    public $address;
    public $document_type;
    public $document_number;
    public $gender;
    public $occupation;
    public $bvn;
    public $status;
    public $document_front_image;
    public $document_back_image;
    public $face_cam_image;

    public $document_front_upload_temp_url;
    public $document_back_upload_temp_url;

    public $document_front_upload;
    public $document_back_upload;
    public $faceCamPhoto;

    public $currentStep = 1;

    public $documentTypes = [
        'national_id_slip' => 'National ID (slip)',
        'national_id_card' => 'National ID card',
        'voters_card' => 'Voters Card',
        'drivers_license' => 'Drivers License',
        'international_passport' => 'International Passport',
    ];

    public function mount()
    {
        $this->first_name = auth()->user()->first_name;
        $this->last_name = auth()->user()->last_name;

        $this->bvn = auth()->user()->bvn ?? '';

        if (auth()->user()->kyc_status == Kyc::STATUS_PENDING) {
            return $this->flash('error', 'KYC verification is already in progress', [], route('home'));
        }
    }

    public function updatedDocumentFrontUpload()
    {

        if ($this->document_front_upload) {
           $this->document_front_upload_temp_url = $this->document_front_upload->temporaryUrl();
        }
    }

    public function updatedDocumentBackUpload()
    {
        if ($this->document_back_upload) {
           $this->document_back_upload_temp_url = $this->document_back_upload->temporaryUrl();
        }
    }

    public function submitStep1()
    {
        $data =  $this->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'other_names' => 'nullable|string',
            'date_of_birth' => 'required|date',
            'address' => 'required|string',
            'occupation' => 'required|string',
            'gender' => 'required|string',
            'bvn' => 'required|numeric|digits:11',
        ]);

        $this->currentStep = 2;
    }

    public function submitStep2()
    {
        $data = $this->validate([
            'document_type' => 'required|string',
            'document_number' => 'required|string',
            'document_front_upload' => 'required|image',
            'document_back_upload' => 'required|image',
        ]);

        $this->currentStep = 3;
    }

    public function submitStep3()
    {

        try {
            $img = $this->faceCamPhoto;
            $folderPath = "kyc/";

            $image_parts = explode(";base64,", $img);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];

            $image_base64 = base64_decode($image_parts[1]);
            $fileName = uniqid() . '.png';

            $file = $folderPath . $fileName;

            Storage::put($file, $image_base64);

            $this->document_front_image = $this->document_front_upload->store('kyc', ['disk' => 'public']);
            $this->document_back_image = $this->document_back_upload->store('kyc', ['disk' => 'public']);

            DB::beginTransaction();

            Kyc::create([
                'user_id' => auth()->id(),
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'other_names' => $this->other_names,
                'date_of_birth' => $this->date_of_birth,
                'address' => $this->address,
                'gender' => $this->gender,
                'occupation' => $this->occupation,
                'document_type' => $this->document_type,
                'document_number' => $this->document_number,
                'document_front_image' => $this->document_front_image,
                'document_back_image' => $this->document_back_image,
                'face_cam_image' => $file,
            ]);

            auth()->user()->update([
                'kyc_status' => Kyc::STATUS_PENDING,
                'bvn' => $this->bvn,
            ]);

            DB::commit();

            return $this->flash('success', 'KYC details submitted successfully', [], route('home'));
        } catch (\Throwable $th) {
            dd($th->getMessage());
            return $this->flash('error', 'An error occurred while subtmitting your KYC details', [], route('home'));
        }
    }

    public function render()
    {
        return view('livewire.kyc.verify')->layout('layouts.auth')->title('KYC Verification');
    }
}
