<div x-data="{showFailedModal: false}">
    <x-loading-spinner wire:target='approve' />

    <x-secondary-button @click="showFailedModal = true" class="">
        View
    </x-secondary-button>

    <div x-show="showFailedModal" x-transition x-cloak class=" fixed bg-black/70 z-50 left-0 top-0 w-full h-full flex justify-center items-center p-4 overflow-y-auto ">
        <div class=" bg-gray-50 p-4 lg:p-8 rounded-lg w-full max-w-5xl  overflow-y-auto h-5/6">
            <div class=" flex justify-between items-center mb-6">
                <h2 class=" text-2xl font-semibold">Verify User KYC</h2>
                <button @click="showFailedModal = false">
                    <span class="fa fa-close fa-2x"></span>
                </button>
            </div>

            <div class=" mt-4">
                <h4 class=" text-gray-500 text-center font-semibold mb-2">Selfie Image</h4>

                <img src="{{asset($kyc->face_cam_image_url)}}" alt="" class=" mx-auto rounded-xl">
            </div>

            <div class=" grid grid-cols-3 gap-6 mt-6">
                <div>
                    <h4 class=" text-gray-500">First Name</h4>
                    <p class=" font-medium text-xl mt-2">
                        {{$kyc->first_name}}
                    </p>
                </div>
                <div>
                    <h4 class=" text-gray-500">Last Name</h4>
                    <p class=" font-medium text-xl mt-2">
                        {{$kyc->last_name}}
                    </p>
                </div>
                <div>
                    <h4 class=" text-gray-500">Other Name</h4>
                    <p class=" font-medium text-xl mt-2">
                        {{$kyc->other_names ?? '-'}}
                    </p>
                </div>
                <div>
                    <h4 class=" text-gray-500">Date of Birth</h4>
                    <p class=" font-medium text-xl mt-2">
                        {{$kyc->date_of_birth->format('d M Y')}}
                    </p>
                </div>

                <div>
                    <h4 class=" text-gray-500">BVN</h4>
                    <p class=" font-medium text-xl mt-2">
                        {{$kyc->user->bvn ?? '-'}}
                    </p>
                </div>

                <div>
                    <h4 class=" text-gray-500">Address</h4>
                    <p class=" font-medium text-xl mt-2">
                        {{$kyc->address ?? '-'}}
                    </p>
                </div>
                <div>
                    <h4 class=" text-gray-500">Document type</h4>
                    <p class=" font-medium text-xl mt-2">
                        {{ucfirst(str_replace("_", " ", $kyc->document_type)) ?? '-'}}
                    </p>
                </div>

                <div>
                    <h4 class=" text-gray-500">Document Number</h4>
                    <p class=" font-medium text-xl mt-2">
                        {{$kyc->document_number ?? '-'}}
                    </p>
                </div>
                <div>
                    <h4 class=" text-gray-500">Gender</h4>
                    <p class=" font-medium text-xl mt-2">
                        {{$kyc->gender ?? '-'}}
                    </p>
                </div>
                <div>
                    <h4 class=" text-gray-500">Occupation</h4>
                    <p class=" font-medium text-xl mt-2">
                        {{$kyc->occupation ?? '-'}}
                    </p>
                </div>
            </div>

            <div class=" grid  gap-10 mt-6">
                <div>
                    <h4 class=" text-gray-600  text-lg font-medium mb-2">Document Front Image</h4>

                    <img src="{{asset($kyc->document_front_image_url)}}" alt="" class=" mx-auto rounded-xl">
                </div>
                <div>
                    <h4 class="text-gray-600  text-lg  font-medium mb-2">Document Back Image</h4>

                    <img src="{{asset($kyc->document_back_image_url)}}" alt="" class=" mx-auto rounded-xl">
                </div>
            </div>



            @if ($kyc->user->kyc_status === 'pending')
            <div x-data='{showRejectForm: false}' class="  mt-8">
                <div class=" w-full grid  grid-cols-2 gap-6">
                    <x-secondary-button @click="showRejectForm = !showRejectForm" class=" w-full border-red-600 text-red-700">
                        Reject
                    </x-secondary-button>

                    <x-primary-button wire:click='approve' class=" w-full">
                        Approve
                    </x-primary-button>

                </div>

                <form action="" x-show="showRejectForm" x-transition x-cloak class=" space-y-4 mt-3" wire:submit.prevent="reject()">
                    <div>
                        <x-input-label for="name" :value="__('Reason:')" />
                        <textarea name="" id="" rows="4" class=" w-full rounded-md bg-white border mt-2" wire:model='reason' required></textarea>
                        <x-input-error class="mt-2 " :messages="$errors->get('rejectMessage')" />
                    </div>

                    <x-primary-button class=" w-full">
                        {{ __('Submit') }}
                    </x-primary-button>
                </form>
            </div>

            @endif
        </div>
    </div>


</div>