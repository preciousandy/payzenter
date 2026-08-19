<div class=" p-6 flex flex-col items-center justify-center min-h-[80vh]  max-w-xl mx-auto ">

    <img src="{{asset('ajo savings.png')}}" alt="" class=" h-80 lg:h-96 mx-auto">

    <h2 class=" text-xl lg:text-2xl font-semibold mt-6">
        Ajo Savings
    </h2>

    <ul class=" space-y-2 text-sm md:text-base lg:text-lg  text-gray-700 mt-4 ">
        <li class=" flex items-center gap-1">
            <img src="{{asset('icons/ajs1.png')}}" alt="" class=" h-14 lg:h-20"> Create daily, weekly, Monthly & yearly Ajo savings
        </li>
        <li class=" flex items-center gap-1">
            <img src="{{asset('icons/ajs2.png')}}" alt="" class=" h-14 lg:h-20"> Manage and keep track of your Ajo savings
        </li>
        <li class=" flex items-center gap-1">
            <img src="{{asset('icons/ajs3.png')}}" alt="" class=" h-14 lg:h-20">Make swift Withdraws and also create multiple Ajo Savings
        </li>
    </ul>

    <a href="{{route('ajo-savings.index')}}" class=" block  w-full mt-6">
        <x-primary-button class=" w-full">
            Start Ajo Savings
        </x-primary-button>
    </a>

</div>