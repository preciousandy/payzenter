<div class=" bg-white">
    <div class=" px-4 py-6  max-w-4xl min-h-screen" x-data="{showUploadModal: false, showDeleteModal: false}">
        <x-loading-spinner wire:target='deleteAccount, photo' />


        <div class="overflow-hidden shadow-sm rounded-xl p-6 flex flex-col gap-10 ">
            <div class=" flex flex-col items-center gap-3 lg:gap-5 md:flex-row md:items-center">
                <div class="  w-fit rounded-full relative">
                    <img src="{{asset($user->profile_photo)}}" alt="" class=" w-28 h-28 rounded-full object-cover">

                    <button @click="showUploadModal = true" class=" bg-gray-100  absolute right-0 bottom-0 h-10 w-10 flex justify-center items-center rounded-full">
                        <span class=" fa fa-camera"> </span>
                    </button>
                </div>
                <div class=" ">
                    <h1 class="text-xl md:text-2xl lg:text-4xl font-bold">{{$user->name}}</h1>
                    <a href="{{route('account-levels')}}" class=" flex items-center justify-center gap-2 mt-1 lg:justify-start lg:mt-2">
                        <img src="{{asset('badges/' . $user->level .'.png')}}" alt="" class=" h-5">
                        <h4>
                            {{ucfirst($user->level)}} Badge
                        </h4>
                    </a>
                </div>
            </div>

        </div>

        {{-- upload profile Image modal --}}
        <div x-show="showUploadModal" x-transition x-cloak class=" fixed left-0 top-0 w-full h-full z-30 bg-black/70 flex justify-center items-center p-2 md:p-4 ">
            <div class=" bg-gray-100 relative p-4 lg:p-6 w-full  max-w-xl rounded-md shadow-lg   overflow-y-auto" @click.outside="showUploadModal = !showUploadModal">
                <div class=" flex items-center justify-between mb-6">
                    <h2 class=" text-lg font-semibold  ">Update Profile Photo</h2>

                    <button class=" text-2xl lg:text-3xl text-gray-700" @click="showUploadModal = false">
                        <span class=" fa fa-close"></span>
                    </button>
                </div>

                <form action="" wire:submit.prevent='handleUpload'>
                    <div>
                        <h2 class=" font-medium  mb-6 ">Please upload a clear and good looking photo (max size: 5MB)</h2>

                        @if ($photo && $temporaryUrl)
                        <div>
                            <img src="{{ $temporaryUrl }}" alt="Preview" class=" rounded-full h-48 w-48 object-cover mx-auto ">
                        </div>
                        @else
                        <label for="payroll-upload" class=" flex items-center justify-center cursor-pointer mx-auto   bg-purple-100 rounded-full h-40 w-40">
                            <span class=" fa fa-user text-blue-500 fa-3x"></span>

                        </label>
                        @endif

                        <x-text-input id="payroll-upload" type="file" class=" block w-full mt-4 disabled:bg-gray-100 disabled:cursor-not-allowed border-0 mt-2" wire:model='photo' required />

                        <x-input-error class="mt-2" :messages="$errors->get('photo')" />
                    </div>


                    <div class=" w-full  mt-10 ">
                        <x-primary-button class=" w-4/5 mx-auto block py-3 rounded-full">Upload Image</x-primary-button>
                    </div>
                </form>

            </div>
        </div>

        {{-- delete Account Modal --}}
        <div x-show="showDeleteModal" x-transition x-cloak class=" fixed left-0 top-0 w-full h-full z-30 bg-black/70 flex justify-center items-center p-2 md:p-4 ">
            <div class=" bg-gray-100 relative p-4 lg:p-6 w-full  max-w-xl rounded-md shadow-lg   overflow-y-auto" @click.outside="showDeleteModal = !showDeleteModal">
                <div class=" flex items-center justify-between mb-6">
                    <h2 class=" text-lg font-semibold lg:text-xl  ">Delete Account</h2>

                    <button class=" text-2xl" @click="showDeleteModal = false">
                        <span class=" fa fa-close"></span>
                    </button>
                </div>

                <img src="{{asset('delete-account.png')}}" alt="" class=" h-32 mx-auto">

                <div class=" mt-6">
                    <h3 class=" text-gray-700 text-center lg:text-lg">Are you sure you want to delete your account?</h3>

                </div>

                <div class=" mt-6 space-y-3 lg:space-y-5">
                    <x-primary-button class=" w-full text-sm block  rounded-full bg-red-700" wire:click='deleteAccount'>Yes, Delete Account</x-primary-button>

                    <button class=" font-medium text-center block p-2  w-full" @click="showDeleteModal = false">
                        No, Cancel
                    </button>
                </div>
            </div>
        </div>


        <div class=" mt-6 lg:mt-8">
            <div class=" flex justify-between items-center">
                <h3 class=" text-lg font-medium lg:text-xl">Personal Details</h3>

                <a href="{{route('contact-support')}}" class="text-blue-500">
                    <span class=" fa fa-edit  text-lg lg:text-2xl"></span>
                    <span class=" hidden lg:inline-block lg:ml-1 font-medium">Edit</span>
                </a>
            </div>

            <div class=" bg-gray-100 p-4 rounded-xl mt-3 lg:mt-6 divide-y lg:text-lg">
                <p class=" flex justify-between items-center p-2 lg:py-3">
                    <span>
                        First Name:
                    </span>

                    <span class=" font-medium">
                        {{$user->first_name}}
                    </span>
                </p>

                {{-- last name --}}
                <p class=" flex justify-between items-center p-2 lg:py-3">
                    <span>
                        Last Name:
                    </span>

                    <span class=" font-medium">
                        {{$user->last_name}}
                    </span>
                </p>
                <p class=" flex justify-between items-center p-2 lg:py-3">
                    <span>
                        Other names:
                    </span>

                    <span class=" font-medium">
                        {{$user?->kyc?->other_names ?? '-'}}
                    </span>
                </p>
                <p class=" flex justify-between items-center p-2 lg:py-3">
                    <span>
                        Email:
                    </span>

                    <span class=" font-medium">
                        {{$user->email ?? '-'}}
                    </span>
                </p>



                {{-- Phone number --}}
                <p class=" flex justify-between items-center p-2 lg:py-3">
                    <span>
                        Phone Number:
                    </span>

                    <span class=" font-medium">
                        {{$user->phone}}
                    </span>
                </p>


                <p class=" flex justify-between items-center p-2 lg:py-3">
                    <span>
                        Address:
                    </span>

                    <span class=" font-medium">
                        {{$user->kyc?->address ?? '-'}}
                    </span>
                </p>

                <p class=" flex justify-between items-center p-2 lg:py-3">
                    <span>
                        Date of Birth:
                    </span>

                    <span class=" font-medium">
                        {{$user->kyc?->date_of_birth->format('d M Y') ?? '-'}}
                    </span>
                </p>

                <div class=" flex justify-between items-center p-2 lg:py-3">
                    <span>
                        Account Status:
                    </span>

                    <a href="{{route('account-levels')}}" class=" flex items-center gap-2 mt-1">
                        <img src="{{asset('badges/' . $user->level .'.png')}}" alt="" class=" h-5">
                        <h4>
                            {{ucfirst($user->level)}} Badge
                        </h4>
                    </a>
                </div>


            </div>
        </div>

        <div class=" mt-4 lg:mt-6">
            <h4 class=" font-semibold mb-1 lg:text-lg">
                {{ config('app.name') }} Tag
            </h4>

            <div class=" bg-gray-100 p-3 rounded-xl flex justify-between items-center lg:p-4">
                <img src="{{asset('icons/At.png')}}" alt="">

                <div class=" flex items-center gap-x-3">
                    <p class=" font-medium">
                        {{$user?->username ?? '-'}}
                    </p>

                    <a href="{{route('profile.change-fitpay-tag')}}">
                        <button class=" p-2 lg:py-3">
                            <span class=" fa fa-pencil text-blue-600"></span>
                        </button>
                    </a>
                </div>
            </div>
        </div>



        <button class=" text-center mt-10 mx-auto block " @click="showDeleteModal = true">
            <span class="fa fa-close  mx-auto rounded-full h-10 w-10 bg-red-700 text-white flex items-center justify-center mb-2"></span>
            <span class=" font-medium text-red-700  text-center text-lg">
                Delete Account
            </span>
        </button>

    </div>
</div>
