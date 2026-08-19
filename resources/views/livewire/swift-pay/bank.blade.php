<div class="bg-white">
    <div class=" px-4 py-6  min-h-screen max-w-5xl">

        <x-loading-spinner wire:target='transfer, confirmUser,submit,test' />

        <div x-data="{showPreviewForm: @entangle('showPreviewForm')}">
            <form class="space-y-6">

                <div class=" flex items-center justify-between gap-4 ">
                    <h2 class=" text-lg lg:text-2xl font-semibold" wire:click='test'>Withdraw to Settlement Account</h2>
                    <a href="{{route('transaction-history', ['source' => 'bank_transfer'])}}" class=" inline-block text-sm text-green-600 md:text-base lg:text-lg font-medium">
                        History
                    </a>
                </div>

                @if ($settlement_only)
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                        <div class="text-sm text-gray-500">Settlement Account</div>
                        <div class="mt-2 space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Bank</span>
                                <span class="font-semibold text-gray-900">{{ $bank_name }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Account Number</span>
                                <span class="font-semibold text-gray-900">{{ $bank_account_number }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Account Name</span>
                                <span class="font-semibold text-gray-900">{{ $account_name }}</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div x-data="{ 
                        isOpen: false,
                        selectedBank: @entangle('bank'),
                        selectedBankId: @entangle('bank_id'),
                        searchTerm: '',
                        banks: [
                            @foreach ($banks as $item)
                                { id: '{{$item['code'] ?? ''}}', name: '{{$item['name'] ?? ''}}' },
                            @endforeach
                        ],
                        filteredBanks() {
                            return this.searchTerm === ''
                                ? this.banks
                                : this.banks.filter(bank => 
                                    bank.name.toLowerCase().includes(this.searchTerm.toLowerCase())
                                  );
                        },
                        togglePopup(event) {
                            event.preventDefault();
                            this.isOpen = !this.isOpen;
                            if (this.isOpen) {
                                this.$nextTick(() => this.$refs.searchInput.focus());
                            }
                        },
                        selectBank(bank) {
                            this.selectedBank = bank;
                            this.selectedBankId = bank.id;
                            this.isOpen = false;
                            this.searchTerm = '';
                        }
                    }">
                        <x-input-label :value="__('Select Bank')" />

                        <div class="relative w-full">
                            <div @click="togglePopup" class="w-full block p-3 rounded mt-2 cursor-pointer bg-white border border-gray-300 lg:py-4" :class="{ 'border-blue-500': isOpen }">
                                <span x-text="selectedBank ? selectedBank.name : 'Select a bank'"></span>
                            </div>

                            <div x-show="isOpen" x-cloak x-transition class=" fixed bg-black/60 z-50 top-0 left-0 h-full w-full flex items-end lg:items-center lg:justify-center">
                                <div class=" w-full mt-1 bg-white border border-gray-300 rounded-t-xl shadow-lg  px-2 py-4 lg:max-w-2xl  lg:rounded-2xl lg:p-6 max-h-[90%]">
                                    <div class=" flex justify-between items-center">
                                        <div></div>
                                        <h3 class=" text-center font-medium text-lg lg:text-xl">
                                            Select Bank
                                        </h3>

                                        <button type="button" @click="isOpen = !isOpen">
                                            <span class=" fa fa-times-circle text-red-700 text-2xl lg:text-3xl"></span>
                                        </button>
                                    </div>
                                    <div class="p-2 mt-4">
                                        <input x-ref="searchInput" x-model="searchTerm" @keydown.escape.window="isOpen = false" type="text" placeholder="Search banks..." class="w-full p-2 border border-gray-300 rounded lg:p-3" />
                                    </div>

                                    <div class=" h-[28rem] overflow-y-auto mt-2 space-y-3 lg:mt-4">
                                        <template x-for="(bank, index) in filteredBanks()" :key="index">
                                            <div @click="selectBank(bank)" class="p-2 hover:bg-gray-100 cursor-pointer flex items-center gap-x-2">
                                                <img :src="`https://ui-avatars.com/api/?name=${bank.name}&background=random&color=fff`" alt="" class="w-10 h-10 lg:h-12 lg:w-12 rounded-full" />
                                                <p x-text="bank.name" class=" lg:text-lg"></p>
                                            </div>
                                        </template>
                                        <div x-show="filteredBanks().length === 0" class="p-3 text-gray-600 text-center">
                                            No banks found
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>

                        <input type="hidden" name="bank" x-bind:value="selectedBank ? selectedBank.id : ''">

                        <x-input-error class="mt-2" :messages="$errors->get('bank')" />
                    </div>
                @endif

                <div>
                    <x-input-label for="bank_account_number" :value="__('Account Number')" />
                    @if ($account_name)
                    <x-text-input id="bank_account_number" wire:model="bank_account_number" type="number" class="mt-1 block w-full" required disabled />
                    @else
                    <x-text-input id="bank_account_number" wire:model="bank_account_number" type="number" class="mt-1 block w-full" required />
                    @endif

                    <x-input-error class="mt-2" :messages="$errors->get('bank_account_number')" />

                    @if ($account_name)
                    <p class=" font-medium text-primary bg-blue-100 text-sm p-2 rounded-md md:text-base md:p-3 mt-1 md:mt-2">
                        <span class="fa fa-check-circle mr-1"></span> {{$account_name}}
                    </p>
                    @endif

                </div>

                @if ($account_name)
                <div>
                    <x-input-label for="amount" :value="__('Amount')" />
                    <x-text-input id="amount" type="number" class="mt-1 block w-full" wire:model='amount' required />

                    <x-input-error class="mt-2 " :messages="$errors->get('amount')" />

                </div>

                <div class=" mt-6">
                    <x-input-label for="desc" :value="__('Description')" />
                    <x-text-input id="desc" type="text" class="mt-1 block w-full" wire:model='description' required />
                    <x-input-error class="mt-2 " :messages="$errors->get('description')" />
                </div>



                <x-primary-button type="button" class=" w-60 lg:w-80 mx-auto block rounded-xl" wire:click='submit'>
                    {{ __('Confirm Transfer') }}
                </x-primary-button>

                @else

                <x-secondary-button type="button" class="py-3 w-full  block  bg-indigo-100 lg:w-80 lg:mx-auto" wire:click='confirmUser'>
                    <span class=" fa fa-user mr-2"></span> {{ __('Confirm User') }}
                </x-secondary-button>
                @endif


            </form>

            {{-- order Preview --}}
            <div x-show="showPreviewForm" x-cloak x-transition class=" fixed top-0 left-0 z-30 bg-black/50 w-full h-full flex items-end lg:justify-center lg:items-center overflow-hidden">

                <div class=" bg-white w-full  rounded-t-2xl p-4 lg:max-w-2xl lg:rounded-2xl lg:p-6 max-h-[90%] overflow-y-auto ">

                    <div class=" flex justify-between items-center pb-3 border-b-2">
                        <h3 class=" text-lg font-medium md:text-xl lg:font-semibold">
                            Confirm Transfer
                        </h3>

                        <button class=" text-2xl lg:text-3xl" @click="showPreviewForm = false">
                            <span class="fa fa-close text-red-700"></span>
                        </button>
                    </div>

                    <div class=" text-center mt-2">
                        <p class=" text-3xl lg:text-4xl font-bold ">
                            <x-naira-symbol /> {{formatNumber($totalAmount)}}
                        </p>
                    </div>


                    <div class="mt-4 space-y-2 md:text-lg lg:space-y-3">
                        <div class=" flex justify-between items-center">
                            <label for="">Bank</label>
                            <p class=" font-semibold text-green-600">
                                {{$bank_name}}
                            </p>
                        </div>
                        <div class=" flex justify-between items-center">
                            <label for="">Recipient Name</label>
                            <p class=" ">
                                {{$account_name}}
                            </p>
                        </div>
                        <div class=" flex justify-between items-center">
                            <label for="">Amount</label>
                            <p class=" ">
                                <x-naira-symbol /> {{formatNumber($amount)}}
                            </p>
                        </div>
                        <div class=" flex justify-between items-center">
                            <label for="">Transfer Fee</label>
                            <p class=" ">
                                <x-naira-symbol /> {{formatNumber($feeAmount)}}
                            </p>
                        </div>
                        <div class=" flex justify-between items-center">
                            <label for="">Remarks</label>
                            <p class=" ">
                                {{$description}}
                            </p>
                        </div>

                    </div>

                    <h4 class=" font-medium mt-4 mb-2 md:text-lg">Payment Method</h4>
                    <div class="  bg-gray-50 p-2 rounded-xl lg:p-3 ">
                        <x-input-label for="pin" :value="__('Wallet Balance')" />
                        <p class=" text-2xl font-semibold text-primary mt-1 lg:text-3xl">
                            <x-naira-symbol /> {{formatNumber(auth()->user()->wallet_balance)}}
                        </p>
                    </div>

                    <x-enter-pin />

                    <x-primary-button type="button" class=" my-4 rounded-lg lg:mt-6" wire:click='transfer'>
                        {{ __('Pay') }}
                    </x-primary-button>

                </div>

            </div>
        </div>

        {{-- recent transfers --}}
        <div x-data="{
            currentTab: 'Recent',
                tabMenu: [
                    'Recent',
                    'Favourites'
                ],
                changeTab(tab) {
                    this.currentTab = tab
            }
        }" class=" bg-gray-50 rounded-xl py-2 mt-6 lg:mt-8">
            <div class="flex items-center gap-x-4 mb-6">
                <template x-for="(tab, tabIndex) in tabMenu" :key="tabIndex">
                    <button class=" px-4 py-2 text-sm lg:text-base lg:py-3 " :class="currentTab == tab && ' text-primary font-semibold border-b-4 border-primary'" @click="currentTab = tab">
                        <span x-text="tab"></span>
                    </button>
                </template>
            </div>

            <div class=" px-3" x-show="currentTab === 'Recent'" x-cloak x-transition>
                @if (count($recentTransfers) > 0)
                <div class=" space-y-4 lg:space-y-6">
                    @foreach ($recentTransfers as $transferItem)
                    <a href="{{route('transfer.bank', ['transfer' => $transferItem->hash_id])}}" class=" flex items-center gap-4">
                        <img src="{{asset(randomImage($transferItem->account_name))}}" alt="" class=" h-10 lg:h-12">

                        <div class="">
                            <p class=" font-semibold lg:text-lg">{{$transferItem->account_name}}</p>
                            <p class=" text-sm text-gray-600 mt-1 lg:text-base">{{$transferItem->account_number}} | {{$transferItem->bank_name}}</p>
                        </div>
                    </a>
                    @endforeach
                </div>
                <a href="{{route('transfer.bank.beneficiaries')}}" class=" block p-3 text-center text-sm  lg:text-base">
                    <p class=" text-center text-primary  font-medium">
                        View all
                        <span class=" fa fa-chevron-right ml-2"></span>
                    </p>
                </a>

                @else
                <p class=" text-center text-gray-500 py-8 px-4">No recent transfers</p>
                @endif

            </div>
        </div>
    </div>
</div>
