<div>
    <div class="flex items-center space-x-2 bg-white p-2 lg:p-3 border border-gray-300 rounded-xl ">
        <span class="fa fa-search text-lg text-gray-400"></span>
        <input wire:model.live='query' type="text" class="py-2 px-4 w-full text-sm bg-inherit border-none focus:outline-0 focus:ring focus:ring-transparent">
        <button type="button" wire:click="$set('query', '')" class="p-2 h-6 w-6 inline-flex justify-center items-center bg-gray-100 rounded-full"><span class="fa fa-close"></span></button>
    </div>


    <div class=" space-y-4 lg:space-y-6 mt-4 lg:mt-6">
        @foreach ($metals as $metal)
        <a href="{{route('metals.show', $metal->metal)}}" class="  bg-white p-4 rounded-lg flex justify-between items-center lg:shadow-sm lg:p-6">
            <div class="  flex items-center gap-2">
                <img src="{{asset('metal.png')}}" alt="" class=" h-10 lg:h-14 ">
                <div>
                    <h2 class=" text-lg  lg:text-xl font-medium">
                        {{ucfirst(str_replace('_', ' ', $metal->metal))}}
                    </h2>

                    {{-- <p>
                        {{$fiat->code}}
                    </p> --}}
                </div>
            </div>

            <div class=" text-right">
                <p class=" font-semibold text-green-700 md:text-lg lg:text-xl ">
                    <x-naira-symbol /> {{formatNumber($metal->price) }}
                </p>
                {{-- <p class=" text-sm mt-1">
                    <x-naira-symbol /> {{formatNumber($asset['price'] * $naira_to_dollar) }}
                </p> --}}
            </div>
        </a>

        @endforeach
    </div>
</div>