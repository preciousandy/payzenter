<div class=" p-4 py-6 max-w-2xl lg:mx-auto w-full">
    <div class=" bg-blue-50 p-4 lg:p-6 border border-blue-400 rounded-t-xl text-sm">

        <div class=" flex justify-between items-center">
            <img src="{{asset('logo-welcome.png')}}" alt="" class=" h-10 lg:h-14">

            <p class=" md:text-lg">
                Transaction Receipt
            </p>
        </div>

        <div class=" mt-6 text-center space-y-2 lg:space-y-3">
            <p class=" text-3xl lg:text-4xl font-bold text-green-700">
                <x-naira-symbol /> {{formatNumber($transfer->total_amount)}}
            </p>

            <p class=" md:text-lg">
                {{$transfer->status}}
            </p>

            <p class=" md:text-lg">
                {{-- date with time --}}
                {{$transfer->created_at->format('d M, Y h:i A')}}
            </p>
        </div>

        <div class=" border-t-2 border-dashed border-blue-700 my-4 lg:my-6">
        </div>

        <div class=" mt-4 space-y-2 md:text-base lg:text-lg lg:space-y-4">
            <div class=" flex justify-between items-center">
                <label for="">Recipients</label>
                <p class=" font-medium ">
                    {{count($swiftpayAccounts) > 0 ? $swiftpayAccounts[0] . " and " . count($swiftpayAccounts) - 1 . " others" : '' }}
                </p>
            </div>
            <div class=" flex justify-between items-center">
                <label for="">Transaction Type</label>
                <p class=" ">
                    <span class=" uppercase">{{$transfer->type}}</span> multiple transfer
                </p>
            </div>
            <div class=" flex justify-between items-center">
                <label for="">Sender</label>
                <p class=" ">
                    {{auth()->user()->name}}
                </p>
            </div>
            <div class=" flex justify-between items-center">
                <label for="">Reference </label>
                <p class=" ">
                    {{$transfer->reference}}
                </p>
            </div>

        </div>

    </div>


    <div class=" grid gap-y-4 text-center mt-8 print:hidden lg:grid-cols-2">
        <x-primary-button @click="print()" class=" rounded-xl">
            Print Receipt
        </x-primary-button>

        <a href="{{route('transfer.index')}}" class=" block p-3 text-lg font-medium text-primary">
            Continue
        </a>
    </div>
</div>