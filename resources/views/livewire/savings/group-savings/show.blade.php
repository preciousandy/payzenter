<div class=" px-4 py-6 max-w-6xl">
    <div class=" bg-white p-4 rounded-xl flex items-center gap-2 lg:gap-4 lg:shadow lg:p-6">
        <img src="{{asset('savings-interest.png')}}" alt="" class=" h-16 lg:h-24">
        <div class=" flex-1 space-y-2 md:space-y-4 lg:space-y-6">
            <div class=" flex items-center justify-between gap-4">
                <h3 class=" text-lg md:text-xl  font-semibold">
                    {{ $savings->name }}
                </h3>

                <p class=" text-sm md:text-base text-gray-600 font-medium">
                    {{$savings->type}}
                </p>
            </div>
            <div class=" text-sm  flex justify-between w-full">
                {{-- <p>{{$savings->members->count()}} members</p> --}}
                <p class=" text-gray-500 md:text-lg">{{$savings->end_date->diffInDays()}} days left</p>

                {{-- End savings modal --}}
                <div x-data="{showEndSavingsModal: false}">
                    {{-- only admin can end savings --}}
                    @if ($is_admin && $savings->status == 'active')
                    <x-primary-button @click="showEndSavingsModal = !showEndSavingsModal" class="  whitespace-nowrap text-xs  py-1 bg-red-600 px-2 rounded-md md:text-sm lg:text-base md:py-2 md:px-4 lg:px-6">
                        End savings
                    </x-primary-button>
                    @endif



                    <div x-show="showEndSavingsModal" x-transition x-cloak class=" fixed bg-black/70 w-screen h-screen z-50 left-0 top-0 flex justify-center items-center p-4">

                        <div class=" bg-white p-4 rounded-md w-full max-w-lg lg:p-6">
                            <div class=" flex justify-between items-center gap-x-4">
                                <h3 class=" text-lg md:text-xl font-semibold">
                                    Close savings Account
                                </h3>
                                <button @click="showEndSavingsModal = !showEndSavingsModal" class="  font-semibold text-2xl lg:text-3xl">
                                    <span class=" fa fa-close"></span>
                                </button>
                            </div>

                            <p class=" text-sm md:text-base md:text-center lg:text-lg text-gray-600 mt-2 md:mt-4">
                                Closing this account will transfer the balance to your wallet and the account will become inactive.
                            </p>

                            <form wire:submit.prevent='endSavings' class=" mt-4 lg:mt-6 ">

                                <x-enter-pin />

                                <x-primary-button class=" w-full mt-4 lg:mt-6">
                                    Close Account
                                </x-primary-button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>

            <div class=" flex items-center justify-between mt-4">

                {{-- edit savings --}}
                @if ($savings->status !== 'closed' && $is_admin)
                <div x-data="{showEditModal: false}">
                    <button @click="showEditModal = !showEditModal" class=" bg-gray-200 py-2 px-4  rounded-lg font-semibold text-xs  cursor-pointer w-full md:px-6 lg:py-3 md:text-sm lg:text-base">
                        <span class=" fa fa-edit"></span> Edit
                    </button>


                    <div x-show="showEditModal" x-transition x-cloak class=" fixed bg-black/70 w-screen h-screen z-50 left-0 top-0 flex justify-center items-center p-4">

                        <div class=" bg-white p-4 rounded-md w-full max-w-lg lg:p-6">

                            <div class=" flex justify-between items-center">
                                <h3 class=" text-lg md:text-xl font-semibold">
                                    Edit Savings
                                </h3>
                                <button @click="showEditModal = !showEditModal" class=" text-primary font-semibold text-2xl lg:text-3xl">
                                    <span class=" fa fa-close"></span>
                                </button>
                            </div>

                            <form wire:submit.prevent='update' class=" mt-4 md:mt-6">

                                <div>
                                    <x-input-label for="name" :value="__('Savings Name')" />
                                    <x-text-input id="name" class="block mt-1 w-full placeholder:text-gray-400" type="text" wire:model="name" required autofocus />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                <x-primary-button class=" w-full mt-4 md:mt-6">
                                    Update Savings
                                </x-primary-button>
                            </form>

                        </div>

                    </div>
                </div>
                @endif

                <div></div>

                {{-- invite friends --}}
                <div x-data="{showInviteModal: false}">

                    @if ( $savings->status == 'active')
                    <button @click="showInviteModal = !showInviteModal" class=" text-primary font-semibold text-xs md:text-sm lg:text-base lg:p-3 p-2   cursor-pointer w-full">
                        <span class=" fa fa-user-plus mr-1"></span> Invite
                    </button>
                    @endif


                    <div x-show="showInviteModal" x-transition x-cloak class=" fixed bg-black/70 w-screen h-screen z-50 left-0 top-0 flex justify-center items-center p-4">

                        <div class=" bg-white p-4 rounded-md w-full lg:p-6 max-w-lg ">

                            <div class=" flex justify-between items-center">
                                <h3 class=" text-lg md:text-xl font-semibold">
                                    Invite Code
                                </h3>
                                <button @click="showInviteModal = !showInviteModal" class=" text-primary font-semibold text-2xl lg:text-3xl">
                                    <span class=" fa fa-close"></span>
                                </button>
                            </div>

                            <p class=" mt-4 md:mt-6 md:text-center md:text-lg">
                                Invite your friends to join your savings account
                            </p>


                            <p class=" text-sm bg-gray-200 p-3 rounded-md mt-2 md:mt-4 ">
                                {{ $savings->hash_id}}
                            </p>



                            <x-primary-button class=" w-full mt-4 md:mt-6" @click="copyToClipboard('{{$savings->hash_id}}')" type="button">
                                <span class="fa fa-copy mr-2"></span> <span id="copy">Copy Code</span>
                            </x-primary-button>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>



    @if ($savings->status === 'closed')

    <div class=" bg-white p-4 lg:p-6 lg:shadow rounded-xl text-center  mt-6 lg:mt-8">
        <p class="text-xl md:text-2xl font-bold text-red-700">
            Group Savings Closed
        </p>
    </div>

    @else


    @if ($savings->type === 'flexible')
    <div class=" bg-white p-4 lg:p-6 lg:shadow rounded-xl text-center  mt-6">
        <h2 class=" md:text-lg lg:font-medium">Target Amount</h2>
        <p class="text-2xl md:text-3xl font-bold mt-2 lg:mt-4">
            <x-naira-symbol /> {{formatNumber($savings->target_amount)}}
        </p>
    </div>
    @endif



    <div class=" bg-white p-4 lg:p-6 lg:shadow rounded-xl text-center  mt-6">
        <h2 class=" md:text-lg lg:font-medium">Wallet Balance</h2>
        <p class="text-4xl lg:text-5xl font-bold mt-2 lg:mt-4">
            <x-naira-symbol /> {{formatNumber($savings->balance)}}
        </p>
    </div>


    <div class=" grid grid-cols-2 gap-x-3 mt-2 md:mt-4 lg:mt-6">

        {{-- add money modal --}}
        <div x-data="{showAddMoneyModal: false}">
            <x-primary-button @click="showAddMoneyModal = !showAddMoneyModal" class=" w-full ">
                Add Money
            </x-primary-button>


            <div x-show="showAddMoneyModal" x-transition x-cloak class=" fixed bg-black/70 w-screen h-screen z-50 left-0 top-0 flex justify-center items-center p-4">

                <div class=" bg-white p-4 rounded-md w-full lg:p-6 max-w-lg">

                    <div class=" flex justify-between items-center">
                        <h3 class=" text-lg md:text-xl font-semibold">
                            Add money to Savings
                        </h3>
                        <button @click="showAddMoneyModal = !showAddMoneyModal" class=" text-primary font-semibold text-2xl md:text-3xl">
                            <span class=" fa fa-close"></span>
                        </button>
                    </div>

                    <form class=" mt-6">
                        <div>
                            <x-input-label for="name" :value="__('Amount')" />

                            <p class=" text-sm md:text-base mt-1 md:mt-2">Wallet Balance:
                                <span class=" text-primary font-medium">
                                    <x-naira-symbol /> {{formatNumber(auth()->user()->wallet_balance)}}
                                </span>
                            </p>

                            <x-text-input id="name" class="block mt-1 md:mt-2 w-full placeholder:text-gray-400" type="number" wire:model="amount" required />
                            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                        </div>

                        <x-enter-pin id="group-add-money" />

                        <x-primary-button type="button" class=" w-full mt-4 lg:mt-6" wire:click='addMoney'>
                            Add Money
                        </x-primary-button>
                    </form>

                </div>

            </div>
        </div>

        {{-- withdraw money modal --}}
        <div x-data="{showWithdrawModal: false}">
            {{-- only admin can withdraw --}}
            @if ($is_admin)
            <x-primary-button @click="showWithdrawModal = !showWithdrawModal" class=" w-full ">
                Withdraw
            </x-primary-button>
            @endif



            <div x-show="showWithdrawModal" x-transition x-cloak class=" fixed bg-black/70 w-screen h-screen z-50 left-0 top-0 flex justify-center items-center p-4">

                <div class=" bg-white p-4 rounded-md w-full max-w-lg lg:p-6">

                    <div class=" flex justify-between items-center">
                        <h3 class=" text-lg lg:text-xl font-semibold">
                            Withdraw money to wallet
                        </h3>
                        <button @click="showWithdrawModal = !showWithdrawModal" class=" text-primary font-semibold text-2xl lg:text-3xl">
                            <span class=" fa fa-close"></span>
                        </button>
                    </div>

                    <form wire:submit.prevent='withDraw' class=" mt-6 space-y-4">

                        <div>
                            <x-input-label for="withdraw_amount" :value="__('Amount')" />

                            <x-text-input id="withdraw_amount" class="block mt-1 w-full placeholder:text-gray-400" type="number" wire:model="withdraw_amount" required autofocus />
                            <x-input-error :messages="$errors->get('withdraw_amount')" class="mt-2" />

                            <p class=" text-sm md:text-base text-gray-700 mt-1">
                                Withdrawal attracts a fee of <b>
                                    <x-naira-symbol /> {{$withdrawal_fee}}
                                </b>
                            </p>

                        </div>

                        <div class="">
                            <x-input-label for="name" :value="__('Withdrawal Reason')" />

                            <textarea name="" id="" rows="3" class=" mt-2 w-full border border-gray-300 rounded-lg" wire:model='withdrawal_reason'></textarea>

                            <x-input-error :messages="$errors->get('withdrawal_reason')" class="mt-2" />
                        </div>

                        <x-enter-pin id="group-withdraw-money" />

                        <x-primary-button class=" w-full">
                            Withdraw Money
                        </x-primary-button>
                    </form>

                </div>

            </div>
        </div>

    </div>
    @endif



    <div class=" mt-10" x-data="{tab: 'Members'}">
        <div class=" mb-6  flex justify-between items-center">
            <button class=" text-base font-semibold text-gray-600 px-4 py-2  inline-block" :class="tab === 'Members' ? '    text-primary border-b-4 border-primary'  : ''" @click="tab = 'Members'">Members</button>

            <button class=" text-base font-semibold text-gray-600 px-4 py-2 inline-block" :class="tab === 'Activities' ? 'text-primary border-b-4 border-primary' : ''" @click="tab = 'Activities'">Activities</button>
        </div>


        {{-- savings members --}}
        <div class="  grid gap-4 lg:grid-cols-2 lg:gap-6 " x-show="tab == 'Members'" x-transition x-cloak wire:ignore>
            @foreach ($members as $user)
            <div class=" p-3 md:p-4 lg:p-6  bg-white lg:shadow-sm rounded-lg">
                <div class="   flex items-start gap-2 md:gap-4">
                    <img src="{{asset($user->profile_photo)}}" alt="" class=" w-12 h-12 lg:h-16 lg:w-16 rounded-full object-cover flex-shrink-0">
                    <div class=" space-y-2 w-full">
                        <div class=" flex justify-between items-center">
                            <h4 class=" font-medium mb-1 lg:text-lg lg:font-semibold">{{$user->name}}
                                @if ($user->id == auth()->id())
                                <span class=" font-semibold text-sm text-primary"> .You</span>
                                @endif
                            </h4>
                            @if ($user && $user->pivot)
                            <p class="  text-xs md:text-sm lg:text-base {{$user?->pivot->status}}">
                                {{$user?->pivot->status}}
                            </p>
                            @endif

                        </div>


                        @if ($savings->type === 'strict')
                        <div class=" text-sm md:text-base">
                            <h4>Member Estimated Amount:</h4>
                            <p class=" text-green-700 font-medium ">
                                <x-naira-symbol /> {{formatNumber($savings->member_target_amount)}}
                            </p>
                        </div>
                        @endif


                        <div class=" text-sm md:text-base">
                            <h4>Active Savings:</h4>
                            <p class=" text-green-700 font-medium ">
                                <x-naira-symbol /> {{formatNumber($savings->donations()->where('user_id', $user->id)->sum('amount'))}}
                            </p>
                        </div>

                    </div>
                </div>

                @if ( $user && $user->pivot && $user->pivot->status === 'active' && $savings->status == 'active')
                <div class=" grid grid-cols-2 gap-3 md:gap-4 lg:gap-6 mt-2 md:mt-4">
                    @if ($savings->members->find($user->id)->pivot->role === 'member' && $savings->members->find(Auth::user()->id)->pivot->role === 'admin')

                    {{-- make admin modal --}}
                    <div x-data="{showMakeAdminModal: false}">
                        <x-primary-button class=" w-full text-xs py-2 md:text-sm
                         lg:text-base lg:p-3 rounded-lg" @click="showMakeAdminModal = !showMakeAdminModal">
                            Make Admin
                        </x-primary-button>

                        <div x-show="showMakeAdminModal" x-transition x-cloak class=" fixed bg-black/70 w-screen h-screen z-50 left-0 top-0 flex justify-center items-center p-4">

                            <div class=" bg-white p-4 rounded-md w-full max-w-lg lg:p-6">

                                <div class=" flex justify-between items-center">
                                    <h3 class=" text-lg lg:text-xl font-semibold">
                                        Make Admin
                                    </h3>
                                    <button class=" text-primary font-semibold text-2xl lg:text-3xl" @click="showMakeAdminModal = !showMakeAdminModal">
                                        <span class=" fa fa-close"></span>
                                    </button>
                                </div>

                                <p class=" text-center md:text-lg  mt-4 md:mt-6">
                                    Are you sure you want to make this user an admin? <br>
                                    The user will have the same privileges as you.
                                </p>

                                <x-primary-button class=" w-full whitespace-nowrap mt-8" wire:click="makeUserAdmin({{$user->id}})">
                                    Yes, Make Admin
                                </x-primary-button>

                            </div>

                        </div>
                    </div>
                    @endif

                    {{-- remove member --}}
                    @if ($user->id !== auth()->id())
                    <div x-data="{showRemoveMemberModal: false}">

                        @if ($savings->members->find(auth()->user()->id)->pivot->role === 'admin' && $savings->status == 'active')
                        <x-primary-button @click="showRemoveMemberModal = !showRemoveMemberModal" class=" w-full py-2 text-xs rounded-lg md:text-sm
                         lg:text-base lg:p-3">
                            Remove Member
                        </x-primary-button>
                        @endif


                        <div x-show="showRemoveMemberModal" x-transition x-cloak class=" fixed bg-black/70 w-screen h-screen z-50 left-0 top-0 flex justify-center items-center p-4">

                            <div class=" bg-white p-4 rounded-md w-full max-w-lg lg:p-6">

                                <div class=" flex justify-between items-center">
                                    <h3 class=" text-lg lg:text-xl font-semibold">
                                        Remove Member
                                    </h3>
                                    <button @click="showRemoveMemberModal = !showRemoveMemberModal" class=" text-primary font-semibold text-2xl lg:text-3xl">
                                        <span class=" fa fa-close"></span>
                                    </button>
                                </div>

                                <form class=" mt-4 md:mt-6">
                                    <div>
                                        <x-input-label for="name" :value="__('Reason to remove member')" />

                                        <textarea name="" id="" rows="4" class=" mt-2 w-full border border-gray-300 rounded-xl" wire:model='reason'></textarea>
                                        <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                                    </div>

                                    <x-primary-button class=" w-full mt-4 md:mt-6" type="button" wire:click='removeMember({{$user->id}})'>
                                        Remove Member
                                    </x-primary-button>
                                </form>

                            </div>

                        </div>
                    </div>
                    @endif
                </div>
                @endif
            </div>
            @endforeach
        </div>




        {{-- savings activities --}}
        <div class=" space-y-4" x-show="tab == 'Activities'" x-transition x-cloak>
            @forelse ($activities as $activity)
            <div class=" bg-white p-4 rounded-md lg:shadow-sm">
                <h4 class=" font-medium mb-1 text-sm
                 lg:text-base">{{$activity->message}}</h4>

                <p class=" text-sm text-gray-600">
                    {{$activity->created_at->diffForHumans()}}
                </p>
            </div>
            @empty

            <div class=" p-6 text-center text-gray-700">
                No activities yet.
            </div>

            @endforelse
        </div>
    </div>
</div>