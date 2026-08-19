<div class=" px-4 py-6 max-w-6xl">
    <h3 class=" text-xl lg:text-2xl font-semibold ">
        Your Closed Savings
    </h3>

    <div class=" mt-6 grid grid-cols-1 gap-4 lg:grid-cols-2 lg:gap-6">
        @forelse ($savings as $item)
        <div>
            <div class=" bg-white p-4 rounded-t-xl flex items-center gap-2 lg:gap-4 lg:p-6">
                <img src="{{asset('savings-interest.png')}}" alt="" class=" h-14 lg:h-20">
                <div class=" flex-1">
                    <h3 class=" text-lg md:text-xl font-semibold mb-2">
                        {{ $item->name }}
                    </h3>
                    <div class=" text-sm md:text-base text-gray-600 flex justify-between w-full mt-2">
                        <div class=" flex items-center gap-2">
                            <p>
                                Closed on {{$item?->end_date?->format('d M, Y')}}
                            </p>
                        </div>

                        <p class="{{$item->status}}">{{$item->status}}</p>

                    </div>
                </div>
            </div>
            <a href="{{route('savings.interest-savings.show', $item->hash_id)}}" class=" p-4 rounded-b-xl block bg-[#1400FB] text-white">
                <div class=" flex justify-between">
                    <div></div>
                    <p class=" text-3xl font-bold ">
                        <x-naira-symbol /> {{formatNumber($item->balance)}}
                    </p>

                    <button>
                        <span class=" fa fa-arrow-circle-right  text-3xl"></span>
                    </button>

                </div>
            </a>
        </div>


        @empty

        <div>
            <div class="  p-4 rounded-t-xl flex items-center gap-2">
                <div class=" flex-1">
                    <h3 class=" text-lg font-medium  text-center text-gray-700">
                        No Closed Savings
                    </h3>
                </div>
            </div>
        </div>

        @endforelse
    </div>
</div>