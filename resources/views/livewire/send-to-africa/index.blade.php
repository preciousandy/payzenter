<div class="bg-white">
    <div class=" px-4 py-6 max-w-5xl  min-h-screen lg:p-6">
        {{-- card view --}}
        <section class=" bg-[#1400FB] p-4 text-white rounded-xl relative lg:p-6">
            <div class=" z-20 relative space-y-4 lg:space-y-6">
                <div class=" flex items-center justify-between">
                    <div>
                        <div class=" ">
                            <h3 class=" text-lg lg:text-xl font-bold">SwiftPay Balance</h3>
                            <p class=" text-sm md:text-base lg:text-lg text-gray-200 font-medium mt-1">
                                International Transfers
                            </p>
                        </div>

                        <div class=" flex items-center gap-x-4 mt-2">
                            <p class=" text-xl md:text-2xl lg:text-3xl font-bold whitespace-nowrap">
                                <x-naira-symbol /> {{ formatNumber(auth()->user()->wallet_balance) }}
                            </p>

                            <a href="{{route('user.fund-wallet')}}">
                                <button class=" bg-white px-3 py-2 rounded-lg text-primary text-xs md:text-sm lg:text-base lg:px-6 font-medium">
                                    Deposit
                                </button>
                            </a>
                        </div>
                    </div>

                    <div class=" ">
                        <img src="{{asset('africa.png')}}" alt="" class=" h-48 lg:h-64">
                    </div>
                </div>

                <div class=" bg-blue-500 p-4 flex justify-between items-center rounded-lg border-2 border-white lg:p-6">
                    <div>
                        <h3 class=" font-semibold mb-1 lg:text-lg">
                            Transaction Volume
                        </h3>
                        <p class=" text-xs md:text-sm lg:text-base">
                            Total Transfer
                        </p>
                    </div>
                    <p class="  text-xl md:text-2xl lg:text-3xl font-bold">
                        $ {{ formatNumber($totalAmount / $dollarRate) }}
                    </p>
                </div>

                <div class="  grid grid-cols-2 gap-x-2 lg:gap-6">
                    <a href="{{route('transfer.index', ['source_link' => 'send_to_africa'])}}">
                        <x-secondary-button class="  text-gray-800 bg-white text-xs md:text-sm lg:text-base px-0 w-full ">
                            <span class=" fa fa-paper-plane mr-1"></span> Send Money
                        </x-secondary-button>
                    </a>
                    <a href="{{route('referrals')}}">
                        <x-secondary-button class="  text-gray-800 w-full bg-white text-xs md:text-sm lg:text-base px-0 ">
                            <span class=" fa fa-money-bill-transfer mr-2"></span> Receive Money
                        </x-secondary-button>
                    </a>
                </div>

            </div>

        </section>

        <h3 class=" mt-6 font-semibold py-2 border-b-4 border-primary w-fit lg:mt-8 md:text-lg lg:text-xl">
            Recent Transactions
        </h3>

        <div class=" mt-4  divide-y lg:mt-6">
            @forelse ($transfers as $transferItem)
            <div class=" flex items-center justify-between py-3 lg:py-5">
                <div class=" flex items-center gap-4">
                    <img src="{{$transferItem['profile_pic'] ??  asset(randomImage($transferItem['account_name']))}}" alt="" class=" h-10 w-10 rounded-full object-cover lg:h-14 lg:w-14">

                    <div class="">
                        <p class=" font-medium lg:text-lg lg:font-semibold">{{$transferItem['account_name']}}</p>
                        <p class=" text-sm text-gray-600 mt-1 md:text-base">{{$transferItem['type']}} </p>
                    </div>
                </div>

                <div>
                    <h2 class=" font-semibold text-right whitespace-nowrap {{$transferItem['status']}} bg-transparent p-0 lg:text-lg ">
                        <x-naira-symbol />{{ formatNumber($transferItem['amount']) }}
                    </h2>
                    <p class=" text-right text-xs text-gray-600 mt-1 whitespace-nowrap md:text-sm lg:text-base">
                        {{$transferItem['date']}}
                    </p>
                </div>
            </div>

            @empty
            <p class=" text-center text-gray-600 py-8 px-4">No recent International transfers</p>
            @endforelse
        </div>
    </div>
</div>