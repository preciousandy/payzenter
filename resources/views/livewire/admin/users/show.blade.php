<div class=" px-4 py-6">

    <div class=" flex justify-between items-center">
        <h2 class=" text-xl font-semibold">
            User Profile
        </h2>

        <div class=" flex  items-center gap-x-2">
            <livewire:edit-user-info :user="$user" />
            <livewire:change-user-account-status :user="$user" />
            <livewire:change-user-level :user="$user" />
            <livewire:send-message-to-single-user :user="$user" />
            <livewire:change-user-role :user="$user" />
        </div>
    </div>

    <div class=" mt-6 grid grid-cols-6 gap-x-4">
        <section class=" bg-white shadow p-12 rounded-xl flex gap-x-8 col-span-4">
            <img src="{{asset($user->profile_photo)}}" alt="" class=" h-40 w-40 object-cover rounded-full">

            <div>
                <h2 class=" text-3xl font-black">
                    {{$user->name}}
                </h2>


                <div class=" text-base mt-6 space-y-4">
                    <div class=" grid grid-cols-2 gap-x-8">
                        <p>
                            <span class=" fa fa-user mr-1"></span> <span class=" font-medium ">{{ config('app.name') }} Tag:</span>
                        </p>
                        <p class="">
                            {{'@'}}{{$user->username}}
                        </p>
                    </div>
                    {{-- other name --}}
                    <div class=" grid grid-cols-2 gap-x-8">
                        <p>
                            <span class=" fa fa-user-tie mr-1"></span> <span class=" font-medium mr-6">Other Name:</span>
                        </p>
                        <p class="">
                            {{$user->kyc->other_names ?? 'N/A'}}
                        </p>
                    </div>
                    <div class=" grid grid-cols-2 gap-x-8">
                        <p>
                            <span class=" fa fa-envelope mr-1"></span> <span class=" font-medium mr-6">Email:</span>
                        </p>
                        <p class="">
                            {{$user->email}}
                        </p>
                    </div>
                    <div class=" grid grid-cols-2 gap-x-8">
                        <p>
                            <span class=" fa fa-phone mr-1"></span> <span class=" font-medium mr-6">Phone:</span>
                        </p>
                        <p class="">
                            {{$user->phone}}
                        </p>
                    </div>
                    <div class=" grid grid-cols-2 gap-x-8">
                        <p>
                            <span class=" fa fa-award mr-1"></span> <span class=" font-medium mr-6">Level:</span>
                        </p>
                        <div class=" flex items-center gap-2 mt-1">
                            <img src="{{asset('badges/' . $user->level .'.png')}}" alt="" class=" h-7">
                            <h4>
                                {{ucfirst($user->level)}} Badge
                            </h4>
                        </div>
                    </div>
                    <div class=" grid grid-cols-2 gap-x-8">
                        <p>
                            <span class=" fa fa-passport mr-1"></span> <span class=" font-medium mr-6">Account Status</span>
                        </p>
                        <div class="space-y-1">
                            <p class="{{$user->account_status}} w-fit">
                                {{$user->account_status}}
                            </p>
                            @if($user->account_status === 'suspended' && $user->suspended_until)
                            <p class="text-sm text-gray-600">
                                <span class="fa fa-clock mr-1"></span>
                                Until: {{$user->suspended_until->format('M j, Y \a\t g:i A')}}
                            </p>
                            @if($user->suspension_reason)
                            <p class="text-sm text-gray-600">
                                <span class="fa fa-info-circle mr-1"></span>
                                Reason: {{$user->suspension_reason}}
                            </p>
                            @endif
                            @endif
                        </div>
                    </div>
                    <div class=" grid grid-cols-2 gap-x-8">
                        <p>
                            <span class=" fa fa-key mr-1"></span> <span class=" font-medium mr-6">Roles:</span>
                        </p>
                        <p class="">
                            {{implode(", ", $user->roles->pluck('name')->toArray())}}
                        </p>
                    </div>
                    <div class=" grid grid-cols-2 gap-x-8">
                        <p>
                            <span class=" fa fa-calendar mr-1"></span> <span class=" font-medium mr-6">Joined:</span>
                        </p>
                        <p class="">
                            {{$user->created_at->diffForHumans()}}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-white shadow p-6 rounded-xl col-span-2" x-data="{showAddMoney: false}">

            <div class=" flex justify-between items-center">
                <div class=" ">
                    <h4 class=" font-medium text-lg"> <span class=" fa fa-wallet mr-2"></span> Wallet Balance</h4>

                    <h2 class=" font-black text-3xl mt-2">
                        <x-naira-symbol /> {{formatNumber($user->wallet_balance)}}
                    </h2>
                </div>

                <button class=" p-3" @click="showAddMoney = !showAddMoney">
                    <span class=" fa fa-edit text-3xl"></span>
                </button>
            </div>

            <form x-show="showAddMoney" x-cloak x-transition class=" mt-4 w-full " wire:submit.prevent='updateBalance'>
                <div class="  w-full">
                    <div class=" mb-2">
                        <x-input-label for="amount" :value="__('Edit User Balance')" />
                        <x-text-input id="amount" class="block mt-1 w-full py-3" type="number" wire:model="userBalance" required placeholder="wallet balance..." />
                        <x-input-error :messages="$errors->get('userBalance')" class="mt-1" />
                    </div>

                    <x-primary-button class="    rounded-lg">
                        <span class=" fa fa-plus-circle mr-2"></span> Update Balance
                    </x-primary-button>
                </div>

            </form>

        </section>
    </div>


    <div x-data="menu" class=" mt-8">
        <div class="   mb-6 ">
            <template x-for="(tab, tabIndex) in tabMenu" :key="tabIndex">
                <button class=" py-3 px-10 bg-indigo-100 text-primary rounded-t-xl whitespace-nowrap" :class="currentTab == tab && '  font-semibold  bg-primary text-white text-lg'" @click="currentTab = tab">
                    <span x-text="tab"></span>
                </button>

            </template>
        </div>

        <div x-show="currentTab == 'Transactions'">
            @livewire('admin.user-transaction-table', ['userId' => $user->id])
        </div>

        <div x-show="currentTab == 'Referrals'">
            @livewire('admin.user-referrals-table', ['userId' => $user->id])
        </div>

    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('menu', () => ({
            currentTab: 'Transactions',
            tabMenu: [
                'Transactions',
                'Referrals',
            ],
            changeTab(tab) {
                this.currentTab = tab
            }
        }))
    })
</script>
@endpush
