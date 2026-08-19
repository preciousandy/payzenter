<div>
    <div class="flex items-center space-x-2 bg-white p-2 border border-gray-300 rounded-xl mb-4">
        <span class="fa fa-search text-lg text-gray-400"></span>
        <input wire:model.live='query' type="text" class="py-2 w-full text-sm lg:text-base bg-inherit border-none focus:outline-0 focus:ring focus:ring-transparent">
        <button type="button" wire:click="$set('query', '')" class="p-2 h-6 w-6 inline-flex justify-center items-center bg-gray-100 rounded-full"><span class="fa fa-close"></span></button>
    </div>

    <div class=" space-y-2 divide-y">
        @foreach ($stocks as $stock)

        <a href="{{route('stocks.show', ['symbol' => $stock['symbol']])}}" class="  bg-white p-5 lg:py-7  flex justify-between items-center">
            <div class="  flex items-center gap-2 lg:gap-4">
                <img src="{{$stock['logo']}}" alt="" class=" h-8 lg:h-12 ">
                <div>
                    <h2 class="  font-medium lg:text-xl ">
                        {{$stock['company']}}
                    </h2>

                    <p @class(['text-xs md:text-sm lg:text-base font-medium mt-1', ' text-green-600'=> $stock['change'] > 0, 'text-red-600'=> $stock['change'] < 0])>
                            {{$stock['change'] > 0 ? '+' : '' }}{{$stock['change'] }} ({{round($stock['change_percent'], 2)  }}%)
                    </p>
                </div>
            </div>

            <div class=" text-right">
                <p class=" font-semibold whitespace-nowrap md:text-lg lg:text-xl">
                    $ {{formatNumber($stock['price'])}}
                </p>
                <p class=" text-xs md:text-sm lg:text-base mt-1 whitespace-nowrap text-gray-700">
                    <x-naira-symbol /> {{formatNumber($stock['price'] * $naira_to_dollar) }}
                </p>
            </div>
        </a>

        @endforeach
    </div>
</div>