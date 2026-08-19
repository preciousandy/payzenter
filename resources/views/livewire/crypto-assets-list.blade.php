<div>
    <div class="flex items-center space-x-2 bg-white p-2 border border-gray-300 rounded-xl mb-4">
        <span class="fa fa-search text-lg text-gray-400"></span>
        <input wire:model.live='query' type="text" class="py-2  w-full text-sm lg:text-base bg-inherit border-none focus:outline-0 focus:ring focus:ring-transparent">
        <button type="button" wire:click="$set('query', '')" class="p-2 h-6 w-6 inline-flex justify-center items-center bg-gray-100 rounded-full"><span class="fa fa-close"></span></button>
    </div>


    <div class=" space-y-2 divide-y">
        @foreach ($data as $asset)

        <a href="{{route('holdings.assets.show', $asset['uuid'])}}" class="  bg-white p-4 lg:py-6  flex justify-between items-center">
            <div class="  flex items-center gap-2 lg:gap-4">
                <img src="{{asset($asset['icon'])}}" alt="" class=" h-10 w-10 object-cover lg:h-14 lg:w-14 ">
                <h2 class=" text-lg lg:text-xl lg:font-medium ">
                    {{$asset['name']}}
                </h2>
            </div>

            <div class=" text-right">
                <p class=" font-semibold lg:text-lg">
                    $ {{formatNumber($asset['price_in_usd'])}}
                </p>
                <p class=" text-sm mt-1 text-gray-700 lg:text-base lg:mt-2">
                    <x-naira-symbol /> {{formatNumber($asset['price_in_usd'] * $naira_to_dollar) }}
                </p>
            </div>
        </a>

        @endforeach
    </div>
</div>