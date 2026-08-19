<div class=" p-6 flex flex-col items-center justify-center min-h-[90vh] gap-10 max-w-xl mx-auto">

    <img src="{{asset('SEND ABROAD 1 (1).png')}}" alt="" class=" lg:h-96">

    <div class=" text-center">
        <h2 class=" text-lg md:text-xl lg:text-2xl font-bold">Send Money Abroad</h2>

        <ul class=" space-y-2 text-left mt-4 text-sm md:text-base lg:text-lg text-gray-700 ">
            <li class=" flex items-center gap-3">
                <img src="{{asset('icons/sta1.png')}}" alt="" class=" h-10 lg:h-14">

                Send money swiftly to your friends & family Abroad.
            </li>
            <li class=" flex items-center gap-3">
                <img src="{{asset('icons/sta2.png')}}" alt="" class=" h-10 lg:h-14">

                Send to their Foreign Bank accounts or Mobile money wallet.
            </li>
            <li class=" flex items-center gap-3">
                <img src="{{asset('icons/sta3.png')}}" alt="" class=" h-10 lg:h-14">

                Pay Low international Transfer fees.
            </li>

        </ul>
    </div>

    <a href="{{route('international-transfer.index')}}" class=" block w-full">
        <x-primary-button class=" w-full">
            Send Money
        </x-primary-button>
    </a>

</div>