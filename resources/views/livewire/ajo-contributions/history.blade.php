<div class=" px-4 py-6 lg:p-6 max-w-4xl">
    <h2 class="  font-bold  text-lg w-fit lg:text-xl">
        Ajo Contributions History
    </h2>

    <p class=" text-sm md:text-base text-gray-700 mt-2">
        Here is all closed Ajo Contributions you participated in.
    </p>

    <div class=" mt-6  divide-y-2 ">
        @forelse ($contributions as $contribution)
        <a href="{{route('ajo-contributions.show', $contribution->hash_id)}}" class=" block py-4 lg:py-6 ">
            <div class=" flex items-center gap-x-3 md:gap-4 ">
                <div class=" bg-blue-100 h-16 w-16 md:h-20 md:w-20 rounded-md flex items-center justify-center flex-shrink-0 relative">
                    <span class="fa fa-piggy-bank text-[#1400FB]  text-3xl lg:text-4xl"></span>
                    <span class="fa fa-square-check text-[#1400FB]  text-xl absolute -bottom-1 right-0 z-10  "></span>
                </div>

                <div class=" space-y-2 w-full lg:space-y-3">
                    <div class=" flex items-start justify-between text-sm ">
                        <h3 class=" text-lg lg:text-xl font-semibold">
                            {{$contribution->name}}
                        </h3>

                        <p class=" text-green-500 font-semibold whitespace-nowrap text-lg lg:text-2xl">

                            <x-naira-symbol /> {{ formatNumber($contribution->amount)}}
                        </p>
                    </div>
                    <div class=" flex items-center justify-between text-sm md:text-base ">
                        <p class=" font-medium text-gray-600">
                            {{ucfirst($contribution->frequency)}} Savings deposit
                        </p>

                        <p class=" whitespace-nowrap">
                            <x-naira-symbol /> {{formatNumber($contribution->amount * $contribution->no_of_members)}}
                        </p>
                    </div>
                </div>
            </div>
        </a>
        @empty

        <div class=" text-lg p-8 w-full text-gray-700">
            <p class=" text-center">
                No closed Ajo Contributions
            </p>
        </div>

        @endforelse



    </div>
</div>