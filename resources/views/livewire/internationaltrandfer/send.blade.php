<div class=" bg-primary min-h-screen flex flex-col">
    <div class=" h-60 flex justify-center items-center">
        <div>
            <h1 class=" text-white uppercase text-xl font-bold tracking-wider">
                International Transfer
            </h1>
            <div class=" flex items-center mt-4">
                <h1 class=" text-center text-4xl font-extrabold text-white">
                    <x-naira-symbol /> {{ formatNumber(auth()->user()->wallet_balance) }}
                </h1>

                <button class="  bg-white p-3 rounded-md text-xs ml-2">
                    <span class=" fa fa-plus"></span>

                    Add Money
                </button>
            </div>
        </div>
    </div>

    <div class=" bg-white flex-1 rounded-t-[3rem] px-4 py-6 ">

        <div class=" mt-10 ">
            <form class="space-y-4" wire:submit.prevent='sendMoney'>
                <h2 class=" text-lg font-semibold">Account Details </h2>
                <p>Fill in your transaction details</p>

                <div>
                    <x-input-label for="phone" :value="__('Select Bank')" />

                    <select wire:model.live="bank" id="" class=" p-3 w-full rounded-lg border-gray-300 mt-2" required>
                        <option value="">Select Bank</option>
                        <option value="Access Bank">Access Bank</option>
                        <option value="First Bank">First Bank</option>
                        <option value="GT Bank">GT Bank</option>
                        <option value="Zenith Bank">Zenith Bank</option>
                        <option value="UBA">UBA</option>

                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('bank_name')" />
                </div>

                <div>
                    <x-input-label for="username" :value="__('Amount')" />
                    <x-text-input id="username" type="number" class="mt-1 block w-full" wire:model='amount' required />
                    <x-input-error class="mt-2 " :messages="$errors->get('amount')" />
                </div>

                <div>
                    <x-input-label for="acct_number" :value="__('Receivers Account Number')" />
                    <x-text-input id="acct_number" type="number" class="mt-1 block w-full disabled:bg-gray-200" wire:model='account_number' />
                    <x-input-error class="mt-2 " :messages="$errors->get('account_number')" />
                </div>

                <div>
                    <x-input-label for="pin" :value="__('Transaction PIN')" />
                    <x-text-input id="pin" type="number" class="mt-1 block w-full" wire:model='pin' required />
                    <x-input-error class="mt-2 " :messages="$errors->get('pin')" />
                </div>


                <x-primary-button class=" w-48 mx-auto block">
                    {{ __('Send Money') }}
                </x-primary-button>


            </form>
        </div>

    </div>
</div>