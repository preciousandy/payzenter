<div class=" px-4 py-6">
    <h3 class=" text-lg lg:text-xl font-semibold  ">
        Closed Group Savings
    </h3>

    <div class=" mt-6 lg:mt-8 grid grid-cols-1 gap-4 lg:grid-cols-2 lg:gap-6">
        @forelse ($groupSavings as $item)
        <div class=" shadow rounded-xl">
            <div class=" bg-white p-4 rounded-t-xl flex items-center gap-2">
                <img src="{{asset('savings-interest.png')}}" alt="" class=" h-14 lg:h-20">
                <div class=" flex-1">
                    <h3 class="  font-semibold  md:text-lg">
                        {{ $item->name }}
                    </h3>
                    <div class=" text-sm md:text-base text-gray-600 flex justify-between w-full mt-2">
                        <p>{{$item->type}}</p>
                        <p>{{$item->members->count()}} members</p>
                    </div>
                </div>
            </div>


            <a href="{{route('savings.group-savings.show', $item->hash_id)}}" class=" p-3 lg:p-4 rounded-b-xl block bg-primary text-white">
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
        <div class="  p-6 lg:text-lg text-gray-700 ">
            <p class=" text-center">You have no closed group savings</p>
        </div>
        @endforelse
    </div>
</div>