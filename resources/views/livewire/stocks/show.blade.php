<div class=" px-4 py-6 max-w-4xl">
    <div class=" flex justify-between items-center">
        <div class=" flex items-center gap-4">
            <img src="{{asset('trend.png')}}" alt="" class=" h-10 lg:h-16">

            <div>
                <h2 class=" text-lg lg:text-xl font-semibold">
                    {{$stock['name']}}
                </h2>
                <div class=" flex items-center gap-x-4">
                    <p class=" text-gray-600 text-sm md:text-base">
                        {{$stock['symbol']}}
                    </p>

                    <p @class(['text-sm font-medium md:text-base', 'text-green-600'=> $stock['change'] > 0, 'text-red-600'=> $stock['change'] < 0])>
                            {{$stock['change'] > 0 ? '+' : '' }}{{$stock['change'] }} ({{round($stock['change_percent'], 2)  }}%)
                    </p>
                </div>
            </div>
        </div>

        <a href="{{route('investments.create', ['name' => $stock['name'], 'symbol' => $stock['symbol'], 'change_percentage' => $stock['change_percent'], 'type' => 'stock', 'icon_url' => asset('trend.png')])}}">
            <x-primary-button class=" px-6 text-xs md:text-base mt-4 rounded-xl lg:w-40 ">
                Invest
            </x-primary-button>
        </a>
    </div>

    {{-- @if ($stock['about'])

    <p class=" bg-white p-4 rounded-xl mt-6">
        {{$stock['about']}}
    </p>

    @endif --}}


    <div class="bg-white p-4 rounded-xl mt-6 text-gray-700 lg:p-6 lg:shadow-sm lg:mt-8 lg:text-lg lg:space-y-3">
        <p>
            <span class=" font-medium text-black ">Current Price:</span> {{formatNumber($stock['price'])}} USD
        </p>
        <p>
            <span class=" font-medium  text-black">Open Price:</span> {{formatNumber($stock['open'] ?? 0)}} USD
        </p>
        <p>
            <span class=" font-medium  text-black">Market Cap:</span> {{formatNumber($stock['company_market_cap'])}} USD
        </p>
    </div>

    <div class=" mt-6 grid grid-cols-2 gap-4 lg:mt-8 lg:gap-6">
        <div class=" bg-white p-4 rounded-xl flex items-center gap-2 lg:p-6 lg:gap-4 lg:shadow-sm">
            <span class=" fa fa-arrow-up text-2xl text-green-600"></span>

            <div>
                <p class=" text-sm md:text-base  font-medium">High</p>
                <p class=" text-2xl lg:text-3xl lg:mt-3 font-semibold mt-1">{{formatNumber($stock['high'])}} <span class=" text-base font-medium">USD</span></p>
            </div>
        </div>
        <div class=" bg-white p-4 rounded-xl flex items-center gap-2 lg:p-6 lg:gap-4 lg:shadow-sm">
            <span class=" fa fa-arrow-down text-2xl text-red-600"></span>

            <div>
                <p class=" text-sm  md:text-base font-medium">Low</p>
                <p class=" text-2xl lg:text-3xl lg:mt-3 font-semibold mt-1">{{formatNumber($stock['low'])}} <span class=" text-base font-medium">USD</span></p>
            </div>
        </div>
    </div>
</div>