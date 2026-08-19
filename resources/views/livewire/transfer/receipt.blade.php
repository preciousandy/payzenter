<div class=" p-4 py-6 max-w-2xl lg:mx-auto w-full" @if($transfer->status === 'pending') wire:poll.5s="checkAndRefreshStatus" @endif>
    <div class=" bg-blue-50 p-4 lg:p-6 border border-blue-400 rounded-t-xl text-sm">

        <div class=" flex justify-between items-center">
            <img src="{{asset('logo-welcome.png')}}" alt="" class=" h-10 lg:h-14">

            <p class=" md:text-lg">
                Transaction Receipt
            </p>
        </div>

        <div class=" mt-6 text-center space-y-2 lg:space-y-3">
            <p class=" text-3xl lg:text-4xl font-bold text-green-700">
                <x-naira-symbol /> {{formatNumber($transfer->amount)}}
            </p>

            <p class=" md:text-lg">
                {{$transfer->status}}
            </p>

            @if($transfer->status === 'failed' && $transfer->getFailureReason())
                <div class="mt-2 text-sm text-red-600 bg-red-50 p-2 px-3 rounded-lg border border-red-200 inline-block">
                    <span class="fa fa-info-circle mr-1"></span> Reason: {{ $transfer->getFailureReason() }}
                </div>
            @endif

            <p class=" md:text-lg">
                {{-- date with time --}}
                {{$transfer->created_at->format('d M, Y h:i A')}}
            </p>
        </div>

        <div class=" border-t-2 border-dashed border-blue-700 my-4 lg:my-6">
        </div>

        <div class=" mt-4 space-y-2 md:text-base lg:text-lg lg:space-y-4 ">
            <div class=" flex justify-between items-center">
                <label for="">Transaction Type</label>
                <p class=" text-lg font-semibold text-green-600">
                    {{$transfer->type}} transfer
                </p>
            </div>
            <div class=" flex justify-between items-center">
                <label for="">Account Name</label>
                <p class=" ">
                    {{$transfer->account_name}}
                </p>
            </div>
            <div class=" flex justify-between items-center">
                <label for="">Account Number</label>
                <p class=" ">
                    {{$transfer->account_number}}
                </p>
            </div>
            <div class=" flex justify-between items-center">
                <label for="">Transfer Fee</label>
                <p class=" ">
                    {{formatNumber($transfer->fee)}} NGN
                </p>
            </div>
            <div class=" flex justify-between items-center">
                <label for="">Remarks</label>
                <p class=" ">
                    {{$transfer->description}}
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
            <span class="fa fa-print mr-2"></span> Print Receipt
        </x-primary-button>

        <a href="{{route('live-chat-welcome')}}" class=" block p-3  font-medium text-red-700">
            <i class="fa-solid fa-circle-exclamation mr-2"></i> Report Issue
        </a>

    </div>
</div>