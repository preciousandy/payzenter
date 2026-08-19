<div class=" px-4 py-8 flex flex-col justify-center items-center gap-y-6 lg:gap-y-8  min-h-[80vh] max-w-xl mx-auto">

    <img src="{{asset('holdings.png')}}" alt="" class=" mx-auto lg:h-96">

    <h2 class=" text-xl md:text-2xl lg:text-3xl lg:font-bold font-semibold text-blue-600 uppercase text-center ">
        Holdings
    </h2>



    <ul class=" space-y-4 lg:space-y-6 text-sm md:text-base lg:text-lg text-gray-700 ">
        <li class=" flex items-center gap-3">
            <img src="{{asset('icons/3dicons.png')}}" alt="">

            Save your money in hard currency or Gold
        </li>
        <li class=" flex items-center gap-3">
            <img src="{{asset('icons/3dicons(1).png')}}" alt="">

            Earn more in your local currency once the rate of the hard currency you saved your money in goes higher
        </li>
        <li class=" flex items-center gap-3">
            <img src="{{asset('icons/3dicons(2).png')}}" alt="">

            Money saved would be sent back to your SwiftPay wallet, at that day's rate. If the rate is higher than when you saved, the user would earn more money!
        </li>
    </ul>

    <a href="{{route('holdings.index')}}" class=" block mx-auto w-full mt-6">
        <x-primary-button class=" w-full rounded-xl">
            Save Now
        </x-primary-button>
    </a>

</div>