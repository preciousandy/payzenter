<div class=" bg-white">
    <div class=" px-4 py-6 bg-white min-h-screen max-w-4xl">
        <h2 class=" text-xl font-semibold text-center md:text-2xl">Add Money</h2>

        <div class=" bg-gray-100 rounded-xl p-6 mt-6 lg:p-8">
            <a href="{{route('bank-transfer')}}" class=" flex items-center gap-x-4 lg:gap-x-6">
                <div class=" flex items-center gap-3">
                    <div class=" w-14 h-14 lg:h-16 lg:w-16 rounded-full flex justify-center items-center bg-blue-200 text-primary text-2xl">
                        <span class=" fa fa-bank"></span>
                    </div>

                    <div class=" flex-1">
                        <p class=" text-lg font-semibold lg:text-xl">Bank Transfer</p>
                        <p class=" text-sm text-gray-600 md:text-base">
                            Send money to your wallet from your bank account
                        </p>
                    </div>
                </div>

                <span class=" fa fa-chevron-right md:text-lg lg:text-xl"></span>
            </a>

            <hr class=" my-4 lg:my-6 lg:border lg:border-gray-300">

            <div>
                <p class=" text-sm text-gray-600 md:text-base lg:text-lg">
                    PayZenter Virtual Account Number
                </p>
                <p class=" text-xl font-semibold mt-1 md:text-2xl">{{auth()->user()->virtualAccount?->account_number ?? 'NIL'}}</p>
            </div>
            <div class=" mt-4">
                <p class=" text-sm text-gray-600 md:text-base lg:text-lg">
                    Bank Name
                </p>
                <p class=" text-xl font-semibold mt-1 md:text-2xl">{{auth()->user()->virtualAccount?->bank_name ?? "NIL"}}</p>
            </div>
            <div class=" mt-4">
                <p class=" text-sm text-gray-600 md:text-base lg:text-lg">
                    Account Name
                </p>
                <p class=" text-xl font-semibold mt-1 md:text-2xl">{{auth()->user()->virtualAccount?->account_name ?? "NIL"}}</p>
            </div>

            @if (auth()->user()->virtual_account_number)
            <x-secondary-button class=" w-full mt-6 lg:mt-8 py-3 lg:py-4 lg:text-base rounded-xl" onclick="copyToClipboard('{{auth()->user()->virtualAccount?->account_number }}')">
                <span class=" fa fa-copy  mr-2"></span> <span id="copy">Copy Number</span>
            </x-secondary-button>
            @endif
        </div>

        <div class=" mt-6 space-y-6">
            <!-- Custom Paystack Card Payment Component -->
            <div>
                <livewire:fund-wallet.paystack-card-pay />
            </div>

            <h3 class=" font-medium mt-4 lg:mt-8 md:text-lg lg:text-xl">

                    International Deposit Method
                </h3>

                <div class=" mt-2 lg:mt-4 space-y-4 lg:space-y-0 lg:divide-y">
                    @foreach ($internationalCurrencies as $currency)
                    <a href="{{route('bureau-da-change.sell-crypto.sell', $currency->uuid)}}" class=" flex items-center justify-between lg:py-6 ">
                        <div class="flex items-center gap-x-3">
                            <img src="{{$currency->icon}}" alt="" class=" h-10 w-10 lg:h-14 lg:w-14 rounded-full object-cover">

                            <div>
                                <h2 class=" font-medium md:text-lg lg:text-xl">
                                    {{$currency->name}}
                                </h2>
                                <p class=" text-sm mt-1 text-gray-600 md:text-base">
                                    {{$currency->symbol}}
                                </p>
                            </div>
                        </div>

                        <span class=" fa fa-chevron-right"></span>
                    </a>

                    @endforeach
                </div>


            </div>
        </div>

    </div>
</div>

<x-slot:headerTitle>
    Fund Wallet
</x-slot:headerTitle>