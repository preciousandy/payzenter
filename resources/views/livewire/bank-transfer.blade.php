<div class=" bg-white">
    <div class=" bg-white min-h-screen px-4 py-6 max-w-4xl">

        <div class=" bg-gray-50 p-4 lg:p-6 border border-gray-200 rounded-xl space-y-5 lg:space-y-8">
            <div class=" flex items-center gap-x-2 lg:gap-x-4">
                <img src="{{asset('icons/num.png')}}" alt="" class=" h-12 lg:h-16">

                <div>
                    <p class=" text-sm text-gray-700 md:text-base lg:text-lg">
                        {{config('app.name')}} Account Number
                    </p>

                    <h2 class=" text-xl font-semibold mt-1 md:text-2xl  lg:mt-1">
                        {{auth()->user()->virtualAccount?->account_number ?? 'NIL'}}
                    </h2>
                </div>
            </div>
            <div class=" flex items-center gap-x-2 lg:gap-x-4">
                <img src="{{asset('icons/bank.png')}}" alt="" class=" h-12 lg:h-16">

                <div>
                    <p class=" text-sm text-gray-700 md:text-base lg:text-lg ">
                        Bank
                    </p>

                    <h2 class=" text-xl font-semibold mt-1 md:text-2xl ">
                        {{auth()->user()->virtualAccount?->bank_name ?? "NIL"}}
                    </h2>
                </div>
            </div>
            <div class=" flex items-center gap-x-2 lg:gap-x-4">
                <img src="{{asset('icons/profile.png')}}" alt="" class=" h-12 lg:h-16">

                <div>
                    <p class=" text-sm text-gray-700 md:text-base lg:text-lg ">
                        Name
                    </p>

                    <h2 class=" text-xl font-semibold mt-1 md:text-2xl ">
                        {{auth()->user()->virtualAccount?->account_name ?? "MIL"}}
                    </h2>
                </div>
            </div>
        </div>

        @if (auth()->user()->virtual_account_number || true)
        <x-primary-button class=" w-full mt-6 py-3 lg:py-4 lg:text-base rounded-xl" onclick="copyToClipboard('{{auth()->user()->virtualAccount?->account_number ?? 'NIL'}}')">
            <span class=" fa fa-copy  mr-2"></span> <span id="copy">Copy Account Number</span>
        </x-primary-button>
        @endif

        <div class=" bg-gray-50 p-4 border border-gray-200 rounded-xl mt-6 ">
            <h3 class=" font-bold mb-4 md:text-lg lg:text-xl">
                Add Money Via Bank Transfer In Just 3 Steps
            </h3>
            <ul class=" text-sm md:text-base lg:text-lg space-y-2 list-inside list-decimal">
                <li>
                    Copy the account details above- {{auth()->user()->virtualAccount?->account_number ?? 'NIL'}} is your
                    SwiftPay Account Number.
                </li>

                <li>
                    Open the bank app you want to transfer money from.
                </li>

                <li>
                    Transfer the desired amount to your SwiftPay Wallet.
                </li>
            </ul>

        </div>

    </div>

</div>
<x-slot:headerTitle>
    Transfer to SwiftPay Wallet
</x-slot:headerTitle>