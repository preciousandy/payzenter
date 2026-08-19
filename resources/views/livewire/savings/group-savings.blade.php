<div class=" max-w-6xl">

    <div class=" px-4 py-6">
        <h2 class=" text-lg font-semibold md:text-xl lg:text-2xl ">Hi, {{auth()->user()->name}} 👋</h2>
        <p class=" text-gray-600 mt-1 text-sm md:text-base lg:text-lg lg:mt-2">Don't forget to save your money!</p>

        <div class=" p-4 rounded-2xl shadow-md mt-3 lg:mt-5 lg:p-6 relative min-h-[15rem] lg:min-h-80  overflow-hidden flex items-center">
            <div class=" relative z-20  w-full">
                <div class=" flex items-center justify-between ">
                    <div class="">
                        <h3 class=" font-medium text-gray-600 mb-3 md:text-lg">Your total Savings</h3>

                        <p>
                            <span class="text-3xl lg:text-5xl font-extrabold">₦ {{formatNumber($totalSavingsDonations)}}</span>
                        </p>
                    </div>

                </div>

                <a href="{{route('savings.group-savings.create')}}" class=" mt-8 block w-60 lg:w-80 mx-auto">
                    <x-primary-button class=" py-3 w-full">
                        Create New Savings
                    </x-primary-button>
                </a>
            </div>

            <img src="{{asset('group-savings-icon.png')}}" alt="" class=" absolute right-2 top-2 h-full ">
        </div>

        <div x-data="{showCodeInput: false}">
            <div class=" flex justify-between items-center gap-4 mt-8">
                <h3 class=" text-lg md:text-xl lg:text-2xl font-semibold text-gray-700 ">
                    Group Savings
                </h3>

                <div class=" flex items-center gap-2">
                    <x-primary-button class=" text-xs md:text-sm lg:text-base lg:py-3 lg:px-6 px-4 py-2" @click="showCodeInput = !showCodeInput">
                        <span class="fa fa-plus-circle mr-1  whitespace-nowrap"></span> Join
                    </x-primary-button>
                    <a href="{{route('savings.group-savings.history')}}">
                        <x-secondary-button class=" text-xs px-4 py-2 whitespace-nowrap md:text-sm lg:text-base lg:py-3 lg:px-6">
                            <span class="fa fa-clock-rotate-left mr-1"></span> History
                        </x-secondary-button>
                    </a>
                </div>
            </div>

            <div class=" mt-4 bg-gray-100 p-4 rounded-lg lg:shadow" x-show="showCodeInput" x-cloak x-transition>
                <h3 class=" font-medium text-sm md:text-base text-gray-700">Enter Invite Code</h3>


                <x-text-input wire:model='groupCode' class=" w-full bg-white p-3 mt-2" />
                <x-input-error class="mt-2 " :messages="$errors->get('groupCode')" />

                <x-primary-button wire:click='checkInvite' class=" mt-4  w-full rounded-lg  lg:w-80 lg:mx-auto lg:mt-6 ">
                    <span>Join Group</span> <span class=" fa fa-chevron-right ml-2"></span>
                </x-primary-button>
            </div>
        </div>


        <div class=" mt-6 lg:mt-10 grid grid-cols-1 gap-4 lg:grid-cols-2 lg:gap-6">
            @forelse ($savings as $item)
            <x-group-savings-item :item="$item" />
            @empty
            <div class="  p-6 ">
                <p class=" text-center">You are not a member of any group savings</p>
            </div>
            @endforelse
        </div>
    </div>


</div>