<div x-data="{showModal:true}">


    <div x-show="showModal" x-transition x-cloak class=" fixed bg-black/60 z-50 top-0 left-0 h-full w-full flex items-center justify-center py-4 px-6">
        <div class=" bg-[#E3E9FD] w-full rounded-xl p-4 lg:p-6 max-w-lg">
            <div class=" flex justify-end">
                <button @click="showModal = !showModal">
                    <span class=" fa fa-close text-xl lg:text-3xl text-red-700"></span>
                </button>
            </div>

            <img src="{{asset('savings -modal image.png')}}" alt="" class=" h-36 mx-auto lg:h-44">

            <h3 class=" text-xl lg:text-2xl font-bold text-center mt-4">
                Personal Savings
            </h3>

            <p class=" text-sm text-gray-600 md:text-base lg:text-lg mt-2 text-center">
                Save with interest and get up to 20% increase annually.
            </p>

            <a href="{{route('savings.interest-savings.welcome')}}" class=" block mt-4 lg:mt-8">
                <x-primary-button class=" rounded-full">
                    Save Now
                </x-primary-button>
            </a>
        </div>
    </div>
</div>