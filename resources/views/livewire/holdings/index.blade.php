<div class=" px-4 py-8 max-w-5xl ">

    <div class=" bg-primary text-white w-full p-8 lg:p-10 pb-0 rounded-xl">

        <div class=" flex justify-between items-center">
            <div>
                <p class=" text-3xl lg:text-4xl font-bold ">
                    <x-naira-symbol />{{formatNumber($totalAssets)}}
                </p>


            </div>

            <p class=" w-fit px-4 py-2 rounded-xl text-green-700 bg-white text-sm  font-medium lg:text-lg">
                50%
            </p>
        </div>

        <div class=" bg-white w-fit mx-auto text-gray-800 grid grid-cols-3 gap-3 p-4 rounded-t-xl mt-12 lg:rounded-xl lg:p-6 lg:gap-x-6">
            <a href="{{route('holdings.assets')}}">
                <div>
                    <p class=" text-center">
                        <span class="fa fa-chart-line lg:text-3xl "></span>
                    </p>
                    <p class=" text-center text-sm md:text-base lg:text-lg lg:mt-2 mt-1 whitespace-nowrap">Analytics</p>
                </div>
            </a>
            <a href="{{route('holdings.assets')}}">
                <div>
                    <p class=" text-center">
                        <span class="fa fa-play lg:text-3xl "></span>
                    </p>
                    <p class=" text-center text-sm mt-1 whitespace-nowrap md:text-base lg:text-lg lg:mt-2">Start Holdings</p>
                </div>
            </a>
            <a href="{{route('holdings.history')}}">
                <div>
                    <p class=" text-center">
                        <span class="fa fa-history lg:text-3xl "></span>
                    </p>
                    <p class=" text-center text-sm mt-1 whitespace-nowrap md:text-base lg:text-lg lg:mt-2">History</p>
                </div>
            </a>

        </div>

    </div>


    <h3 class=" text-lg md:text-xl lg:text-2xl font-semibold my-6 lg:my-8">
        My Holdings
    </h3>


    <div class=" space-y-4">
        @forelse ($holdings as $holding)

        <a href="{{$holding->asset_type === 'fiat' ? route('fiats.show', [$holding->asset_symbol, 'holding' => $holding->hash_id ]) : route('metals.show', [$holding->asset_symbol, 'holding' => $holding->hash_id ])}}" class="  bg-white p-4 rounded-lg flex justify-between items-center lg:shadow-sm lg:p-6">
            <div class="  flex items-center gap-2">
                <img src="{{asset($holding->icon_url)}}" alt="" class=" h-8 w-8 object-cover lg:h-12 lg:w-12 ">
                <div>
                    <h2 class="   font-medium md:text-lg lg:text-xl">
                        {{$holding->asset_name}}
                    </h2>

                    <p class=" text-xs  md:text-sm lg:text-base text-gray-500 md:mt-2 md:text-gray-600">
                        {{formatNumber($holding->naira_start_rate)}} <span class=" fa fa-arrow-right mx-1"></span> {{formatNumber($holding->current_rate)}}
                    </p>
                </div>
            </div>

            <div class=" text-right ">
                <p class=" font-semibold text-green-700 whitespace-nowrap text-sm  lg:text-base">
                    <span class=" text-base md:text-lg lg:text-xl">{{$holding->asset_amount}}</span> {{$holding->asset_symbol}}
                </p>
                <p class=" text-sm mt-1 text-gray-600 lg:mt-2 lg:text-base">
                    {{$holding->end_date->format('d M Y') }}
                </p>
            </div>
        </a>

        @empty

        <div class="  p-4 rounded-xl text-center">
            <p class=" text-gray">
                No active holdings
            </p>
        </div>

        @endforelse
    </div>

</div>