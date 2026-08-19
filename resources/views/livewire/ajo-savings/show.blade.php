<div class=" px-4 py-6 lg:p-8 max-w-4xl">

    <x-loading-spinner wire:target='endSavings' />


    <div class=" flex items-center gap-3 lg:gap-4">
        <div class=" bg-[#1400FB] h-20 w-20 lg:h-24 lg:w-24 rounded-xl flex items-center justify-center flex-shrink-0 ">
            <img src="{{asset('ajo-savings-icon.png')}}" alt="" class="h-10 lg:h-14">
        </div>

        <div class=" space-y-2 w-full lg:space-y-4">
            <h3 class=" text-lg md:text-xl lg:text-2xl font-semibold">
                {{$savings->type}} Ajo Savings
            </h3>

            <div class=" flex items-center justify-between text-sm">
                <p class=" text-xl font-medium">
                    <x-naira-symbol /> {{ formatNumber($savings->balance)}}
                </p>



                <div x-data="{showCloseModal: false}">

                    @if ($savings->pay_back_amount == 0 && $savings->status == 'active')
                    <x-primary-button class=" px-6 text-xs md:text-sm lg:text-base lg:w-28 py-2" @click="showCloseModal = !showCloseModal">
                        End
                    </x-primary-button>
                    @endif



                    <div x-show="showCloseModal" x-transition x-cloak class=" fixed bg-black/70 w-screen h-screen z-50 left-0 top-0 flex justify-center items-center p-4">

                        <div class=" bg-white p-4 rounded-md w-full max-w-lg lg:p-6">

                            <div class=" flex justify-between items-center mb-4 lg:mb-6">
                                <h3 class=" text-lg lg:text-xl font-semibold">
                                    End Ajo Savings
                                </h3>
                                <button @click="showCloseModal = !showCloseModal" class="  text-gray-600 font-semibold text-2xl">
                                    <span class=" fa fa-close"></span>
                                </button>
                            </div>

                            @if ($savings->pay_back_amount < 0) <p class="  text-center lg:text-lg">
                                You still
                                <x-naira-symbol /> {{ formatNumber($savings->pay_back_amount)}} outstanding payment, which means you can't end this savings yet.
                                </p>

                                <div class=" mt-4 w-full text-center">
                                    <x-primary-button class=" px-6 text-xs ">
                                        Payback
                                    </x-primary-button>
                                </div>

                                @else

                                <p class=" text-center text-sm md:text-base lg:text-lg text-gray-700">
                                    <span class=" fa fa-info mr-2"></span> Closing the Ajo savings before the end date will attract a {{$early_close_fee}}% fee on the total amount saved.
                                </p>

                                <div class=" mt-6 w-full flex justify-between gap-4 items-center lg:mt-8">
                                    <x-secondary-button class=" py-3  text-xs  px-6 md:text-sm lg:text-base" @click="showCloseModal = !showCloseModal">
                                        Cancel
                                    </x-secondary-button>

                                    <x-primary-button class="  text-xs md:text-sm lg:text-base" wire:click='endSavings'>
                                        Yes, End Savings
                                    </x-primary-button>
                                </div>

                                @endif

                        </div>

                    </div>
                </div>
            </div>

            {{-- <p class=" text-sm">
                Locked <span class=" fa fa-padlock"></span>
            </p> --}}

            @if ($savings->status == 'closed')
            <p class=" text-sm whitespace-nowrap">
                Closed {{$savings->end_date->diffForHumans()}}
            </p>

            @else

            <div class=" flex items-center gap-x-4">
                <div class=" h-2  w-full">
                    <div class=" h-full bg-blue-600 rounded-xl  w-full">

                    </div>
                </div>

                <p class=" text-sm whitespace-nowrap">
                    {{$savings->days_left}} days left
                </p>
            </div>
            @endif


        </div>
    </div>

    <div class=" bg-white lg:shadow-sm p-3 lg:p-5 rounded-lg mt-6 flex justify-between items-center">
        <div class=" text-sm md:text-base">
            Payback Amount:
            <span class=" text-xl lg:text-2xl font-semibold ml-2">
                <x-naira-symbol /> {{ formatNumber($savings->pay_back_amount)}}
            </span>
        </div>

        <div x-data="{showModal: false}">
            @if ($savings->pay_back_amount > 0 && $savings->status == 'active')
            <x-secondary-button class=" px-2 text-xs py-2 flex items-center gap-1  rounded-md" @click="showModal = !showModal">
                <x-lucide-banknote class=" h-5" /> Payback
            </x-secondary-button>
            @endif



            <div x-show="showModal" x-transition x-cloak class=" fixed bg-black/70 w-screen h-screen z-50 left-0 top-0 flex justify-center items-center p-4">

                <div class=" bg-white p-4 rounded-md w-full lg:p-6 max-w-lg">

                    <div class=" flex justify-between items-center">
                        <h3 class=" text-lg lg:text-xl font-semibold">
                            Payback Ajo Savings
                        </h3>
                        <button @click="showModal = !showModal" class="  text-gray-600 font-semibold text-2xl">
                            <span class=" fa fa-close"></span>
                        </button>
                    </div>

                    <p class=" text-center text-sm md:text-base lg:text-lg text-gray-700 mt-4 lg:mt-6">
                        You need to pay back the total outstanding amount before you can end this savings.
                    </p>

                    <div class=" text-center mt-2 lg:mt-4">
                        <p class=" lg:text-lg">
                            Total Payback Amount
                        </p>
                        <p class=" text-2xl lg:text-3xl mt-1 lg:mt-2 font-bold ">
                            <x-naira-symbol /> {{formatNumber($savings->pay_back_amount)}}
                        </p>
                    </div>

                    <x-enter-pin id="ajo-savings" />

                    <div class=" mt-6 lg:mt-8 w-full flex justify-between gap-4 items-center">
                        <x-secondary-button class=" py-3  text-xs  px-6 md:text-sm lg:text-base" @click="showModal = !showModal">
                            Cancel
                        </x-secondary-button>

                        <x-primary-button class="  text-xs md:text-sm lg:text-base" wire:click='payback'>
                            Pay Now
                        </x-primary-button>
                    </div>


                </div>

            </div>
        </div>
    </div>


    <a href="{{route('ajo-savings.transactions', $savings->hash_id)}}" class=" text-sm inline-block p-2 md:text-base  font-semibold text-primary mt-2">
        View Transactions <span class="fa fa-chevron-right ml-1"></span>
    </a>



    <x-display-chart :chartLabels="$labels" :chartData="$data" title="Amount Saved" />
</div>

{{-- @push('scripts')

<script type="module">

</script>

@endpush --}}