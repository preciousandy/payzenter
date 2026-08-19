<div class=" px-4 py-8 flex flex-col justify-center items-center gap-6  min-h-screen  md:max-w-lg md:mx-auto  ">

    <img src="{{asset('new savings.png')}}" alt="" class=" mx-auto lg:h-96">

    <h2 class=" text-xl md:text-2xl lg:text-3xl font-semibold  text-center ">
        Save With Interest
    </h2>



    <ul class=" space-y-3  lg:space-y-5 text-sm md:text-base lg:text-xl text-gray-700">
        <li class=" flex items-center gap-3">
            <span class=" fa fa-shield text-xl"></span>

            Safe and Secured way to save money
        </li>
        <li class=" flex items-center gap-3">
            <span class=" fa fa-lock text-xl"></span>

            Lock your savings whenever you want
        </li>
        <li class=" flex items-center gap-3">
            <span class=" fa fa-credit-card text-xl"></span>

            Get high interest on your savings today!
        </li>
    </ul>

    <a href="{{route('savings.interest-savings')}}" class=" block mx-auto w-full mt-8">
        <x-primary-button class=" px-10 w-full rounded-xl">
            Next
        </x-primary-button>
    </a>

</div>