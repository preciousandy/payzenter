<div class=" px-4 py-6 bg-white min-h-screen">

    <h2 class=" text-xl font-semibold text-center">
        Complete Order
    </h2>

    <div class=" flex justify-between items-center mt-4">
        <p class=" text-lg">
            Complete the order within
        </p>

        <p class=" bg-primary p-3 text-xl rounded-lg text-white font-semibold ">
            20:23
        </p>
    </div>

    <ul class=" list-disc pl-4 text-sm space-y-2  mt-3">
        <li>
            Please transfer Asset to the address below or Scan the QR
            code to make payment.
        </li>

        <li>
            After your Transfer, please ensure you fill in the transaction
            info of your payment, to help us confirm your order.
        </li>

        <li>
            Note: This order will be automatically cancelled if the button
            is not clicked by the deadline.
        </li>
    </ul>

    <div class=" mt-6 space-y-2 p-3 border-2 rounded-xl">
        <div class=" flex justify-between items-center">
            <label for="">Price</label>
            <p class=" text-lg font-semibold text-green-600">
                <x-naira-symbol /> {{formatNumber($order->amount_in_naira)}}
            </p>
        </div>
        <div class=" flex justify-between items-center">
            <label for="">Amount in {{$order->crypto_symbol}}</label>
            <p class=" ">
                {{$order->amount_in_crypto}} {{$order->crypto_symbol}}
            </p>
        </div>
        <div class=" flex justify-between items-center">
            <label for="">Amount to pay in USD </label>
            <p class=" ">
                ${{$order->amount_in_usd}}
            </p>
        </div>
        <div class=" flex justify-between items-center">
            <label for="">Network</label>
            <p class=" ">
                (BEP20)
            </p>
        </div>
        <div class=" flex justify-between items-center">
            <label for="">Transaction Fee</label>
            <p class=" ">
                $2.67
            </p>
        </div>
    </div>

    <div class=" p-4 bg-gray-100 rounded-xl mt-6">
        <h3>SwiftPay Balance</h3>
        <p class="  font-semibold  mt-2 text-2xl">
            <x-naira-symbol /> {{formatNumber(auth()->user()->wallet_balance)}}
        </p>
    </div>

    <p class=" text-primary mt-3">
        <span class=" fa fa-info-circle"></span>
        Note: Ensure you input the right info, as we would not be held liable for any loss of asset.
    </p>


    <div class=" mt-4">
        <x-input-label for="amount" :value="__('Amount of BTC to send')" />
        <p class="  p-4 bg-gray-100 rounded-xl flex justify-between items-center mt-1">
            x1fguf67769hhgv678fivgjo088

            <button class=" text-2xl">
                <span class=" fa fa-copy"></span>
            </button>
        </p>
    </div>

    <form action="" class=" mt-4 space-y-4" wire:submit.prevent='completeOrder'>
        <div>
            <h3 class=" text-lg font-semibold mb-2  ">Transaction Details</h3>

            <div>
                <x-input-label :value="__('Wallet Address')" />
                <x-text-input class="block mt-1 w-full placeholder:text-gray-400" type="text" wire:model='wallet_address' required />
                <x-input-error :messages="$errors->get('wallet_address')" class="mt-2" />
            </div>
        </div>

        <div>
            <x-input-label :value="__('Transaction ID')" />
            <x-text-input class="block mt-1 w-full placeholder:text-gray-400" type="text" wire:model='transaction_id' required />
            <x-input-error :messages="$errors->get('transaction_id')" class="mt-2" />
        </div>

        <div>
            <x-input-label :value="__('Screenshot of successful transfer')" />
            <x-text-input class="block mt-1 w-full placeholder:text-gray-400" type="file" wire:model='screenshot' required />
            <x-input-error :messages="$errors->get('screenshot')" class="mt-2" />
            <p class=" text-sm text-primary flex justify-between mt-1">
                <span>
                    Max file size: 10MB
                </span>

                <span>
                    Format: JPG, PNG
                </span>
            </p>
        </div>

        <x-primary-button class="  ">
            {{ __('Sell') }}
        </x-primary-button>
    </form>




</div>