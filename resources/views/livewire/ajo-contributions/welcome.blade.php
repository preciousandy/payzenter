<div class=" bg-white">
    <div class=" p-6 flex flex-col items-center justify-center min-h-screen bg-white max-w-xl mx-auto ">

        <img src="{{asset('ajo-contribution.png')}}" alt="" class=" h-80 lg:h-96 mx-auto">

        <h2 class=" text-xl lg:text-2xl font-semibold mt-6">
            Ajo Contributions
        </h2>

        <ul class=" space-y-2 text-sm md:text-base lg:text-lg  text-gray-700 mt-4 ">
            <li class=" flex items-center gap-1">
                <img src="{{asset('icons/ajs1.png')}}" alt="" class=" h-10 lg:h-16"> Create daily, weekly, Monthly & yearly Ajo Contribution.
            </li>
            <li class=" flex items-center gap-1">
                <img src="{{asset('icons/ajs2.png')}}" alt="" class=" h-10 lg:h-16"> Add & Manage Members to your Ajo Contribution.
            </li>
            <li class=" flex items-center gap-1">
                <img src="{{asset('icons/ajs3.png')}}" alt="" class=" h-10 lg:h-16">Create Personal or Business Ajo Contribution Account.
            </li>

        </ul>

        <a href="{{route('ajo-contributions.index')}}" class=" block  w-full mt-6">
            <x-primary-button class=" w-full">
                Start Contribution
            </x-primary-button>
        </a>

    </div>
</div>