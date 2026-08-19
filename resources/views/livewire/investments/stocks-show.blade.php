<div class=" px-4 py-6 max-w-4xl">
    <div class=" flex justify-between items-center">
        <div class=" flex items-center gap-4">
            <img src="{{$investment->asset_icon_url}}" alt="" class=" h-10 lg:h-14">

            <div>
                <h2 class=" text-lg md:text-xl lg:text-2xl font-semibold">
                    {{$stock['name']}}
                </h2>
                <div class=" flex items-center gap-x-4">
                    <p class=" text-gray-600 text-sm md:text-base lg:text-lg">
                        {{$stock['symbol']}}
                    </p>

                    <p @class(['text-sm md:text-base lg:text-lg font-medium', 'text-green-600'=> $stock['change'] > 0, 'text-red-600'=> $stock['change'] < 0])>
                            {{$stock['change'] > 0 ? '+' : '' }}{{$stock['change'] }} ({{round($stock['change_percent'], 2)  }}%)
                    </p>
                </div>
            </div>
        </div>

        <span class="{{$investment->status}}">
            {{$investment->status}}
        </span>
    </div>


    <div class="bg-white p-4 rounded-xl mt-6 text-gray-700 lg:p-6 lg:shadow-sm lg:mt-8 lg:text-lg lg:space-y-3">
        <p>
            <span class=" font-medium text-black ">Current Price:</span> {{formatNumber($stock['price'])}} USD
        </p>
        <p>
            <span class=" font-medium  text-black">Open Price:</span> {{formatNumber($stock['open'])}} USD
        </p>
        <p>
            <span class=" font-medium  text-black">Market Cap:</span> {{formatNumber($stock['company_market_cap'])}} USD
        </p>
    </div>

    <div class=" mt-6 grid grid-cols-2 gap-4 lg:mt-8">
        <div class=" bg-white p-4 rounded-xl flex items-center gap-2 lg:gap-4 lg:p-6 lg:shadow-sm">
            <span class=" fa fa-arrow-up text-2xl text-green-600"></span>

            <div>
                <p class=" text-sm  lg:text-base font-medium">High</p>
                <p class=" text-2xl lg:text-3xl font-semibold mt-1">{{formatNumber($stock['high'])}} <span class=" text-base font-medium">USD</span></p>
            </div>
        </div>
        <div class=" bg-white p-4 rounded-xl flex items-center gap-2 lg:gap-4 lg:p-6 lg:shadow-sm">
            <span class=" fa fa-arrow-down text-2xl text-red-600"></span>

            <div>
                <p class=" text-sm lg:text-base  font-medium">Low</p>
                <p class=" text-2xl lg:text-3xl font-semibold mt-1">{{formatNumber($stock['low'])}} <span class=" text-base font-medium">USD</span></p>
            </div>
        </div>
    </div>

    <div class=" grid grid-cols-2 gap-4 mt-6 bg-white p-3 rounded-xl lg:p-5 lg:shadow-sm">
        <div class=" ">
            <p class=" text-sm text-gray-600 md:text-base">
                Amount Invested
            </p>
            <p class=" font-semibold lg:mt-2 md:text-lg">
                <x-naira-symbol /> {{formatNumber($investment->amount_invested )}}
            </p>
        </div>

        <div class=" ">
            <p class=" text-sm text-gray-600 md:text-base">
                {{$investment->status == 'completed' ? 'Amount Earned' : 'Possible Earnings'}}
            </p>
            <p class=" font-semibold lg:mt-2 md:text-lg {{$earnings > $investment->amount_invested ? 'text-green-700' : 'text-red-700' }} ">
                <x-naira-symbol /> {{formatNumber($earnings) }}
            </p>
        </div>

        <div class=" ">
            <p class=" text-sm text-gray-600 md:text-base">
                Change Percentage
            </p>
            <p class=" font-semibold  lg:mt-2 md:text-lg">
                {{$investment->change_percentage}} %
            </p>
        </div>

        <div class=" ">
            <p class=" text-sm text-gray-600 md:text-base">
                Current Rate
            </p>
            <p class=" font-semibold lg:mt-2 md:text-lg {{$current_rate > 0 ? 'text-green-700' : 'text-red-700' }} ">
                {{$current_rate}} %
            </p>
        </div>

        <div class=" ">
            <p class=" text-sm text-gray-600 md:text-base">
                End Date
            </p>
            <p class=" font-semibold lg:mt-2 md:text-lg ">
                {{$investment->end_date->format('d M Y')}}
            </p>
        </div>


    </div>
</div>