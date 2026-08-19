<div class=" px-4 py-6 max-w-5xl">

    <h2 class=" text-lg md:text-xl lg:text-2xl font-semibold">
        Create a new Interest Savings
    </h2>

    <x-loading-spinner wire:target='create, confirmSavings' />

    {{-- locked savings notice --}}
    @if (!$if_has_locked_savings)

    <x-locked-savings-notice />
    @endif

    <div x-data="{showPreviewForm: @entangle('showPreviewForm')}">
        <form class=" mt-6 space-y-4 lg:space-y-6" wire:submit.prevent='confirmSavings'>
            <div>
                <x-input-label for="name" :value="__('Name of Savings')" />
                <x-text-input id="name" type="text" class="mt-2 block w-full" wire:model='name' required />
                <x-input-error class="mt-2 " :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="name" :value="__('What type of Savings?')" />
                <select wire:model.live="type" id="" class=" p-3 w-full rounded-lg border-gray-300 mt-2" required>
                    <option value="locked">Locked</option>
                    <option value="unlocked">Unlocked</option>

                </select>

                <p class=" text-sm mt-1">
                    You will earn interest of {{$interest}}% daily
                </p>

                <x-input-error class="mt-2 " :messages="$errors->get('type')" />
            </div>


            <div>
                <x-input-label for="amount" :value="__('Amount to save')" />
                <p class=" text-sm mt-1">Wallet Balance:
                    <span class=" text-primary font-medium">
                        <x-naira-symbol /> {{formatNumber(auth()->user()->wallet_balance)}}
                    </span>
                </p>
                <x-text-input id="amount" type="number" class="mt-2 block w-full" wire:model.live='amount' required />
                <x-input-error class="mt-2 " :messages="$errors->get('amount')" />

                <div class=" mt-1">
                    <p class=" text-sm font-medium">
                        If you save
                        <x-naira-symbol /> {{$amount}}, your estimated returns will be:
                    </p>

                    <div class=" mt-4 grid grid-cols-3  divide-x-2">
                        <div>
                            <h4 class=" text-sm font-medium text-gray-700 text-center">
                                Daily
                            </h4>

                            <h2 class=" text-lg font-bold mt-2 text-center text-primary">
                                <x-naira-symbol /> {{formatNumber($daily_earnings)}}
                            </h2>
                        </div>
                        <div>
                            <h4 class=" text-sm font-medium text-gray-700 text-center">
                                Weekly
                            </h4>

                            <h2 class=" text-lg font-bold mt-2 text-center text-primary">
                                <x-naira-symbol /> {{formatNumber($weekly_earnings)}}
                            </h2>
                        </div>
                        <div>
                            <h4 class=" text-sm font-medium text-gray-700 text-center">
                                Monthly
                            </h4>

                            <h2 class=" text-lg font-bold mt-2 text-center text-primary">
                                <x-naira-symbol /> {{formatNumber($monthly_earnings)}}
                            </h2>
                        </div>
                    </div>
                </div>
            </div>



            @if ($type === 'locked')
            <div>
                <x-input-label for="end_data" :value="__('End date')" />
                <x-text-input id="end_data" type="date" class="mt-1 block w-full" wire:model='end_date' required />
                <x-input-error class="mt-2 " :messages="$errors->get('end_date')" />
            </div>

            @endif

            <div>
                <label for="auto_saave" class="inline-flex items-center ">
                    <input id="auto_saave" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" wire:model.live="if_auto_save">
                    <span class="ms-2  text-gray-600 dark:text-gray-400 text-base">{{ __('Turn on Auto-save') }}</span>
                </label>
            </div>

            @if ($if_auto_save)
            <div class=" space-y-4">
                <div>
                    <x-input-label for="phone" :value="__('Select frequency')" />

                    <select wire:model="frequency" id="" class=" p-3 w-full rounded-lg border-gray-300 mt-2" required>
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('frequency')" />
                </div>

                <div>
                    <x-input-label for="auto_save_amount" :value="__('Amount to save')" />
                    <p class=" text-xs mt-1">This amount will be deducted from your wallet balance based on the frequency you set
                    </p>
                    <x-text-input id="auto_save_amount" type="number" class="mt-2 block w-full" wire:model='auto_save_amount' required />
                    <x-input-error class="mt-2 " :messages="$errors->get('auto_save_amount')" />
                </div>
            </div>

            @endif

            <div>
                <label for="terms" class="inline-flex items-center">
                    <input id="terms" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" required>
                    <span class="ms-2 text-sm md:text-base  dark:text-gray-400 text-primary">{{ __('Accept Terms & Conditions') }}</span>
                </label>
            </div>

            <x-primary-button class=" w-full">
                Continue
            </x-primary-button>


        </form>


        {{-- order Preview --}}
        <div x-show="showPreviewForm" x-cloak x-transition class=" fixed top-0 left-0 z-30 bg-black/50 w-full h-full flex items-end lg:justify-center lg:items-center overflow-hidden">

            <div class=" bg-white w-full  rounded-t-2xl p-4 lg:max-w-2xl lg:rounded-2xl lg:p-6 max-h-[90%] overflow-y-auto ">

                <div class=" flex justify-between items-center py-3 border-b-2">
                    <h3 class="  text-lg font-medium md:text-xl lg:font-semibold">
                        Confirm Savings
                    </h3>

                    <button class=" text-2xl lg:text-3xl" @click="showPreviewForm = false">
                        <span class="fa fa-close text-red-700"></span>
                    </button>
                </div>

                <div class="text-center space-y-1 lg:space-y-3 mt-2">
                    <label for="">Total Amount</label> <br>
                    <p class=" text-3xl lg:text-4xl font-bold ">
                        <x-naira-symbol /> {{formatNumber($total_amount)}}
                    </p>
                </div>


                <div class=" mt-4 space-y-2 md:text-lg lg:space-y-3">
                    <div class=" flex justify-between items-center">
                        <label for="">Name</label>
                        <p class=" font-semibold text-green-600">
                            {{$name}}
                        </p>
                    </div>
                    <div class=" flex justify-between items-center">
                        <label for="">Amount</label>
                        <p class=" ">
                            <x-naira-symbol /> {{formatNumber($amount)}}
                        </p>
                    </div>
                    <div class=" flex justify-between items-center">
                        <label for="">Fee</label>
                        <p class=" ">
                            <x-naira-symbol /> {{$fee}}
                        </p>
                    </div>
                    <div class=" flex justify-between items-center">
                        <label for="">Type</label>
                        <p class=" ">
                            {{$type}} savings
                        </p>
                    </div>
                    <div class=" flex justify-between items-center">
                        <label for="">Daily Earnings</label>
                        <p class=" ">
                            <x-naira-symbol /> {{formatNumber($daily_earnings)}}
                        </p>
                    </div>
                    <div class=" flex justify-between items-center">
                        <label for="">Weekly earnings</label>
                        <p class=" ">
                            <x-naira-symbol /> {{formatNumber($weekly_earnings)}}
                        </p>
                    </div>
                    <div class=" flex justify-between items-center">
                        <label for="">Monthly Earnings</label>
                        <p class=" ">
                            <x-naira-symbol /> {{formatNumber($monthly_earnings)}}
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

                <x-enter-pin />

                <x-primary-button type="button" class="mt-4 mb-4 lg:mt-6 lg:mb-0 rounded-lg" wire:click='create'>
                    {{ __('Create Savings') }}
                </x-primary-button>

            </div>

        </div>
    </div>

</div>