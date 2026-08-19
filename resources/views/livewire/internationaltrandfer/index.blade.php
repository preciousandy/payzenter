<div class="bg-white">
    <div class=" px-4 py-6  min-h-screen max-w-5xl">
        {{-- card view --}}
        <section class=" bg-[#1400FB] p-4 text-white rounded-xl relative lg:p-6">

            <div class=" absolute top-2 right-2 lg:top-6 lg:right-6 ">
                <img src="{{asset('globe.png')}}" alt="">
            </div>

            <div class=" z-20 relative pt-10 space-y-6 lg:space-y-8">

                <div class=" flex justify-between items-center">
                    <h3 class=" text-2xl font-bold">Total Transfer</h3>

                </div>

                <p class=" text-3xl lg:text-4xl font-bold">
                    <x-naira-symbol /> {{ formatNumber($totalVolume) }}
                </p>

                <div class=" bg-blue-500 p-4 flex justify-between items-center rounded-lg border-2 border-white lg:p-6">
                    <div>
                        <h3 class=" text-xl font-semibold mb-1">
                            Transaction Volume
                        </h3>
                        <p class=" text-lg">
                            Total Balance
                        </p>
                    </div>
                    <p class=" mt-4 text-2xl lg:text-3xl font-bold">
                        <x-naira-symbol /> {{ formatNumber($totalVolume) }}
                    </p>
                </div>

                <div class="">

                    <a href="{{route('international-transfer.send-abroad')}}" class="lg:w-80 block mx-auto">
                        <x-secondary-button class=" text-gray-800 w-full bg-white">
                            <span class=" fa fa-money-bill-transfer mr-2 "></span> Send Abroad
                        </x-secondary-button>
                    </a>
                </div>

            </div>

        </section>


        <section class=" mt-10">
            <h3 class=" text-lg lg:text-xl font-semibold p-2 border-b-4 border-primary w-fit">Recent Transactions</h3>

            <div class=" mt-6 space-y-2 divide-y-2">

                @forelse ($transfers as $transferItem)
                <div class=" flex justify-between items-center p-4 bg-white lg:p-6 lg:shadow-sm ">
                    <div class=" flex items-center gap-4">
                        <img src="{{asset('https://ui-avatars.com/api/?background=random&name=' . $transferItem->recipient_name . '&rounded=true&bold=true')}}" alt="" class=" h-12 lg:h-16">

                        <div>
                            <h2 class=" text-lg lg:text-xl font-semibold">{{$transferItem->recipient_name}}
                            </h2>
                            <p class=" text-sm lg:text-base text-gray-500">
                                {{$transferItem->created_at->diffForHumans()}}
                            </p>
                        </div>
                    </div>

                    {{-- <div class=" text-success text-pending text-failed"></div> --}}

                    <div class=" text-right text-{{$transferItem->status }}">
                        <p class=" text-lg lg:text-xl font-bold ">
                            {{ formatNumber($transferItem->recipient_amount) }} {{$transferItem->recipient_currency}}
                        </p>

                        <p>
                            {{$transferItem->status}}
                        </p>
                    </div>
                </div>

                @empty

                <div class=" py-6  text-center">
                    <p class="  text-gray-700 font-medium lg:text-lg">
                        No Recent Transactions
                    </p>
                </div>
                @endforelse


            </div>
        </section>

    </div>
</div>