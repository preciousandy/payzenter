<div x-data="{showModal:true}">


    <div x-show="showModal" x-transition x-cloak class=" fixed bg-black/60 z-50 top-0 left-0 h-full w-full flex items-center justify-center py-4 px-6">
        <div class=" bg-white w-full rounded-xl p-4 ">
            <div class=" flex justify-end ">
                <button wire:click='closeModal'>
                    <span class=" fa fa-close text-2xl text-red-700"></span>
                </button>
            </div>

            @foreach ($levels as $level => $limit)

            @continue($level != $current_level)
            <div class=" ">
                <div class=" ">
                    <h3 class="text-center  mt-2 mb-4 font-medium">
                        Congrats 🎉 <br> on reaching a new level
                    </h3>
                    <img src="{{asset('badges/'. $level  .'.png')}}" alt="" class=" h-28 mx-auto">

                    <h4 class=" text-lg font-semibold text-center mt-2">
                        {{ucfirst($level)}} Badge
                    </h4>
                </div>

                <div class=" flex items-center justify-between gap-4 mt-4">

                    <p class=" whitespace-nowrap">
                        Today's Transaction
                    </p>

                    <p class=" text-right text-lg">
                        <span class="font-medium">₦{{number_format($today_transactions_total)}} </span>
                    </p>
                </div>
                <div class=" flex items-center justify-between gap-4 mt-2">

                    <p class=" whitespace-nowrap">
                        Progress to next level
                    </p>

                    <div class=" w-full text-right">
                        <p>{{number_format($percentage_remaining, 2)}}%</p>
                        <div class=" h-4 bg-blue-100 rounded-md">
                            <div class=" h-full bg-blue-500 rounded-md" style="width: {{$percentage_remaining}}%"></div>
                        </div>
                        <p class=" text-right text-sm mt-1">
                            Remaining
                            <span class="font-medium text-sm">₦{{number_format($amount_remaining)}}</span>

                        </p>
                    </div>
                </div>
            </div>

            @endforeach
        </div>
    </div>
</div>