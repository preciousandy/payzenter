<div class=" px-4 py-6 max-w-4xl">

    <x-loading-spinner wire:target='create' />

    <h2 class=" text-lg  lg:text-xl text-center font-semibold">
        Create Ajo Savings
    </h2>

    <form class=" mt-6 lg:mt-8 space-y-6" wire:submit.prevent='create'>
        <div>
            <x-input-label for="phone" :value="__('Ajo Savings Type')" />

            @php
            $ajo_savings = [
            'Daily',
            'Weekly',
            'Monthly',
            'Yearly'
            ];
            @endphp

            <select wire:model="type" id="" class=" p-3 lg:py-4 w-full rounded-lg border-gray-300 mt-2" required>
                <option value="">Select Type</option>
                @foreach ($ajo_savings as $savings_type)
                <option value="{{$savings_type}}">{{$savings_type}} Ajo Savings</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('type')" />
        </div>


        <div>
            <x-input-label for="amount" :value="__('Amount to save')" />
            <p class=" text-sm mt-1 md:text-base">Wallet Balance:
                <span class=" text-primary font-medium">
                    <x-naira-symbol /> {{formatNumber(auth()->user()->wallet_balance)}}
                </span>
            </p>
            <x-text-input id="amount" type="number" class="mt-2 block w-full" wire:model='amount' required />
            <x-input-error class="mt-2 " :messages="$errors->get('amount')" />
        </div>

        <div>
            <x-input-label for="end_date" :value="__('End Date')" />

            <x-text-input id="end_date" type="date" class="mt-2 block w-full" wire:model='end_date' required />
            <x-input-error class="mt-2 " :messages="$errors->get('end_date')" />
        </div>


        <div>
            <x-input-label for="phone" :value="__('Payment Method')" />


            <select wire:model.live="payment_method" id="" class=" p-3 w-full rounded-lg border-gray-300 mt-2" required>
                <option value="">Select Payment Method</option>
                <option value="wallet">SwiftPay Wallet</option>
                {{-- <option value="card">Card</option> --}}
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('payment_method')" />
        </div>

        @if ($payment_method == 'card' && !$card_token)
        <div class=" bg-red-50 border-2 border-red-300 p-3 rounded-lg text-sm text-red-700 font-medium flex justify-between items-center gap-3">
            <p>
                You haven't add your card yet, please add your card to continue
            </p>

            <a href="{{route('add-card-details')}}" class=" inline-block bg-red-700 text-white px-3 py-2 rounded-lg whitespace-nowrap">
                Add Card
            </a>
        </div>
        @endif

        <div>
            <x-input-label for="referral_code" :value="__('Referral Code (optional)')" />

            <x-text-input id="referral_code" type="text" class="mt-2 block w-full" wire:model='referral_code' />

            <p class=" text-blue-600 text-sm md:text-base mt-1">
                Note: using an Ajo Referral code would reduce the percentage fee you'll pay for your Ajo Savings
            </p>
            <x-input-error class="mt-2 " :messages="$errors->get('referral_code')" />
        </div>


        {{-- terms and conditions --}}
        <div>
            <label for="terms" class="inline-flex items-center">
                <input id="terms" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" required>
                <span class="ms-2 text-sm md:text-base text-gray-600 dark:text-gray-400">{{ __('Accept Terms & Conditions') }}</span>
            </label>
        </div>




        <x-primary-button class=" w-full lg:w-80 mx-auto">
            Create Savings
        </x-primary-button>


    </form>

</div>