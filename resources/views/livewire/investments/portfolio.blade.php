<div class=" px-4 py-8 max-w-5xl ">

    <div class=" rounded-xl h-60 lg:h-80 bg-no-repeat bg-center bg-cover p-8 lg:p-10" style="background-image: url('/investment.png')">
        <div>
            <p class=" font-medium text-gray-600 md:text-lg lg:text-xl">Total Active Investments </p>
            <h2 class=" text-3xl lg:text-5xl font-bold text-primary mt-2 lg:mt-4">
                <x-naira-symbol /> {{formatNumber($totalAsset)}}
            </h2>
        </div>
    </div>

    <a href="{{route('investments.index')}}" class=" block w-fit mx-auto mt-6 lg:mt-8 lg:w-80">
        <x-primary-button class=" px-16  mt-4 rounded-xl">
            Invest Now
        </x-primary-button>
    </a>


    <div class=" flex justify-between items-center mt-6 lg:mt-8">
        <h3 class=" text-lg md:text-xl lg:text-2xl font-semibold ">
            My Holdings
        </h3>


        <a href="{{route('investments.history')}}" class=" font-semibold text-primary text-sm md:text-base lg:text-lg">
            View History <span class=" fa fa-chevron-right "></span>
        </a>
    </div>


    <div class=" space-y-4 mt-4 lg:mt-6">
        @forelse ($investments as $investment)

        <a href="{{$investment->asset_type === 'crypto' ? route('investments.crypto.show',$investment->hash_id ) : route('investments.stocks.show',$investment->hash_id )}}" class="  bg-white p-4 rounded-lg flex justify-between items-center gap-3 lg:p-6 lg:shadow-sm">
            <div class="  flex items-center gap-2 lg:gap-4">
                <img src="{{asset($investment->asset_icon_url)}}" alt="" class=" h-8 w-8 object-cover lg:h-14 lg:w-14  ">
                <div>
                    <h2 class="   font-medium md:text-lg lg:text-xl">
                        {{$investment->asset_name}}
                    </h2>

                    <p class=" text-xs md:text-sm lg:text-base text-gray-600 mt-1">
                        {{$investment->change_percentage}} <span class=" fa fa-arrow-right mx-1"></span> {{$investment->current_rate}}
                    </p>
                </div>
            </div>

            <div class=" text-right text-sm ">
                <p class=" font-semibold text-lg lg:text-xl  {{$investment->current_earnings > $investment->amount_invested ? 'text-green-700' : 'text-red-700' }}">
                    <x-naira-symbol /> {{formatNumber($investment->current_earnings)}}
                </p>
                <p class=" text-sm lg:text-base mt-1 text-gray-700">
                    <x-naira-symbol /> {{formatNumber($investment->amount_invested)}}
                </p>
            </div>
        </a>

        @empty

        <div class="  p-4 rounded-xl text-center">
            <p class=" text-gray">
                No active investments
            </p>
        </div>

        @endforelse
    </div>

</div>