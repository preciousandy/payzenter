<div class=" px-4 py-6 max-w-4xl lg:p-8">
    <h3 class=" text-lg lg:text-2xl font-semibold">
        {{$savings->type}} Ajo Savings
    </h3>

    <div class=" mt-6 space-y-4">
        @foreach ($transactions as $transaction)

        <div class=" flex items-center justify-between bg-white p-4 lg:p-6 lg:shadow-sm rounded-lg ">
            <div class=" flex items-center gap-2">
                <p @class([' rounded-lg text-2xl h-12 w-12 flex items-center justify-center ', ' bg-green-200 text-green-800'=> $transaction->status == 'successful', 'bg-red-200 text-red-800' => $transaction->status == 'failed'])>
                    <span class="fa fa-inbox block"></span>
                </p>

                <div>
                    <h3 class=" font-medium text-sm md:text-base">
                        {{$transaction->message}}
                    </h3>


                    <p class=" text-xs md:text-sm  text-gray-600 mt-1 lg:mt-2">
                        {{$transaction->created_at->diffForHumans()}}
                    </p>
                </div>
            </div>

            <div>
                <h3 class=" text-lg font-semibold">
                    <x-naira-symbol /> {{ formatNumber($transaction->amount)}}
                </h3>

                <p class=" text-right md:mt-2">
                    <span class="{{$transaction->status}} text-xs w-fit ">
                        {{$transaction->status}}
                    </span>
                </p>
            </div>

        </div>

        @endforeach
    </div>
</div>