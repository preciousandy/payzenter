<div class=" px-4 py-6">
    <x-blank-page-header title="Status Information" />
    <div class=" mt-6 space-y-4" x-data="{level: @entangle(
        'current_level'
    )}">
        @foreach ($levels as $level => $limit)

        <div class=" border border-gray-200 rounded-lg">
            <div class="  flex items-center gap-3 p-4 rounded-t-lg" :class="level == '{{$level}}' ? 'bg-primary text-white' : 'bg-[#D4DFFC]' ">
                <img src="{{asset('badges/'. $level  .'.png')}}" alt="" class=" h-10 md:h-14">

                <h4 class=" font-medium md:text-lg">
                    {{ucfirst($level)}} Badge
                </h4>
            </div>

            <div class="p-4 flex flex-col md:flex-row md:items-center justify-between gap-4" :class="level == '{{$level}}' && 'bg-[#D4DFFC]' ">

                <p class=" whitespace-nowrap text-sm text-gray-600 md:text-base">
                    Minimum Daily Transaction
                </p>

                <p>
                    <span class="font-medium">₦{{number_format($limit['min'])}}</span> -
                    <span class="font-medium">{{$level == 'gold' ? 'Above' : '₦' . number_format($limit['max']) }}</span>
                </p>
            </div>
        </div>

        @endforeach
    </div>
</div>