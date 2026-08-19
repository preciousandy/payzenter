<div x-data="{showModal:true}">


    <div x-show="showModal" x-transition x-cloak class=" fixed bg-black/60 z-50 top-0 left-0 h-full w-full flex items-center justify-center py-4 px-6">
        <div class=" bg-[#E3E9FD] w-full rounded-xl max-w-lg ">
            <div class=" flex justify-end p-4 lg:p-6">
                <button @click="showModal = !showModal">
                    <span class=" fa fa-close text-xl lg:text-3xl text-red-700"></span>
                </button>
            </div>

            <div class=" flex justify-end ">
                <img src="{{asset('referral modal image.png')}}" alt="" class=" h-48 lg:h-64 ">
            </div>

            <div class=" p-4">
                <h3 class=" text-xl lg:text-2xl font-bold text-center mt-4">
                    Refer & Get paid up to <br>
                    <span class=" text-primary">
                        <x-naira-symbol />{{formatNumber(5000)}}
                    </span> in cash
                </h3>



                <a href="{{route('referrals')}}" class=" block mt-4 lg:mt-8">
                    <x-primary-button class=" rounded-full">
                        Refer Now
                    </x-primary-button>
                </a>
            </div>
        </div>
    </div>
</div>