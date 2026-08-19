<div class=" bg-white">
    <div class=" px-4 py-6  bg-white min-h-screen max-w-4xl">

        <x-loading-spinner wire:target='updateAjoContribution, cover_image' />

        <div class=" border border-gray-300 p-4 rounded-xl lg:p-6">
            <img src="{{$ajoContribution->cover_photo_url}}" alt="" class="  h-20 w-20 rounded-full object-cover md:w-28 md:h-28 lg:h-36 lg:w-36">

            <h3 class="  font-medium mt-2 lg:mt-4 md:text-lg lg:text-xl lg:font-semibold ">{{$ajoContribution->name}}</h3>
            <div class="  mt-2">
                <div class=" text-sm flex justify-between items-center md:text-base">
                    <h3 class="">{{ucfirst($ajoContribution->type)}} Ajo Contribution</h3>

                    <p class="{{$ajoContribution->status}} {{$ajoContribution->status == 'active' ? 'bg-green-700 text-white' : 'bg-red-700 text-white'}}">{{ucfirst($ajoContribution->status)}} </p>
                </div>

                <p class=" text-3xl lg:text-4xl font-bold mt-1">
                    <x-naira-symbol /> {{formatNumber($contributionTotalAmount)}}
                </p>

                <p class=" text-sm mt-1 md:text-base md:mt-2">{{ucfirst($ajoContribution->frequency)}} contributions starting from: {{$ajoContribution->start_date->format('d M Y')}} </p>
            </div>

            @if ($ajoContribution->status == 'active')
            <div class="  rounded-lg flex items-center gap-x-4 mt-4 lg:mt-6" x-data="">
                <p class="font-semibold text-xl text-[#1400FB] ">
                    {{$ajoContribution->hash_id}}
                </p>

                <button class=" text-sm font-semibold text-[#1400FB]" onclick="copyToClipboard('{{$ajoContribution->hash_id}}')">
                    <span class=" fa fa-copy mr-1"></span> <span id='copy'>Copy Code</span>
                </button>
            </div>
            @endif


        </div>


        <div class=" mt-8 flex items-center justify-between gap-2 lg:mt-10">
            {{-- Add money --}}
            @if ( $isUserContributing && $ajoContribution->status == 'active')
            <div x-data="{showAddMoneyModal: false}">
                <button @click="showAddMoneyModal = !showAddMoneyModal" class="border border-[#1400FB] text-[#1400FB] flex items-center py-2 px-6 rounded-full lg:px-10 lg:py-3">
                    <span class="fa fa-circle-down  mr-2   flex justify-center items-center text-xl lg:text-2xl font-semibold rounded-xl ">
                    </span>

                    <p class=" text-center text-sm font-medium md:text-base ">
                        Deposit
                    </p>
                </button>

                {{-- Add money Modal --}}
                <div x-show="showAddMoneyModal" x-transition x-cloak class=" fixed bg-black/70 w-screen h-screen z-50 left-0 top-0 flex justify-center items-center p-4">

                    <div class=" bg-white p-4 rounded-md w-full lg:p-6 max-w-xl">

                        <div class=" flex justify-between items-center">
                            <h3 class=" text-lg font-semibold lg:text-xl">
                                Add money to Ajo Contribution
                            </h3>
                            <button @click="showAddMoneyModal = !showAddMoneyModal" class=" text-gray-600 font-semibold text-2xl lg:text-3xl">
                                <span class=" fa fa-close"></span>
                            </button>
                        </div>

                        @if (!$ajoContribution->start_date->lte(now()))
                        <p class=" text-base text-gray-700 text-center p-4 lg:text-lg mt-4">
                            <span class=" fa fa-info mr-2"></span>
                            Ajo Contribution has not started yet
                        </p>

                        @elseif ($hasUserDonated)
                        <p class=" text-base text-gray-700 text-center p-4 lg:text-lg mt-4">
                            <span class=" fa fa-info mr-2"></span>
                            You have already made a contribution for this round
                        </p>

                        @else

                        <form wire:submit.prevent='addMoney' class=" mt-6">
                            <p class=" text-center text-gray-700 lg:text-lg">
                                Amount
                            </p>

                            <p class=" text-2xl mb-3 lg:text-3xl text-primary font-semibold text-center md:mt-2">
                                <x-naira-symbol /> {{formatNumber($ajoContribution->amount)}}
                            </p>

                            <x-enter-pin id="add-money" />

                            <x-primary-button class=" w-full mt-4 lg:mt-6">
                                Add Money
                            </x-primary-button>
                        </form>
                        @endif

                    </div>
                </div>
            </div>
            @endif

            {{-- Edit Ajo Contribution --}}
            @if ( $ajoContribution->status == 'active' && $isAdmin)
            <div x-data="{showAddMoneyModal: false}">
                <button @click="showAddMoneyModal = !showAddMoneyModal" class="border border-[#1400FB] text-[#1400FB] flex items-center py-2 px-6 rounded-full lg:px-10 lg:py-3">
                    <span class="fa fa-edit  mr-2   flex justify-center items-center text-xl lg:text-2xl font-semibold rounded-xl ">
                    </span>

                    <p class=" text-center text-sm md:text-base font-medium ">
                        Edit
                    </p>
                </button>

                <div x-show="showAddMoneyModal" x-transition x-cloak class=" fixed bg-black/70 w-screen h-screen z-50 left-0 top-0 flex justify-center items-center p-4">

                    <div class=" bg-white p-4 rounded-md w-full lg:p-6 lg:max-w-xl">

                        <div class=" flex justify-between items-center">
                            <h3 class=" text-lg lg:text-xl font-semibold">
                                Edit Ajo Contribution
                            </h3>

                            <button @click="showAddMoneyModal = !showAddMoneyModal" class=" text-gray-600 font-semibold text-2xl lg:text-3xl">
                                <span class=" fa fa-close"></span>
                            </button>
                        </div>

                        <form class=" mt-6 space-y-4 lg:space-y-6 lg:mt-8" wire:submit.prevent='updateAjoContribution'>
                            <div>
                                <x-input-label for="name" :value="__('Contribution Name')" />
                                <x-text-input id="name" type="text" class="mt-2 block w-full" wire:model='name' required />
                                <x-input-error class="mt-2 " :messages="$errors->get('name')" />
                            </div>

                            <div>
                                @if ($ajoContribution->type === 'personal')
                                <x-input-label for="amount" :value="__('Number of Members(Including you)')" />
                                @else
                                <x-input-label for="amount" :value="__('Number of Members(Excluding you)')" />
                                @endif

                                <x-text-input id="amount" type="number" class="mt-2 block w-full" wire:model='no_of_members' required />
                                <x-input-error class="mt-2 " :messages="$errors->get('no_of_members')" />
                            </div>

                            <div>
                                <x-input-label for="cover_image" :value="__('New Contribution logo')" />

                                <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 mt-1 py-3" id="cover_image" type="file" wire:model='cover_image'>
                                <x-input-error :messages="$errors->get('cover_image')" class="mt-2" />

                            </div>

                            <x-primary-button class=" w-full px-8 mx-auto ">
                                Update Ajo Contribution
                            </x-primary-button>
                        </form>

                    </div>

                </div>
            </div>
            @endif

            {{-- End Ajo Contribution --}}
            @if ( $ajoContribution->status == 'active' && $isAdmin)
            <div x-data="{showAddMoneyModal: false}">
                <button @click="showAddMoneyModal = !showAddMoneyModal" class="border border-[#1400FB] text-[#1400FB] flex items-center py-2 px-6 rounded-full lg:px-10 lg:py-3">
                    <span class="fa fa-trash  mr-2   flex justify-center items-center text-xl lg:text-2xl font-semibold rounded-xl ">
                    </span>

                    <p class=" text-center text-sm md:text-base font-medium ">
                        End
                    </p>
                </button>

                <div x-show="showAddMoneyModal" x-transition x-cloak class=" fixed bg-black/70 w-screen h-screen z-50 left-0 top-0 flex justify-center items-center p-4">

                    <div class=" bg-white p-4 rounded-md w-full lg:p-6  max-w-xl">

                        <div class=" flex justify-between items-center">
                            <h3 class=" text-lg lg:text-xl font-semibold">
                                End Ajo Contribution
                            </h3>
                            <button @click="showAddMoneyModal = !showAddMoneyModal" class=" text-gray-600 font-semibold text-2xl lg:text-3xl">
                                <span class=" fa fa-close"></span>
                            </button>
                        </div>

                        <form class=" mt-6 lg:mt-8">
                            <p class=" text-center  text-gray-800 lg:text-lg">
                                Are you sure you want to end this Ajo Contribution?
                            </p>

                            <x-enter-pin id="end" />

                            <x-primary-button type="button" class="  mt-4 w-full px-8 mx-auto bg-red-700 lg:mt-6" wire:click='endAjoContribution'>
                                End Ajo Contribution
                            </x-primary-button>
                        </form>

                    </div>

                </div>
            </div>
            @endif

        </div>

        {{-- contribution Rounds --}}
        <div class=" mt-6 lg:mt-8">
            <h2 class=" text-lg font-medium  lg:text-xl lg:font-semibold">Contribution Rounds</h2>

            @if ($ajoContribution->activeRound()->status == 'pending')
            <p class="  text-sm text-red-700 bg-red-100 p-2 rounded-md mt-2">
                <span class=" fa fa-info mr-2"></span> A member(s) have not paid for the current round, so the Ajo Contribution is on hold until payment is complete.
            </p>
            @endif

            <div class=" space-y-3 mt-4 ">
                @forelse ($rounds as $round)
                <div class="  border border-gray-200 rounded-lg p-3 flex items-center justify-between lg:p-5">
                    <div>
                        <h3 class=" font-medium lg:text-lg">{{$round->beneficiary->name}}

                            @if ($ajoContribution->current_round == $round->order)
                            <span class=" text-xs md:text-sm text-green-600 font-medium">.current round</span>
                            @endif
                        </h3>
                        <p class=" font-bold text-xl mt-1 lg:mt-2">
                            <x-naira-symbol /> {{formatNumber($round->balance)}}
                        </p>

                    </div>

                    {{-- for tailwind css --}}
                    {{-- <div class="round-completed round-pending round-awaiting round-on-going">
    
                    </div> --}}

                    <div>
                        <p class=" font-medium bg-blue-100 text-[#1400FB] py-1 px-3 text-xs md:text-sm lg:text-base lg:px-4 rounded-full whitespace-nowrap ">
                            Round {{$round->order}}
                        </p>
                        <p class="round-{{$round->status}} text-center mt-1 md:text-sm lg:text-base">
                            {{$round->status}}
                        </p>
                    </div>
                </div>

                @empty

                <p class=" text-center text-gray-500 md:text-lg ">
                    No rounds yet
                </p>

                @endforelse
            </div>
        </div>

        {{-- turn on auto save --}}
        @if ($ajoContribution->status == 'active' && $ajoContribution->start_date->lte(now()) && $isUserContributing)
        <div class=" mt-6 lg:mt-8 bg-blue-100 rounded-xl p-4 flex justify-between items-center gap-4 lg:p-6 ">
            <div>
                <h2 class=" text-lg lg:text-xl font-semibold">
                    Auto-save
                </h2>
                <p class=" text-sm lg:text-base text-gray-700 mt-1">
                    save automatically daily, weekly or monthly with autosave
                </p>
            </div>

            <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                <input type="checkbox" wire:model.live="toggleAutoSave" id="toggle" class="toggle-checkbox absolute block w-6 h-6  rounded-full bg-white border-4 appearance-none cursor-pointer" />
                <label for="toggle" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label>
            </div>
        </div>
        @endif

        {{-- Ajo members --}}
        <h3 class=" text-lg lg:text-xl lg:font-semibold font-medium mt-6 mb-4">Members</h3>
        <div class=" space-y-4" wire:ignore>
            @foreach ($members as $user)
            <div class="p-3 lg:p-5 bg-white flex items-center gap-2 border border-gray-200 rounded-lg lg:gap-4">
                <img src="{{$user->profile_photo}}" alt="" class="w-12 h-12 rounded-full object-cover flex-shrink-0 lg:h-20 lg:w-20">
                <div class="space-y-1 lg:space-y-2 w-full">
                    <div class="flex justify-between items-center gap-2">
                        <h4 class="font-medium lg:text-lg">
                            {{$user->name}}
                            <span class="text-xs md:text-sm text-gray-700 ml-1">.{{$user?->pivot?->role}}</span>
                            @if ($user->id == auth()->id())
                            <span class="font-semibold text-sm text-primary ml-1">.You</span>
                            @endif
                        </h4>

                        @if ($user?->pivot?->order_number)
                        <p class="font-medium bg-blue-100 text-[#1400FB] py-2 px-3 text-xs md:text-sm lg:text-base lg:px-4 rounded-full whitespace-nowrap">
                            Round {{$user->pivot->order_number}}
                        </p>
                        @endif
                    </div>

                    <p class="text-sm lg:text-base text-gray-600">
                        {{$user->email}}
                    </p>
                    <p class="text-sm text-gray-600 lg:text-base font-medium">
                        {{$user->phone}}
                    </p>

                    @if ($user?->pivot?->is_contributing)
                    <div class="flex items-center gap-2  pt-2">
                        @foreach ($ajoContribution->rounds as $round)

                        <div>
                            @php
                            $hasDonated = $round->donations()->where('user_id', $user->id)->exists();
                            $isCurrentUser = $user->id == auth()->id();
                            $isAdminUser = $user->id == $adminUser->id;
                            $joinedLate = $round->order < $activeRound->order && $round->updated_at < $user->pivot->created_at;
                                    @endphp

                                    @if ($round->order > $activeRound->order)
                                    {{-- Future rounds - Yellow --}}
                                    <div class="h-6 w-6 md:h-8 md:w-8 rounded-full flex justify-center items-center bg-amber-500">
                                        {{$round->order}}
                                    </div>
                                    @elseif ($joinedLate)
                                    {{-- For rounds that took place before the user joined - gray --}}
                                    <div class="h-6 w-6 md:h-8 md:w-8 rounded-full flex justify-center items-center bg-gray-500">
                                        {{$round->order}}
                                    </div>
                                    @elseif ($hasDonated)
                                    {{-- Rounds with donation - Green --}}
                                    <div class="h-6 w-6 md:h-8 md:w-8 rounded-full flex justify-center items-center bg-green-500">
                                        {{$round->order}}
                                    </div>
                                    @elseif ($round->order <= $activeRound->order && !$hasDonated)
                                        @if ($isAdmin)
                                        {{-- admin is supposed to report to admin - red--}}
                                        <div class="h-6 w-6 md:h-8 md:w-8 rounded-full flex justify-center items-center bg-red-500">
                                            {{$round->order}}
                                        </div>
                                        @elseif ($isCurrentUser || $isAdminUser)
                                        {{-- Auth user should not be able report themselves or report an admin--}}
                                        <div class="h-6 w-6 md:h-8 md:w-8 rounded-full flex justify-center items-center bg-red-500">
                                            {{$round->order}}
                                        </div>
                                        @else
                                        {{-- Other non-admin users' unpaid rounds with report option --}}
                                        <div x-data="{showModal: false}">
                                            <button @click="showModal = !showModal" class="h-6 w-6 md:h-8 md:w-8 rounded-full flex justify-center items-center bg-red-500">
                                                {{$round->order}}
                                            </button>

                                            {{-- Report member modal --}}
                                            <div x-show="showModal" x-cloak x-transition class="fixed top-0 left-0 z-10 bg-black/50 w-full h-full flex items-end overflow-hidden lg:items-center lg:justify-center">
                                                <div class="bg-white w-full rounded-t-2xl px-4 py-6 lg:p-6 max-w-xl">
                                                    <div class="flex justify-end items-center">
                                                        <button @click="showModal = !showModal" class="text-gray-600 font-semibold text-2xl">
                                                            <span class="fa fa-close"></span>
                                                        </button>
                                                    </div>

                                                    <img src="{{asset('report user.png')}}" alt="" class="mx-auto h-20 lg:h-28">

                                                    <h3 class="text-lg lg:text-xl font-semibold text-center mt-4">
                                                        Report {{$user->name}}
                                                    </h3>

                                                    <p class="text-sm md:text-base lg:text-lg text-gray-600 text-center">
                                                        Are you sure you want to report {{$user->name}} to the Admin for defaulting payment?
                                                    </p>

                                                    <div class="grid grid-cols-2 gap-4 mt-6 lg:mt-8">
                                                        <x-primary-button class="bg-red-700" wire:click='reportMember({{$user->id}}, {{$round->order}})'>
                                                            Report
                                                        </x-primary-button>

                                                        <x-secondary-button class="text-gray-700 border-gray-700" @click="showModal = !showModal">
                                                            Cancel
                                                        </x-secondary-button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        @endif
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>


            </div>
            @endforeach



        </div>



    </div>
</div>