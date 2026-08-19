<div>
    <div class=" px-4 py-6 max-w-6xl">
        <h2 class=" text-lg font-semibold md:text-xl lg:text-2xl ">Hi, {{auth()->user()->name}} 👋</h2>
        <p class=" text-gray-600 mt-1 text-sm md:text-base lg:text-lg lg:mt-2">Don't forget to save your money!</p>

        <div class="  p-4 rounded-2xl shadow-md mt-3 lg:mt-5 lg:p-6 relative min-h-[15rem] lg:min-h-80" style="background-image: url('{{asset('savings-bg.png')}}')">
            <div class=" flex items-center justify-between ">
                <div class="">
                    <h3 class=" font-medium text-gray-600 md:text-lg">Total Savings</h3>

                    <p class=" whitespace-nowrap mt-2 lg:mt-4">
                        <span class="text-3xl lg:text-5xl font-extrabold">₦ {{formatNumber($totalSavings)}}</span>
                    </p>
                </div>

                <img src="{{asset('coins.png')}}" alt="" class=" h-56 lg:h-80 absolute right-4  top-0 z-10 ">
            </div>

            <a href="{{route('savings.interest-savings.create')}}" class=" mt-16 block w-52 lg:w-80 mx-auto relative lg:mt-24 z-20 ">
                <x-primary-button class=" w-full bg-[#1400FB]">
                    Create Savings
                </x-primary-button>
            </a>
        </div>

        <div class=" flex justify-between items-center mt-8">
            <h3 class=" text-lg md:text-xl lg:text-2xl font-semibold text-gray-700 ">
                Your Savings
            </h3>

            <a href="{{route('savings.interest-savings.history')}}" class=" text-primary font-medium text-sm md:text-base lg:text-lg">
                <button class=" px-2 ">
                    History <span class=" fa fa-chevron-right  ml-1"></span>
                </button>
            </a>
        </div>

        <div class=" mt-6 lg:mt-10 grid grid-cols-1 gap-4 lg:grid-cols-2 lg:gap-6">
            @forelse ($savings as $item)
            <x-savings-item :item="$item" :lockedSavingsInterestRate="$lockedSavingsInterestRate" :unlockedSavingsInterestRate="$unlockedSavingsInterestRate" />
            @empty
            <div class="  p-6 lg:col-span-2 ">
                <p class=" text-center">
                    You have no savings yet
                </p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<x-slot:headerTitle>
    Interest Savings
</x-slot:headerTitle>