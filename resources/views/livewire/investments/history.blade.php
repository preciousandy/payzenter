<div class=" px-4  py-6 max-w-5xl">

    <div>
        <h2 class=" text-lg lg:text-xl font-semibold">
            Your Investments history
        </h2>
    </div>

    <div class=" space-y-4 mt-6 lg:mt-8 lg:space-y-6">
        @forelse ($investments as $investment)

        <div class="  bg-white p-4 rounded-lg block lg:p-6 lg:shadow-sm " x-data="{showDetails: false}">
            <div class=" flex justify-between items-center">
                <div class="  flex items-center gap-2 lg:gap-4">
                    <img src="{{asset($investment->asset_icon_url)}}" alt="" class=" h-10 lg:h-14 ">
                    <div>
                        <h2 class=" text-lg lg:text-xl  font-medium">
                            {{$investment->asset_name}}
                        </h2>

                        <p class=" text-sm md:text-base text-gray-600">
                            {{$investment->asset_type}}
                        </p>
                    </div>
                </div>

                <button class=" text-right  p-3 lg:p-4" @click=" showDetails =  !showDetails">
                    <span class=" fa fa-chevron-down text-xl lg:text-2xl" x-show="!showDetails"> </span>
                    <span class=" fa fa-chevron-up text-xl lg:text-2xl" x-show="showDetails"> </span>
                </button>
            </div>

            <div class=" grid grid-cols-2 gap-4 mt-4 lg:mt-6" x-show="showDetails" x-transition x-cloak>
                <div class=" ">
                    <p class=" text-sm text-gray-600 md:text-base">
                        Amount Invested
                    </p>
                    <p class=" font-semibold mt-1 lg:mt-2 md:text-lg">
                        <x-naira-symbol /> {{formatNumber($investment->amount_invested)}}
                    </p>
                </div>

                <div class=" ">
                    <p class=" text-sm text-gray-600 md:text-base">
                        Amount Returned
                    </p>
                    <p class=" font-semibold mt-1 lg:mt-2 md:text-lg {{$investment->amount_earned > $investment->amount_invested ? 'text-green-700' : 'text-red-700' }} ">
                        <x-naira-symbol /> {{formatNumber($investment->amount_earned)}}
                    </p>
                </div>

                <div class=" ">
                    <p class=" text-sm text-gray-600 md:text-base">
                        Initial Rate
                    </p>
                    <p class=" font-semibold mt-1 lg:mt-2 md:text-lg ">
                        {{formatNumber($investment->change_percentage)}}%
                    </p>
                </div>

                <div class=" ">
                    <p class=" text-sm text-gray-600 md:text-base">
                        Final Percentage Change
                    </p>
                    <p class=" font-semibold mt-1  lg:mt-2 md:text-lg{{$investment->final_change_percentage > 0 ? 'text-green-700' : 'text-red-700' }} ">
                        {{formatNumber($investment->final_change_percentage)}}%
                    </p>
                </div>

                <div class=" ">
                    <p class=" text-sm text-gray-600 md:text-base">
                        Profit/Lost
                    </p>
                    <p class=" font-semibold mt-1 lg:mt-2 md:text-lg {{$investment->amount_earned - $investment->amount_invested > 0 ? 'text-green-700' : 'text-red-700' }} ">
                        <x-naira-symbol /> {{formatNumber($investment->amount_earned - $investment->amount_invested)}}
                    </p>
                </div>

                <div class=" ">
                    <p class=" text-sm text-gray-600 md:text-base">
                        Date Completed
                    </p>
                    <p class=" font-semibold mt-1 lg:mt-2 md:text-lg ">
                        {{$investment->end_date->format('d M Y')}}
                    </p>
                </div>


            </div>
        </div>

        @empty

        <div class="  p-4 rounded-xl text-center">
            <p class=" text-gray">
                You have no completed investments yet
            </p>
        </div>

        @endforelse
    </div>
</div>