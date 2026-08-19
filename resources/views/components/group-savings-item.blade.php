<div class=" shadow rounded-xl">
    <div class=" bg-white p-4 rounded-t-xl flex items-center gap-2 lg:p-6 lg:gap-4">
        <img src="{{asset('savings-interest.png')}}" alt="" class=" h-14 lg:h-20">
        <div class=" flex-1">
            <h3 class="  font-semibold mb-2 md:text-lg">
                {{ $item->name }}
            </h3>
            <div class=" text-sm md:text-base text-gray-600 flex justify-between w-full">
                <p>{{$item->type}}</p>
                <p>{{$item->members->count()}} members</p>
            </div>
        </div>
    </div>


    <a href="{{route('savings.group-savings.show', $item->hash_id)}}" class=" p-3 rounded-b-xl block bg-primary text-white md:p-4">
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