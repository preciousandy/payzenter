<a class=" block" href="{{route('transaction-receipt', $transaction->hash_id)}}">
    <div class="p-3 bg-white rounded-md border-l-4 {{$transaction->status}}-border flex items-center justify-between gap-x-2  w-full lg:p-5 lg:shadow-sm">
        <div>
            <p class=" text-sm lg:text-base font-medium mb-2">
                {{$transaction->message}}
            </p>
            <div>
                <span class=" text-xs text-gray-600 font-medium">
                    {{ ucfirst(str_replace('_', ' ', $transaction->source))}}
                </span>
                -
                <span class=" text-xs md:text-sm lg:text-base text-gray-600 mt-1 md:mt-2">
                    {{$transaction->created_at->format('d M Y H:i:s')}}
                </span>
            </div>
        </div>



        <div class=" text-right ">
            <p class=" font-semibold whitespace-nowrap lg:text-lg">
                {{$transaction->type == 'credit' ? "+" : '-'}}
                <x-naira-symbol /> {{formatNumber($transaction->amount)}}
            </p>
            <span class="{{$transaction->status}} text-xs md:text-sm lg:text-base inline-block mt-1 md:mt-2">
                {{$transaction->status}}
            </span>
        </div>
    </div>
</a>