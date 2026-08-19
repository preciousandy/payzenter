<div>
    <div class=" bg-white p-4 rounded-t-xl flex items-center gap-2 lg:gap-4 lg:p-6 lg:shadow">
        <img src="{{asset('savings-interest.png')}}" alt="" class=" h-14 lg:h-20">
        <div class=" flex-1">
            <h3 class=" md:text-lg  font-semibold mb-2">
                {{ $item->name }}
            </h3>
            <div class=" text-sm md:text-base text-gray-500 flex justify-between w-full">
                <div class=" flex items-center gap-2">
                    <p class=" text-green-700 font-medium">
                        {{$item->type == 'locked' ? $lockedSavingsInterestRate : $unlockedSavingsInterestRate}}%
                    </p>
                    <p> <span class="fa fa-{{$item->type == 'locked' ? 'lock' : 'lock-open'}} mr-1"></span> {{$item->type}}</p>
                </div>

                <p class="{{$item->status}}">{{$item->status}}</p>

            </div>
        </div>
    </div>
    <a href="{{route('savings.interest-savings.show', $item->hash_id)}}" class=" p-3 md:p-4 rounded-b-xl block bg-[#1400FB] text-white">
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