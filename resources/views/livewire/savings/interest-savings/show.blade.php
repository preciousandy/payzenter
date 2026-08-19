<div>
    <div class=" px-4 py-6 max-w-5xl">
        <div class=" bg-white p-6 rounded-xl flex items-center gap-4 lg:shadow lg:p-8">
            <img src="{{asset('savings-interest.png')}}" alt="" class=" h-16 lg:h-28">
            <div class=" flex-1 space-y-2 lg:space-y-4">
                <h3 class=" text-lg font-semibold md:text-xl lg:text-2xl">
                    {{ $savings->name }}
                </h3>
                <div class=" flex  items-center gap-2 w-full">
                    <p class=" text-green-700 font-medium lg:text-lg">
                        {{$savings->type == 'locked' ? $lockedSavingsInterestRate : $unlockedSavingsInterestRate}}%
                    </p>
                    @if ($savings->type === 'locked')
                    <div class=" text-sm md:text-base lg:text-lg text-gray-500 ">
                        <p>{{$savings->end_date->diffForHumans()}} </p>
                    </div>
                    @endif
                </div>

                <div class=" flex justify-between items-center">
                    <p class=" lg:text-lg"> <span class="fa fa-{{$savings->type == 'locked' ? 'lock' : 'lock-open'}} mr-1"></span> {{$savings->type}}</p>

                    {{-- close savings modal --}}
                    @if ($savings->status !== 'closed' && $savings->type === 'unlocked')
                    <div x-data="{showCloseModal: false}">
                        <button @click="showCloseModal = !showCloseModal" class=" text-white bg-red-700  font-semibold text-xs md:text-sm lg:text-base lg:px-6 lg:py-3 px-4 py-2 rounded-full cursor-pointer">
                            <span class=" fa fa-trash mr-1"></span> End Savings
                        </button>

                        <div x-show="showCloseModal" x-transition x-cloak class=" fixed bg-black/70 w-screen h-screen z-50 left-0 top-0 flex justify-center items-center p-4">

                            <div class=" bg-white p-4 rounded-md w-full space-y-4 lg:p-6 lg:max-w-xl lg:space-y-6">

                                <div class=" flex justify-between items-center">
                                    <h3 class=" text-xl lg:text-2xl font-semibold">
                                        Close savings Account
                                    </h3>
                                    <button @click="showCloseModal = !showCloseModal" class=" text-primary font-semibold text-2xl lg:text-3xl">
                                        <span class=" fa fa-close"></span>
                                    </button>
                                </div>

                                <p class=" text-sm md:text-base lg:text-lg text-gray-700">
                                    Closing this account will transfer the balance to your wallet and the account will become inactive.
                                </p>
                                <p class=" text-sm md:text-base lg:text-lg text-gray-700">
                                    This will attract a fee of <b>
                                        <x-naira-symbol /> {{$fee}}
                                    </b>
                                </p>

                                <form wire:submit.prevent='closeAccount' class=" ">

                                    <x-enter-pin id="close-savings" />

                                    <x-primary-button class=" w-full mt-4">
                                        Close Account
                                    </x-primary-button>
                                </form>

                            </div>

                        </div>
                    </div>
                    @endif
                </div>



            </div>
        </div>



        @if ($savings->status === 'closed')

        <div class=" bg-white p-4 lg:p-6 lg:shadow-sm rounded-xl text-center  mt-6">
            <p class="text-xl lg:text-3xl font-bold">
                Account Closed
            </p>
        </div>

        @else

        <div class=" bg-white p-4 rounded-xl text-center  mt-6 lg:p-6 lg:shadow-sm">
            <p class="text-3xl lg:text-4xl font-bold">
                <x-naira-symbol /> {{formatNumber($savings->balance)}}
            </p>

            <p class=" font-medium mt-2 lg:text-lg lg:mt-4">
                Interest Gained: <span class=" text-primary">
                    <x-naira-symbol />{{formatNumber($savings->interest_accumulated)}}
                </span>
            </p>

        </div>

        <div class=" grid grid-cols-2 gap-x-3 mt-2  lg:mt-4 lg:gap-6">
            {{-- add money modal --}}
            <div x-data="{showAddMoneyModal: false}">
                <x-primary-button @click="showAddMoneyModal = !showAddMoneyModal" class=" w-full py-3">
                    Add Money
                </x-primary-button>


                <div x-show="showAddMoneyModal" x-transition x-cloak class=" fixed bg-black/70 w-screen h-screen z-50 left-0 top-0 flex justify-center items-center p-4">

                    <div class=" bg-white p-4 lg:p-6 lg:max-w-xl rounded-md w-full">

                        <div class=" flex justify-between items-center">
                            <h3 class=" text-lg lg:text-xl font-semibold">
                                Add money to Savings
                            </h3>
                            <button @click="showAddMoneyModal = !showAddMoneyModal" class=" text-primary font-semibold text-2xl lg:text-3xl">
                                <span class=" fa fa-close"></span>
                            </button>
                        </div>

                        <form wire:submit.prevent='addMoney' class=" mt-6">

                            <div>
                                <x-input-label for="name" :value="__('Amount')" />

                                <p class=" text-sm md:text-base mt-1">Wallet Balance:
                                    <span class=" text-primary font-medium">
                                        <x-naira-symbol /> {{formatNumber(auth()->user()->wallet_balance)}}
                                    </span>
                                </p>

                                <x-text-input id="name" class="block mt-1 w-full placeholder:text-gray-400" type="number" wire:model="amount" required />
                                <x-input-error :messages="$errors->get('amount')" class="mt-2" />

                            </div>

                            <x-enter-pin id="add-money" />

                            <x-primary-button class=" w-full mt-4">
                                Add Money
                            </x-primary-button>
                        </form>

                    </div>

                </div>
            </div>

            {{-- withdrawal modal --}}
            <div x-data="{showWithdrawModal: false}">
                <x-primary-button @click="showWithdrawModal = !showWithdrawModal" class=" w-full py-3">
                    Withdraw
                </x-primary-button>


                <div x-show="showWithdrawModal" x-transition x-cloak class=" fixed bg-black/70 w-screen h-screen z-50 left-0 top-0 flex justify-center items-center p-4">

                    <div class=" bg-white p-4 rounded-md w-full lg:max-w-xl lg:p-6">

                        <div class=" flex justify-between items-center">
                            <h3 class=" text-lg lg:text-xl font-semibold">
                                Withdraw money to wallet
                            </h3>
                            <button @click="showWithdrawModal = !showWithdrawModal" class=" text-primary font-semibold text-2xl">
                                <span class=" fa fa-close"></span>
                            </button>
                        </div>

                        @if ($savings->type === 'locked')
                        <div class=" bg-red-100 text-red-700 font-medium text-xs p-2 md:text-sm lg:text-base border border-red-700 rounded-lg mt-4">
                            <span class=" fa fa-info mr-2"></span> Since this is a locked savings account, withdrawal will attract a <span class=" font-semibold">{{$breaking_locked_savings_fee}}%</span> penalty on your savings balance.
                        </div>
                        @endif

                        <form wire:submit.prevent='withDraw' class=" mt-6 space-y-6">

                            <div>
                                <x-input-label for="source" :value="__('Select Source')" />

                                <select wire:model="source" id="source" class=" p-3 w-full rounded-lg border-gray-300 mt-2" required>
                                    <option value="balance">Wallet balance (
                                        <x-naira-symbol />{{formatNumber($savings->balance)}})
                                    </option>
                                    <option value="interest">Interest Amount (
                                        <x-naira-symbol />{{formatNumber($savings->interest_accumulated)}})
                                    </option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('source')" />
                            </div>


                            <div>
                                <x-input-label for="withdraw_amount" :value="__('Amount')" />

                                <x-text-input id="withdraw_amount" class="block mt-1 w-full placeholder:text-gray-400" type="number" wire:model="withdraw_amount" required autofocus />
                                <x-input-error :messages="$errors->get('withdraw_amount')" class="mt-2" />
                            </div>

                            <x-enter-pin id="withdraw-money" />

                            <x-primary-button class=" w-full">
                                Withdraw Money
                            </x-primary-button>
                        </form>

                    </div>

                </div>
            </div>

        </div>
        @endif



        <div class=" mt-10">
            <div class=" flex items-center justify-between ">
                {{-- edit savings modal --}}
                @if ($savings->status !== 'closed')
                <div x-data="{showEditModal: false}">
                    <button @click="showEditModal = !showEditModal" class=" text-primary font-semibold text-sm pr-8 py-3 cursor-pointer">
                        <span class=" fa fa-edit"></span> Edit
                    </button>


                    <div x-show="showEditModal" x-transition x-cloak class=" fixed bg-black/70 w-screen h-screen z-50 left-0 top-0 flex justify-center items-center p-4">

                        <div class=" bg-white p-4 rounded-md w-full lg:max-w-md lg:p-6">

                            <div class=" flex justify-between items-center">
                                <h3 class=" text-lg md:text-xl font-semibold">
                                    Edit Savings
                                </h3>
                                <button @click="showEditModal = !showEditModal" class=" text-primary font-semibold text-2xl lg:text-3xl">
                                    <span class=" fa fa-close"></span>
                                </button>
                            </div>

                            <form wire:submit.prevent='update' class=" mt-6">

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


                <h1 class=" p-2 font-semibold text-sm border-b-4 border-primary">
                    Activities
                </h1>
            </div>

            <div class=" space-y-4 mt-6">
                @foreach ($activities as $activity)
                <div class=" bg-white p-4 rounded-md lg:shadow-sm">
                    <h4 class=" font-medium mb-1">{{$activity->message}}</h4>

                    <p class=" text-sm text-gray-600">
                        {{$activity->created_at->diffForHumans()}}
                    </p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>