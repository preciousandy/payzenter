<div class=" px-4 py-8 flex flex-col justify-center items-center gap-6  min-h-screen md:max-w-lg md:mx-auto lg:gap-8">

    <img src="{{asset('group-savings-welcome.png')}}" alt="" class=" mx-auto h-80 lg:h-96">

    <h2 class="text-xl md:text-2xl lg:text-3xl font-semibold  text-center ">
        Group Savings
    </h2>


    <ul class=" space-y-3 lg:space-y-5 text-sm md:text-base lg:text-xl text-gray-700 lg:font-medium">
        <li class=" flex items-center gap-3">
            <span class=" fa fa-shield text-xl"></span>

            Safe and Secured way to save money
        </li>
        <li class=" flex items-center gap-3">
            <span class=" fa fa-wallet text-xl"></span>

            Create a shared Savings wallet for any purpose in mind.
        </li>
        <li class=" flex items-center gap-3">
            <span class=" fa fa-users text-xl"></span>

            Add members and view saving activities.
        </li>
    </ul>

    <a href="{{route('savings.group-savings')}}" class=" block mx-auto w-full ">
        <x-primary-button class=" px-10 w-full rounded-xl">
            Next
        </x-primary-button>
    </a>

</div>