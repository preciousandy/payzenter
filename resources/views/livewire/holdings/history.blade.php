<div class=" px-4  py-6 max-w-5xl">

    <div>
        <h2 class=" text-lg md:text-xl lg:text-2xl font-semibold">
            Your Holdings history
        </h2>
    </div>

    <div class=" space-y-4 mt-6 lg:mt-8 lg:space-y-6">
        @forelse ($holdings as $holding)

        <div class="  bg-white p-4 rounded-lg block lg:p-6 lg:shadow-sm  " x-data="{showDetails: false} ">
            <div class=" flex justify-between items-center">
                <div class="  flex items-center gap-2 md:gap-4">
                    <img src="{{asset($holding->icon_url)}}" alt="" class=" h-10 lg:h-14 ">
                    <div>
                        <h2 class=" text-lg text-left lg:text-xl  font-semibold">
                            {{$holding->asset_name}}
                        </h2>

                        <p class=" text-sm text-gray-600 md:text-base lg:text-lg md:mt-2">
                            {{$holding->asset_type}}
                        </p>
                    </div>
                </div>

                <button class=" text-right  p-3" @click=" showDetails =  !showDetails">
                    <span class=" fa fa-chevron-down text-xl md:text-2xl lg:text-3xl" x-cloak x-show="!showDetails"> </span>
                    <span class=" fa fa-chevron-up text-xl md:text-2xl lg:text-3xl" x-cloak x-show="showDetails"> </span>
                </button>
            </div>

            <div class=" grid grid-cols-2 gap-4 mt-4 md:mt-6 lg:gap-6" x-show="showDetails" x-transition x-cloak>
                <div class=" ">
                    <p class=" text-sm text-gray-600 md:text-base lg:text-lg">
                        Amount Invested
                    </p>
                    <p class=" font-semibold mt-1 md:text-lg lg:text-xl md:mt-2 ">
                        <x-naira-symbol /> {{formatNumber($holding->amount)}}
                    </p>
                </div>

                <div class=" ">
                    <p class=" text-sm text-gray-600 md:text-base lg:text-lg">
                        Asset Invested
                    </p>
                    <p class=" font-semibold mt-1 md:text-lg lg:text-xl md:mt-2  ">
                        {{formatNumber($holding->asset_amount)}} ({{$holding->asset_symbol}})
                    </p>
                </div>

                <div class=" ">
                    <p class=" text-sm text-gray-600 md:text-base lg:text-lg">
                        Amount Returned
                    </p>
                    <p class=" font-semibold mt-1 md:text-lg lg:text-xl md:mt-2 {{$holding->amount_earned > $holding->amount ? 'text-green-700' : 'text-red-700' }} ">
                        <x-naira-symbol /> {{formatNumber($holding->amount_earned)}}
                    </p>
                </div>


                <div class=" ">
                    <p class=" text-sm text-gray-600 md:text-base lg:text-lg">
                        Profit/Lost
                    </p>
                    <p class=" font-semibold mt-1 md:text-lg lg:text-xl md:mt-2 {{$holding->amount_earned - $holding->amount > 0 ? 'text-green-700' : 'text-red-700' }} ">
                        <x-naira-symbol /> {{formatNumber($holding->amount_earned - $holding->amount)}}
                    </p>
                </div>

                <div class=" ">
                    <p class=" text-sm text-gray-600 md:text-base lg:text-lg">
                        Initial Rate
                    </p>
                    <p class=" font-semibold mt-1 md:text-lg lg:text-xl md:mt-2 text--700">
                        <x-naira-symbol /> {{formatNumber($holding->naira_start_rate)}}
                    </p>
                </div>

                <div class=" ">
                    <p class=" text-sm text-gray-600 md:text-base lg:text-lg">
                        End Rate
                    </p>
                    <p class=" font-semibold mt-1 md:text-lg lg:text-xl md:mt-2 {{$holding->naira_end_rate > $holding->naira_start_rate ? 'text-green-700' : 'text-red-700' }} ">
                        <x-naira-symbol /> {{formatNumber($holding->naira_end_rate)}}
                    </p>
                </div>

                <div class=" ">
                    <p class=" text-sm text-gray-600 md:text-base lg:text-lg">
                        Date Completed
                    </p>
                    <p class=" font-semibold mt-1 md:text-lg lg:text-xl md:mt-2 ">
                        {{$holding->end_date->format('d M Y')}}
                    </p>
                </div>


            </div>
        </div>

        @empty

        <div class="  p-4 rounded-xl text-center">
            <p class=" text-gray">
                You have no holdings yet
            </p>
        </div>

        @endforelse
    </div>
</div>