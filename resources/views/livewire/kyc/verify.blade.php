<div class="bg-white min-h-screen px-4 py-6">

    <x-blank-page-header title="KYC Verification" />
    
    {{-- Spinner for heavy operations --}}
    <x-loading-spinner wire:target='document_front_upload, document_back_upload, submitStep3' />

    {{-- Progress Steps --}}
    <div class="flex justify-center space-x-4 mb-8 mt-4">
        @php
            $steps = [1 => 'Personal Info', 2 => 'Document Upload', 3 => 'Face Capture'];
        @endphp
        @foreach($steps as $stepNum => $label)
            <div class="flex items-center">
                {{-- Circle --}}
                <div class="@if($currentStep >= $stepNum) bg-[#1400FB] @else bg-gray-300 @endif w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold shadow-md transition duration-300">
                    {{ $stepNum }}
                </div>
                {{-- Label (optional on mobile, but helpful) --}}
                <span class="absolute mt-14 text-xs font-medium @if($currentStep >= $stepNum) text-[#1400FB] @else text-gray-500 @endif hidden sm:block">{{ $label }}</span>
                {{-- Divider Line --}}
                @if($stepNum < 3)
                <div class="@if($currentStep > $stepNum) bg-[#1400FB] @else bg-gray-300 @endif w-24 h-1 transition duration-300"></div>
                @endif
            </div>
        @endforeach
    </div>
    
    <div class="max-w-xl mx-auto">
        @if ($currentStep === 1)
        <x-kyc.verify-step1 />

        @elseif ($currentStep === 2)
        <form class="mt-6 space-y-6" wire:submit.prevent='submitStep2'>
            <h2 class="text-xl font-bold text-gray-800 border-b pb-2">Step 2: Document Information</h2>
            
            {{-- Document Type Select --}}
            <div>
                <x-input-label for="document_type" :value="__('Document Type')" />
                <select wire:model="document_type" id="document_type" class="p-3 w-full rounded-lg border-gray-300 focus:border-[#1400FB] focus:ring-[#1400FB] mt-2" required>
                    <option value="">Select type</option>
                    @foreach ($documentTypes as $key => $name)
                    <option value="{{ $key }}">{{ $name }}</option>
                    @endforeach
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('document_type')" />
            </div>

            {{-- Document Number Input --}}
            <div>
                <x-input-label for="document_number" :value="__('Document Number')" />
                <x-text-input id="document_number" class="block mt-2 w-full placeholder:text-gray-400" type="text" wire:model='document_number' placeholder="Enter the document ID number" required />
                <x-input-error class="mt-2" :messages="$errors->get('document_number')" />
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 pt-4">
                
                {{-- Document Front Upload --}}
                <div>
                    <p class="mb-2 font-medium text-gray-700">Upload Document **Front**</p>
                    <div class="relative group">
                        
                        {{-- Preview Image --}}
                        @if ($document_front_upload)
                        <div class="w-full rounded-2xl h-48 md:h-60 overflow-hidden border-2 border-[#1400FB] shadow-lg">
                            {{-- FIX: Using the temp URL variable --}}
                            <img src="{{ $document_front_upload_temp_url }}" alt="Front Document Preview" class="w-full h-full object-cover transition duration-300">
                        </div>
                        @else
                        <div class="w-full rounded-2xl h-48 md:h-60 bg-gray-100 flex items-center justify-center border-2 border-dashed border-gray-300 text-gray-500">
                            <span class="fa fa-id-card fa-2x"></span>
                        </div>
                        @endif
                        
                        {{-- Upload Button (Disguised) --}}
                        <label for="card-front-upload" class="bg-[#1400FB] h-12 w-12 rounded-full flex items-center justify-center text-white absolute right-4 -bottom-6 cursor-pointer shadow-xl hover:bg-[#2E2380] transition duration-150" title="Upload Front Image">
                            <span class="fa fa-camera text-xl"></span>
                        </label>
                    </div>
                    {{-- Actual File Input --}}
                    <input id="card-front-upload" type="file" class="hidden" wire:model='document_front_upload' required />
                    <x-input-error :messages="$errors->get('document_front_upload')" class="mt-8" />
                </div>

                {{-- Document Back Upload --}}
                <div>
                    <p class="mb-2 font-medium text-gray-700">Upload Document **Back**</p>
                    <div class="relative group">
                        
                        {{-- Preview Image --}}
                        @if ($document_back_upload)
                        <div class="w-full rounded-2xl h-48 md:h-60 overflow-hidden border-2 border-[#1400FB] shadow-lg">
                             {{-- FIX: Using the temp URL variable --}}
                            <img src="{{ $document_back_upload_temp_url }}" alt="Back Document Preview" class="w-full h-full object-cover transition duration-300">
                        </div>
                        @else
                        <div class="w-full rounded-2xl h-48 md:h-60 bg-gray-100 flex items-center justify-center border-2 border-dashed border-gray-300 text-gray-500">
                            <span class="fa fa-undo fa-2x"></span>
                        </div>
                        @endif
                        
                        {{-- Upload Button (Disguised) --}}
                        <label for="card-back-upload" class="bg-[#1400FB] h-12 w-12 rounded-full flex items-center justify-center text-white absolute right-4 -bottom-6 cursor-pointer shadow-xl hover:bg-[#2E2380] transition duration-150" title="Upload Back Image">
                            <span class="fa fa-camera text-xl"></span>
                        </label>
                    </div>
                     {{-- Actual File Input --}}
                    <input id="card-back-upload" type="file" class="hidden" wire:model='document_back_upload' required />
                    <x-input-error :messages="$errors->get('document_back_upload')" class="mt-8" />
                </div>

            </div>

            {{-- Navigation Buttons --}}
            <div class="flex justify-between items-center pt-8 border-t mt-10">
                <x-secondary-button class="rounded-xl px-8 w-fit shadow-md hover:border-[#1400FB]" type="button" wire:click.prevent="$set('currentStep', 1)">
                    <span class="fa fa-chevron-left mr-2"> </span> **Previous**
                </x-secondary-button>
                <x-primary-button class="rounded-xl px-8 w-fit bg-[#1400FB] hover:bg-[#2E2380] shadow-md" type="submit">
                    **Next** <span class="fa fa-chevron-right ml-2"> </span>
                </x-primary-button>
            </div>

        </form>

        @elseif ($currentStep === 3)
        <x-kyc.verify-step3 />
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.25/webcam.min.js"></script>

<script language="JavaScript">
    function openWebcam() {

        document.getElementById("webcam").style.display = 'block';
        document.getElementById("captured_image").style.display = 'none';
        
        Webcam.set({
            height: 350,
            border_radius: 10,
            image_format: 'jpeg',
            jpeg_quality: 90,
        });
        
        Webcam.attach('#my_camera');
    }
    
    function take_snapshot() {
        Webcam.snap( function(data_uri) {
            $(".image-tag").val(data_uri);
            document.getElementById('results').innerHTML = '<img src="'+data_uri+'"/>';
            @this.set('faceCamPhoto', data_uri, true);
            // document.getElementById('submit').style.display = "block";
            document.getElementById("captured_image").style.display = 'block';
            document.getElementById("webcam").style.display = 'none';

        } );
    }
</script>
@endpush