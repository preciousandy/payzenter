<div class="px-4 py-8 flex flex-col justify-center items-center gap-y-6 lg:gap-y-8  min-h-[80vh] max-w-xl mx-auto">

    <img src="{{asset('investments.png')}}" alt="" class=" mx-auto lg:h-96">

    <h2 class=" text-xl md:text-2xl lg:text-3xl lg:font-bold font-semibold text-blue-600 uppercase text-center ">
        Invest & Earn
    </h2>



    <ul class=" space-y-4 lg:space-y-6 text-sm md:text-base lg:text-lg text-gray-800">
        <li class=" flex items-center gap-3">
            <img src="{{asset('icons/inv-1.png')}}" alt="" class=" h-8">

            Invest In real time global assets.
        </li>
        <li class=" flex items-center gap-3">
            <img src="{{asset('icons/inv-2.png')}}" alt="" class=" h-8">

            Watch your money grow according to the percentage increase on each asset.
        </li>
        <li class=" flex items-center gap-3">
            <img src="{{asset('icons/inv-3.png')}}" alt="" class=" h-8">

            Create multiple investments and choose your preferred time duration.
        </li>

    </ul>

    <a href="{{route('investments.portfolio')}}" class=" block mx-auto w-full mt-8">
        <x-primary-button class=" px-10 w-full rounded-xl">
            Invest Now
        </x-primary-button>
    </a>

</div>