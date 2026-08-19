<div>

    <x-loading-spinner wire:target='transfer, confirmUser, confirm' />

    <div x-data="{showAddUserModal: false}" x-on:close-modal.window="showAddUserModal = false">
        <button class="bg-indigo-50 text-center text-sm md:text-base lg:text-lg mx-auto w-full p-3 font-medium text-primary rounded-lg md:p-4 lg:p-6" @click="showAddUserModal = true">
            <span class=" fa fa-plus-circle text-3xl lg:text-5xl text-primary block mb-1 ">
            </span>
            <span>
                Add account
            </span>

        </button>

        <div x-show="showAddUserModal" x-transition x-cloak class=" fixed bg-black/70 z-30 left-0 top-0 w-full h-full lg:flex lg:items-center lg:justify-center">
            <div class=" bg-gray-50 p-4 lg:p-6 w-full h-full space-y-5  lg:h-auto lg:max-w-xl lg:rounded-xl ">
                <div class=" flex justify-between items-center mb-6">
                    <h2 class=" text-lg font-semibold lg:text-xl">Add Bank Account</h2>
                    <button @click="showAddUserModal = false">
                        <span class="fa fa-close text-2xl lg:text-3xl text-gray-600"></span>
                    </button>
                </div>

                <form class="space-y-6">
                    <div x-data="{ 
                                isOpen: false,
                                selectedBank: @entangle('bank'),
                                selectedBankId: @entangle('bank_id'),
                                searchTerm: '',
                                banks: [
                                    @foreach ($banks as $item)
                                        @if ($item)
                                        { id: '{{$item['code']}}', name: '{{$item['name']}}' },
                                        @endif
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
                            <div @click="togglePopup" class="w-full block p-3 rounded mt-2 cursor-pointer bg-white border border-gray-300" :class="{ 'border-blue-500': isOpen }">
                                <span x-text="selectedBank ? selectedBank.name : 'Select a bank'"></span>
                            </div>

                            <div x-show="isOpen" x-cloak x-transition class=" fixed bg-black/60 z-50 top-0 left-0 h-full w-full flex items-end lg:items-center lg:justify-center">
                                <div class=" w-full mt-1 bg-white border border-gray-300 rounded-t-xl shadow-lg  px-2 py-4 lg:max-w-2xl  lg:rounded-2xl lg:p-6 max-h-[90%]">
                                    <div class=" flex justify-between items-center">
                                        <div></div>
                                        <h3 class=" text-center font-medium text-lg">
                                            Select Bank
                                        </h3>

                                        <button type="button" @click="isOpen = !isOpen">
                                            <span class=" fa fa-times-circle text-red-700 text-2xl"></span>
                                        </button>
                                    </div>
                                    <div class="p-2 mt-4">
                                        <input x-ref="searchInput" x-model="searchTerm" @keydown.escape.window="isOpen = false" type="text" placeholder="Search banks..." class="w-full p-2 border border-gray-300 rounded" />
                                    </div>

                                    <div class=" h-[28rem] overflow-y-auto mt-2 space-y-3">
                                        <template x-for="bank in filteredBanks()" :key="bank.id">
                                            <div @click="selectBank(bank)" class="p-2 hover:bg-gray-100 cursor-pointer flex items-center gap-x-2">
                                                <img :src="`https://ui-avatars.com/api/?name=${bank.name}&background=random&color=fff`" alt="" class="w-10 h-10 rounded-full" />
                                                <p x-text="bank.name"></p>
                                            </div>
                                        </template>
                                        <div x-show="filteredBanks().length === 0" class="p-2 text-gray-500">
                                            No banks found
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>

                        <input type="hidden" name="bank" x-bind:value="selectedBank ? selectedBank.id : ''">

                        <x-input-error class="mt-2" :messages="$errors->get('bank')" />
                    </div>

                    <div>
                        <x-input-label for="account_number" :value="__('Account Number')" />
                        @if ($account_name)
                        <x-text-input id="account_number" wire:model="account_number" type="number" class="mt-1 block w-full" required disabled />
                        @else
                        <x-text-input id="account_number" wire:model="account_number" type="number" class="mt-1 block w-full" required />
                        @endif

                        <x-input-error class="mt-2" :messages="$errors->get('account_number')" />

                        @if ($account_name)
                        <p class=" font-medium text-primary bg-blue-100 text-sm p-2 rounded-md mt-1">
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


                    <x-primary-button type="button" class=" w-60 mx-auto block rounded-xl" wire:click='addAccount'>
                        {{ __('Add Account') }}
                    </x-primary-button>

                    <button type="button" class=" block w-fit mx-auto p-2 text-sm font-medium mt-3 text-center" wire:click='clearDetails'>
                        <span class=" fa fa-close mr-1"></span> Clear Details
                    </button>

                    @else

                    <x-secondary-button type="button" class="py-3 w-full  block   bg-indigo-100" wire:click='confirmUser'>
                        <span class=" fa fa-user mr-2"></span> {{ __('Confirm User') }}
                    </x-secondary-button>
                    @endif


                </form>
            </div>
        </div>


    </div>

    <div class=" mt-6" x-data="{showPreviewForm: @entangle('showPreviewForm'), showSuccessfullModal : @entangle('showSuccessfullModal')}">
        <h3 class="text-lg font-medium lg:text-2xl">
            Bank Accounts
        </h3>


        <div class=" mt-4 space-y-2">
            @forelse ($bankAccounts as $account)

            <div class=" bg-slate-100 py-3 px-2 lg:p-4 rounded-lg flex items-center gap-x-2 lg:gap-x-4">
                <img src="{{asset('https://ui-avatars.com/api/?background=random&name=' . $account['account_name'] . '&rounded=true&bold=true')}}" alt="" class=" h-10 w-10 lg:h-14 lg:w-14 rounded-full">
                <div class=" w-full flex items-center justify-between gap-2">
                    <div>
                        <h3 class=" font-medium text-sm md:text-base lg:text-lg">
                            {{$account['account_name']}}

                        </h3>

                        <p class=" text-xs md:text-sm lg:text-base text-gray-600 lg:mt-1">
                            <span class=" mr-3">{{$account['account_number']}}</span>

                            <span>
                                {{$account['bank_name']}}
                            </span>
                        </p>
                    </div>


                    <div class="flex items-center gap-1 whitespace-nowrap">
                        <p class=" text-lg font-semibold md:text-xl lg:text-2xl">
                            <x-naira-symbol /> {{formatNumber($account['amount'])}}
                        </p>
                        <button class=" p-3" wire:click="removeAccount({{$account['account_number']}})">
                            <span class=" fa fa-times h-6 w-6 lg:h-8 lg:w-8 flex justify-center items-center text-white bg-red-700 rounded-full"></span>
                        </button>
                    </div>
                </div>
            </div>

            @empty

            <div class=" flex justify-center items-center h-40 text-sm text-gray-600 md:text-base lg:text-lg">
                <p>
                    No bank accounts added yet
                </p>
            </div>

            @endforelse

            <x-input-error class="mt-2 " :messages="$errors->get('swiftpayAccounts')" />
        </div>


        <div class=" mt-6">

            <p class=" lg:text-lg">
                Total Amount: <span class=" font-semibold text-lg md:text-xl lg:text-2xl">
                    <x-naira-symbol /> {{formatNumber($totalAmount)}}
                </span>
            </p>

            <p class=" text-sm md:text-base lg:text-lg text-gray-700 lg:mt-2">
                Transfer Fee: <span class=" font-medium text-base md:text-lg lg:text-xl">
                    <x-naira-symbol /> {{formatNumber($feeAmount)}}
            </p>

            <x-primary-button class=" w-48 lg:w-80 mx-auto block rounded-lg mt-6 lg:mt-8" type="submit" wire:click='confirm'>
                {{ __('Confirm') }}
            </x-primary-button>
        </div>

        {{-- order Preview --}}
        <div x-show="showPreviewForm" x-cloak x-transition class=" fixed top-0 left-0 z-30 bg-black/50 w-full h-full  flex items-end lg:justify-center lg:items-center overflow-hidden">

            <div class=" bg-white w-full  rounded-t-2xl p-4 lg:max-w-2xl lg:rounded-2xl lg:p-6 max-h-[90%] overflow-y-auto ">

                <div class=" flex justify-between items-center py-3 border-b-2">
                    <h3 class="  text-lg font-medium md:text-xl lg:font-semibold">
                        Confirm Transfer
                    </h3>

                    <button class=" text-2xl lg:text-3xl" @click="showPreviewForm = false">
                        <span class="fa fa-close text-red-700"></span>
                    </button>
                </div>

                <div class=" text-center space-y-1 lg:space-y-3 mt-2">
                    <h4 class=" lg:text-lg font-medium text-gray-700">
                        Total
                    </h4>

                    <h2 class=" text-3xl lg:text-4xl font-bold">
                        <x-naira-symbol /> {{formatNumber($totalAndFee)}}
                    </h2>
                </div>

                <div class=" mt-4 space-y-2 md:text-lg lg:space-y-3">
                    <div class=" flex justify-between items-center">
                        <label for="">Recipients</label>
                        <p class=" font-medium ">
                            {{count($bankAccounts) > 0 ? collect($bankAccounts)->first()['account_name']. " and " . count($bankAccounts) - 1 . " others" : '' }}
                        </p>
                    </div>
                    <div class=" flex justify-between items-center">
                        <label for="">Total Amount</label>
                        <p class=" ">
                            <x-naira-symbol /> {{formatNumber($totalAmount)}}
                        </p>
                    </div>
                    <div class=" flex justify-between items-center">
                        <label for="">Total Fee</label>
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
                <div class="  bg-gray-50 p-2 rounded-xl  lg:p-3">
                    <x-input-label for="pin" :value="__('SwiftPay Balance')" />
                    <p class=" text-2xl font-semibold text-primary mt-1 lg:text-3xl">
                        <x-naira-symbol /> {{formatNumber(auth()->user()->wallet_balance)}}
                    </p>
                </div>

                <x-enter-pin id="bank-accounts" />

                <x-primary-button type="button" class="   mt-4 mb-10 lg:mt-6 lg:mb-0 rounded-lg" wire:click='transfer'>
                    {{ __('Pay') }}
                </x-primary-button>

            </div>

        </div>

        {{-- transfer successful form --}}
        <div x-show="showSuccessfullModal" x-cloak x-transition class=" fixed top-0 left-0 z-30 bg-black/50 w-full h-full flex items-end overflow-hidden lg:items-center lg:justify-center">

            <div class=" bg-white w-full  rounded-t-2xl p-4 lg:max-w-lg lg:p-8 lg:rounded-2xl">

                <img src="{{asset('transfer-success.png')}}" alt="" class=" mx-auto h-40 lg:h-48">

                <h3 class=" text-lg font-semibold text-center md:text-xl lg:text-2xl">
                    Transfer Successful
                </h3>

                <p class="text-sm text-gray-700 text-center mt-2 lg:mt-3 md:text-base lg:text-lg ">
                    Your multiple transfer of
                    <x-naira-symbol />{{formatNumber($totalAmount)}} NGN was successful
                </p>

                @if ($transaction)
                <a href="{{route('transfer.multiple.receipt', $transaction?->hash_id)}}">
                    <x-primary-button type="button" class=" mt-6 mb-8 rounded-lg lg:mb-4">
                        {{ __('View Receipt') }}
                    </x-primary-button>
                </a>
                @endif

            </div>

        </div>
    </div>
</div>


@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js" integrity="sha512-3gJwYpMe3QewGELv8k/BX9vcqhryRdzRMxVfq6ngyWXwo03GFEzjsUm8Q7RZcHPHksttq7/GFoxjCVUjkjvPdw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js" integrity="sha512-2ImtlRlf2VVmiGZsjm9bEyhjGW4dU7B6TNwh/hx/iSByxNENtj3WVE6o/9Lj4TJeVXPi4bnOIMXFIJJAeufa0A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
    $(document).ready(function() {
            $('#select-banks').select2({
            placeholder: "select banks...",
            theme: "classic"
            });
            $('#select-banks').on('change', function (e) {
            var data = $('#select-banks').select2("val");
            @this.set('bank', data);
            });

          
                          
        });

        
</script>
@endpush