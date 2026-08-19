<div class=" bg-white">
    <div class=" px-4 py-6 bg-white min-h-screen max-w-4xl lg:p-8">
        <h3 class=" text-lg lg:text-xl font-semibold">
            Closed Ajo Savings
        </h3>

        <div class=" mt-6 lg:mt-8 space-y-4 lg:space-y-6">

            @forelse ($savings as $saving)
            <a href="{{route('ajo-savings.show', $saving->hash_id)}}" class=" block border border-gray-200 p-4 rounded-xl">
                <div class=" flex items-center gap-3 ">
                    <div class=" bg-[#1400FB] h-20 w-20 rounded-xl flex items-center justify-center flex-shrink-0 p-3">
                        <img src="{{asset('ajo-savings-icon.png')}}" alt="" class=" h-10  ">
                    </div>

                    <div class=" space-y-2 w-full">
                        <h3 class=" font-semibold lg:text-lg">
                            {{$saving->type}} Ajo Savings
                        </h3>

                        <div class=" flex items-start justify-between text-sm md:text-base">
                            <p>
                                Amount Earned
                                <span class=" font-medium text-base">
                                    <x-naira-symbol /> {{ formatNumber($saving->amount_earned)}}
                                </span>
                            </p>
                        </div>



                        <div class=" flex items-center">

                            <p class=" text-sm lg:text-base whitespace-nowrap">
                                Closed {{$saving->end_date->diffForHumans()}}
                            </p>
                        </div>
                    </div>
                </div>
            </a>
            @empty

            <div class=" text-lg p-8 w-full text-gray-700">
                <p class=" text-center">
                    No ongoing Ajo savings
                </p>
            </div>

            @endforelse



        </div>
    </div>
</div>