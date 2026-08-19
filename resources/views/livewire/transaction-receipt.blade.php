<div class=" p-4 py-6 max-w-2xl mx-auto">
    <div class=" bg-blue-50 p-4 border border-blue-400 rounded-t-xl lg:p-6 ">

        <div class=" flex justify-between items-center">
            <img src="{{asset('logo-welcome.png')}}" alt="" class=" h-10 lg:h-16">

            <p class=" text-sm md:text-base lg:text-lg">
                Transaction Receipt
            </p>
        </div>

        <div class=" mt-6 text-center space-y-2 lg:space-y-4 lg:mt-8">
            <p class=" text-3xl lg:text-4xl font-bold text-green-700">
                <x-naira-symbol /> {{formatNumber($transaction->amount)}}
            </p>

            <p>
                {{$transaction->status}}
            </p>

            <p>
                {{-- date with time --}}
                {{$transaction->created_at->format('d M, Y h:i A')}}
            </p>
        </div>

        <div class=" border-t-2 border-dashed border-blue-700 my-4 lg:my-6">
        </div>

        <div class=" mt-4 space-y-2 text-sm md:text-base lg:space-y-4">
            <div class=" grid grid-cols-2 gap-2">
                <label for="">Transaction Type</label>
                <p class="{{$transaction->type == 'credit' ? 'text-green-700' : 'text-red-700'}} font-medium text-right">
                    {{$transaction->type}}
                </p>
            </div>
            <div class=" grid grid-cols-2 gap-2">
                <label for="">Source</label>
                <p class=" text-primary font-medium text-right">
                    {{ucfirst(str_replace('_', ' ', $transaction->source))}}
                </p>
            </div>

            <div class=" grid grid-cols-2 gap-2">
                <label for="">Description</label>
                <p class=" text-right text-sm ">
                    {{$transaction->message}}
                </p>
            </div>


            <div class=" grid grid-cols-2 gap-2">
                <label for="">Reference </label>
                <p class=" text-right">
                    {{$transaction->reference ?? '-'}}
                </p>
            </div>

        </div>

    </div>


    <div class=" grid gap-3 text-center mt-8 print:hidden lg:grid-cols-2 lg:items-center">
        <x-primary-button @click="print()" class="rounded-xl">
            <span class="fa fa-print mr-2"></span> Print Receipt
        </x-primary-button>

        <a href="{{route('live-chat-welcome')}}" class=" block p-3  font-medium text-red-700">
            <i class="fa-solid fa-circle-exclamation mr-2"></i> Report Issue
        </a>
        {{-- <a href="{{route('transfer.index')}}" class=" block p-3 font-medium text-primary">
        Continue
        </a> --}}
    </div>
</div>