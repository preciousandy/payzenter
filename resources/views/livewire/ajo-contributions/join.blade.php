<div class=" bg-white">
    <div class=" min-h-screen  bg-white flex flex-col justify-center  px-4 py-8 max-w-2xl mx-auto">
        <div class=" ">
            {{-- <span class="fa fa-user-plus text-primary block w-fit mx-auto text-7xl"></span> --}}

            @if ($ajoContribution)

            <h2 class=" text-lg lg:text-xl font-semibold">Hello, {{auth()->user()->name}}</h2>

            <p class=" mt-2 lg:text-lg">
                You have been invited to join this Ajo Contribution with the following details
            </p>

            <div class=" bg-[#E2E8FD] p-4 rounded-md space-y-3 lg:space-y-5 mt-4 text-sm lg:p-6 lg:text-base">
                <img src="{{$ajoContribution->cover_photo_url}}" alt="" class=" mb-2 h-20 w-20 lg:h-32 lg:w-32 md:mb-4 lg:mb-6 rounded-full object-cover mx-auto">
                <div>
                    <h4 class=" lg:text-lg lg:mb-1 text-gray-700">Name:</h4>
                    <p class=" text-lg font-medium">{{$ajoContribution?->name}} ({{$ajoContribution->members->count()}} members)</p>
                </div>
                <div>
                    <h4 class=" lg:text-lg lg:mb-1 text-gray-700">Description:</h4>
                    <p class=" text-lg font-medium">{{$ajoContribution?->description}} </p>
                </div>
                <div>
                    <h4 class=" lg:text-lg lg:mb-1 text-gray-700">Contribution Amount:</h4>
                    <p class=" text-lg font-medium">
                        <x-naira-symbol />{{formatNumber($ajoContribution?->amount)}}
                    </p>
                </div>
                <div>
                    <h4 class=" lg:text-lg lg:mb-1 text-gray-700">Status:</h4>
                    <p class=" text-lg font-medium">
                        {{ucfirst($ajoContribution?->status)}}
                    </p>
                </div>
                <div>
                    <h4 class=" lg:text-lg lg:mb-1 text-gray-700">Start Date:</h4>
                    <p class=" text-lg font-medium">{{formatDate($ajoContribution?->start_date)}}</p>
                </div>
            </div>

            <x-primary-button class=" mt-8  block  w-4/5 mx-auto py-3 lg:w-80" wire:click='joinAjoContribution'>
                Proceed
            </x-primary-button>

            <a href="{{route('home')}}">
                <button class=" font-semibold text-red-600  py-2 text-center cursor-pointer mt-4  w-4/5 mx-auto block  rounded-md lg:w-80" wire:click='decline'>
                    Decline Invite
                </button>
            </a>

            @else

            <h1 class=" text-xl lg:text-2xl font-bold text-center mt-6  uppercase">Join Ajo Contribution</h1>

            <p class=" text-center text-lg mt-4">
                Enter group code below to join
            </p>

            <div class=" mt-4 lg:mt-6">
                <div class=" mx-auto">
                    <x-input-label for="name" :value="__('Enter Ajo Contribution group code')" />
                    <x-text-input id="name" type="text" class="mt-2 block w-full bg-gray-50" wire:model='code' required autofocus />
                    <x-input-error class="mt-2 " :messages="$errors->get('code')" />
                </div>


                <x-primary-button class=" mt-4  block  w-full  lg:mt-6" wire:click='checkAjoContribution'>
                    Join Ajo Contribution
                </x-primary-button>


            </div>



            @endif





        </div>

    </div>
</div>