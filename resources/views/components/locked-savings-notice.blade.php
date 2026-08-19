<div x-data="{showModal: true}">
    <div x-show="showModal" x-cloak x-transition class=" fixed bg-black/60 top-0 left-0 h-full w-full z-30 flex justify-center items-center p-4">

        <div class=" bg-white p-4 rounded-2xl w-full max-w-lg lg:p-6">
            <div class=" flex justify-end">
                <button>
                    <span class="fa fa-close text-primary text-2xl lg:text-3xl"></span>
                </button>
            </div>
            <img src="{{asset('locked savings notice.png')}}" alt="" class=" h-32 mx-auto mt-4 lg:h-44">

            <p class=" mt-4 md:mt-6 text-gray-600 text-center text-sm md:text-base lg:text-lg leading-6">
                Please note that once Locked, User will no longer be able to make withdrawals, till the date set elapse.
            </p>

            <x-primary-button class=" w-full mt-4 md:mt-6" @click="showModal = !showModal">
                Acknowledge
            </x-primary-button>
        </div>

    </div>
</div>