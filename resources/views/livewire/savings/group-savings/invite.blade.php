<div class=" px-4 py-6">
    @if ($isAleardyAMember)

    <div class=" p-6 bg-white rounded-lg lg:shadow-sm text-center lg:max-w-lg lg:mx-auto">
        <p class=" md:text-lg lg:text-xl">
            You are already a member of this group savings
        </p>
        <a href="{{route('savings.group-savings.show', $savings->hash_id)}}" class=" inline-block mt-4 lg:mt-6 lg:w-80 ">
            <x-primary-button class=" w-full">
                Go to Savings
            </x-primary-button>
        </a>

        <a href="{{route('home')}}" class=" font-semibold p-3 text-primary block  w-fit mx-auto mt-2 lg:mt-4">
            Go Home
        </a>
    </div>

    @else
    <div class=" max-w-2xl lg:mx-auto ">
        <h2 class=" text-lg lg:text-xl font-semibold">Hello, {{auth()->user()->name}}</h2>

        <p class=" mt-2 lg:text-lg lg:mt-4">
            You have been invited to join a group savings account with the following details
        </p>

        <div class=" bg-blue-100 p-4 rounded-md space-y-4 mt-4 lg:p-6 lg:text-lg">
            <div>
                <p class=" font-medium">Name:</p>
                <p class=" text-gray-800">{{$savings->name}}</p>
            </div>
            <div>
                <p class=" font-medium">Description:</p>
                <p class=" text-gray-800">{{$savings->description}}</p>
            </div>
            <div>
                <p class=" font-medium">Type:</p>
                <p class=" text-gray-800">{{$savings->type}}</p>
            </div>

            @if ($savings->type === 'flexible')
            <div>
                <p class=" font-medium">Savings target amount:</p>
                <p class=" text-gray-800">
                    <x-naira-symbol /> {{formatNumber($savings->target_amount)}}
                </p>
            </div>

            @else

            <div>
                <p class=" font-medium">Each member target amount:</p>
                <p class=" text-gray-800">
                    <x-naira-symbol /> {{formatNumber($savings->member_target_amount)}}
                </p>
            </div>


            @endif

            <div>
                {{-- savings members --}}
                <h3 class=" font-semibold mb-2 md:text-lg lg:text-xl">
                    Group Members
                </h3>
                <div class=" grid gap-4 lg:grid-cols-2  ">
                    @foreach ($savings->members as $user)
                    <div class=" py-4 px-2 flex items-center gap-2">
                        <img src="{{asset($user->profile_photo)}}" alt="" class=" w-12 h-12 lg:h-14 lg:w-14 rounded-full object-cover">
                        <div>
                            <h4 class=" font-medium mb-1 lg:text-lg lg:font-semibold">
                                {{$user->name}}
                            </h4>
                            <p class=" text-sm md:text-base text-gray-700">
                                {{$user->pivot->role}}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>

        <div class="  mt-8 grid lg:grid-cols-2 gap-4">
            <x-primary-button wire:click='accept' class="  block">
                Accept Invite
            </x-primary-button>

            <button class=" font-semibold text-red-600  py-2 text-center cursor-pointer   block" wire:click='decline'>
                Decline Invite
            </button>
        </div>
    </div>
    @endif

</div>