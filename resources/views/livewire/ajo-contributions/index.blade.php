<div class=" px-4 py-6 max-w-5xl lg:p-6">

    {{-- card view --}}
    <div class=" bg-[#1400FB] p-4 text-white rounded-xl relative lg:p-6">
        <img src="{{asset('ajocontri.png')}}" alt="" class=" absolute z-0 right-4 top-4 lg:top-6 lg:right-6">

        <div class=" relative z-10">
            <div class=" bg-[#17A1FA69] w-fit px-3 py-2 text-xs md:text-sm lg:text-base rounded-lg">
                10% per annum
            </div>

            <div class="  mt-8 gap-4 lg:mt-10">
                <div class=" space-y-2 lg:space-y-4">
                    <h4 class=" font-medium lg:text-lg">
                        Ajo Contribution Balance
                    </h4>

                    <div class=" text-3xl lg:text-4xl font-bold">
                        <x-naira-symbol /> {{ formatNumber(auth()->user()->ajo_contribution_wallet)}}
                    </div>
                </div>
            </div>

            <a href="{{route('ajo-contributions.create')}}" class=" block w-fit bg-[#17A1FAC7] py-3 font-medium text-sm px-10 rounded-lg mx-auto lg:text-base lg:w-80 text-center lg:py-4 mt-4 lg:mt-6">
                Create Ajo Contribution
            </a>
        </div>
    </div>



    <div class=" mt-6 lg:mt-8 flex justify-between items-center gap-2">
        <a href="{{route('ajo-contributions.join')}}" class="block  w-full">
            <span class="fa fa-plus  h-12 w-12 md:h-16 md:w-16 lg:h-20 lg:w-20  bg-blue-200 flex justify-center items-center text-2xl lg:text-3xl font-semibold rounded-xl mx-auto">

            </span>

            <p class=" text-sm md:text-base lg:text-lg lg:mt-2 font-medium mt-1 text-gray-700 text-center">
                Join Contribution
            </p>
        </a>

        {{-- withdraw modal --}}
        <div x-data="{showWithdrawModal: false}">
            <button @click="showWithdrawModal = !showWithdrawModal" class="block  w-full">
                <span class="fa fa-bank  h-12 w-12 md:h-16 md:w-16 lg:h-20 lg:w-20 bg-blue-200 flex justify-center items-center text-2xl lg:text-3xl font-semibold rounded-xl mx-auto">

                </span>

                <p class=" text-center text-sm md:text-base lg:text-lg  font-medium mt-1 text-gray-700">
                    Withdraw
                </p>
            </button>

            <div x-show="showWithdrawModal" x-transition x-cloak class=" fixed bg-black/70 w-screen h-screen z-50 left-0 top-0 flex justify-center items-center p-4">

                <div class=" bg-white px-4 py-6 rounded-md w-full lg:p-6 max-w-xl">

                    <div class=" flex justify-between items-center">
                        <h3 class=" text-lg lg:text-xl font-semibold">
                            Withdraw money to wallet
                        </h3>
                        <button @click="showWithdrawModal = !showWithdrawModal" class=" text-primary font-semibold text-2xl lg:text-3xl">
                            <span class=" fa fa-close"></span>
                        </button>
                    </div>

                    <form wire:submit.prevent='withDraw' class=" mt-6 lg:mt-8 space-y-6">

                        <div>
                            <x-input-label for="amount" :value="__('Amount')" />

                            <x-text-input id="amount" class="block mt-1 w-full placeholder:text-gray-400" type="number" wire:model="amount" required autofocus />
                            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                        </div>
                        <x-enter-pin id="withdraw" />

                        <x-primary-button class=" w-full">
                            Withdraw Money
                        </x-primary-button>
                    </form>

                </div>

            </div>
        </div>


        <a href="{{route('ajo-contributions.history')}}" class="block  w-full">
            <span class="fa fa-history  h-12 w-12 md:h-16 md:w-16 lg:h-20 lg:w-20 bg-blue-200 flex justify-center items-center text-2xl lg:text-3xl font-semibold rounded-xl mx-auto">
            </span>

            <p class=" text-center text-sm md:text-base lg:text-lg font-medium mt-1 text-gray-700">
                History
            </p>
        </a>
    </div>

    {{-- Ongoing Ajo savings --}}
    <section class=" mt-6 lg:mt-8">
        <h2 class="  font-bold  text-lg lg:text-xl w-fit">
            Ongoing Contributions
        </h2>

        <div class=" mt-2 divide-y-2  ">
            @forelse ($contributions as $contribution)
            <a href="{{route('ajo-contributions.show', $contribution->hash_id)}}" class=" block py-4 lg:py-6">
                <div class=" flex items-center gap-x-3 lg:gap-4 ">
                    <div class=" bg-blue-100 h-16 w-16 md:h-20 md:w-20 rounded-md flex items-center justify-center flex-shrink-0 relative">
                        <span class="fa fa-piggy-bank text-[#1400FB]  text-3xl lg:text-4xl"></span>
                        <span class="fa fa-square-check text-[#1400FB]  text-xl absolute -bottom-1 right-0 z-10  "></span>
                    </div>

                    <div class=" space-y-2 w-full">
                        <div class=" flex items-start justify-between gap-2 text-sm">
                            <h3 class=" text-base lg:text-xl font-semibold">
                                {{$contribution->name}}
                            </h3>

                            <p class=" text-green-500 font-semibold whitespace-nowrap text-lg lg:text-xl">

                                <x-naira-symbol /> {{ formatNumber($contribution->amount)}}
                            </p>
                        </div>
                        <div class=" flex items-center justify-between text-sm text-gray-700 md:text-base">
                            <p class=" font-medium ">
                                {{ucfirst($contribution->frequency)}} Savings deposit
                            </p>

                            <p class=" whitespace-nowrap">
                                <x-naira-symbol /> {{formatNumber($contribution->amount * $contribution->no_of_members)}}
                            </p>
                        </div>
                    </div>
                </div>
            </a>
            @empty

            <div class=" text-lg p-8 w-full">
                <p class=" text-center">
                    No ongoing Ajo Contributions
                </p>
            </div>

            @endforelse



        </div>
    </section>
</div>