<div class=" bg-primary min-h-screen flex flex-col">
    <div class=" h-60 flex justify-center items-center">
        <div>
            <h1 class=" text-white uppercase text-xl font-bold tracking-wider">
                International Transfer
            </h1>

            <h1 class=" text-center text-4xl font-extrabold text-white mt-4">
                <x-naira-symbol /> {{ formatNumber(auth()->user()->wallet_balance) }}
            </h1>


        </div>
    </div>

    <div class=" bg-white flex-1 rounded-t-[3rem] px-4 py-6 ">

        <div class=" mt-10 ">
            <form class="space-y-4" wire:submit.prevent='sendMoney'>
                <h2 class=" text-lg font-semibold">Account Details </h2>
                <p>Fill in your transaction details</p>

                <div>
                    <x-input-label for="phone" :value="__('Select Bank')" />

                    <select wire:model="bank" id="" class=" p-3 w-full rounded-lg border-gray-300 mt-2" required>
                        <option value="">Select Bank</option>
                        <option value="Access Bank">Access Bank</option>
                        <option value="First Bank">First Bank</option>
                        <option value="GT Bank">GT Bank</option>
                        <option value="Zenith Bank">Zenith Bank</option>
                        <option value="UBA">UBA</option>

                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('bank')" />
                </div>


                <div>
                    <x-input-label for="phone" :value="__('Select Currency')" />

                    <select wire:model.live="currency" id="" class=" p-3 w-full rounded-lg border-gray-300 mt-2" required>
                        <option value="">Select Currency</option>
                        @foreach ($exchangeRates as $rate)
                        <option value="{{$rate->currency}}">{{$rate->currency}}</option>

                        @endforeach

                    </select>

                    @if ($currency)
                    @php
                    $exchangeRate = $exchangeRates->where('currency', $currency)->first();
                    @endphp
                    <p class=" text-sm font-semibold text-primary mt-1">
                        1 {{$currency}} =
                        <x-naira-symbol /> {{ formatNumber($exchangeRate->rate) }}
                    </p>

                    @endif

                    <x-input-error class="mt-2" :messages="$errors->get('currency')" />
                </div>

                <div>
                    @if ($currency)
                    <label for="">Amount in {{$currency}}</label>
                    @else
                    <x-input-label for="name" :value="__('Amount')" />
                    @endif

                    <x-text-input id="name" type="text" class="mt-1 block w-full disabled:bg-gray-200" wire:model='amount' />
                    <x-input-error class="mt-2 " :messages="$errors->get('amount')" />
                </div>

                <div>
                    <x-input-label for="name" :value="__('Receivers Account Number')" />
                    <x-text-input id="name" type="text" class="mt-1 block w-full disabled:bg-gray-200" wire:model='account_number' />
                    <x-input-error class="mt-2 " :messages="$errors->get('account_number')" />
                </div>


                <div>
                    <x-input-label for="name" :value="__('Receivers Name')" />
                    <x-text-input id="name" type="text" class="mt-1 block w-full disabled:bg-gray-200" wire:model='name' />
                    <x-input-error class="mt-2 " :messages="$errors->get('name')" />
                </div>


                <div>
                    <x-input-label for="email" :value="__('Receivers Email')" />
                    <x-text-input id="email" type="email" class="mt-1 block w-full" wire:model='email' required />
                    <x-input-error class="mt-2 " :messages="$errors->get('email')" />
                </div>



                <x-primary-button class=" w-48 mx-auto block">
                    {{ __('Send Money') }}
                </x-primary-button>


            </form>
        </div>

    </div>
</div>