<div class="bg-white">
    <div class=" px-4 py-6  min-h-screen max-w-6xl lg:p-8">

        <div class=" grid gap-6 lg:grid-cols-5 lg:items-start">
            {{-- card view --}}
            <div class=" bg-[#1400FB] p-4 text-white rounded-xl overflow-hidden lg:p-6 lg:col-span-3">
                <div class=" bg-[#17A1FA69] w-fit p-3 text-xs md:text-sm lg:text-base rounded-lg">
                    6 - 135% per annum
                </div>

                <div class=" flex items-center justify-between mt-8 lg:mt-10 gap-4">
                    <div class=" space-y-3 lg:space-y-5">
                        <h4 class=" font-medium lg:text-lg lg:font-medium">
                            Ajo Savings Balance
                        </h4>

                        <div class=" text-3xl lg:text-4xl font-bold">
                            <x-naira-symbol /> {{ formatNumber($ajoSavingsBalance)}}
                        </div>

                        <a href="{{route('ajo-savings.create')}}" class=" block bg-[#17A1FAC7] py-3 lg:py-4 font-medium text-sm md:text-base px-10 rounded-md whitespace-nowrap lg:w-60 text-center">
                            Create Ajo Savings
                        </a>
                    </div>
                    <img src="{{asset('ajo-icon.png')}}" alt="">
                </div>
            </div>

            {{-- referral --}}
            <div class="  bg-gray-100 p-4 rounded-lg lg:col-span-2 lg:p-6 lg:shadow-sm" x-data>
                <div class=" flex items-center justify-between">
                    <h3 class=" font-medium lg:text-lg">Ajo Referral Code</h3>

                    <a href="{{route('referrals')}}" class="inline-block px-2 ">
                        <span class="fa fa-chevron-right"></span>
                    </a>
                </div>
                <x-text-input wire:model='referralCode' disabled class=" w-full bg-white p-3 mt-2" />

                <x-secondary-button onclick="copyToClipboard('{{$referralCode}}')" class=" mt-4  w-full rounded-lg py-3 ">
                    <span class=" fa fa-clipboard"></span> <span id="copy">Copy Referral Code</span>
                </x-secondary-button>
            </div>
        </div>

        {{-- history section --}}
        <section class=" mt-6 lg:mt-8">
            <div class=" flex justify-between items-center">
                <h3 class=" text-lg lg:text-xl font-semibold">
                    History
                </h3>

                <a href="{{route('ajo-savings.history')}}" class=" font-medium p-3 text-sm md:text-base text-primary">
                    See all <span class="fa fa-chevron-right ml-2"></span>
                </a>
            </div>

            <div class=" mt-4 clear-left whitespace-nowrap overflow-auto space-x-6 py-3 lg:py-5">

                {{-- Savings history --}}
                @forelse ($savingsHistory as $history)
                <div class=" bg-purple-50 p-4 rounded-xl inline-block w-1/2 md:w-80 lg:p-6  lg:shadow-sm">
                    <div class=" flex justify-end">
                        <p class=" bg-blue-300 p-2 rounded-lg text-xs md:text-sm  w-fit">
                            earned
                            <span class=" font-medium">
                                <x-naira-symbol /> {{ formatNumber($history->amount_earned)}}
                            </span>
                        </p>
                    </div>

                    <h3 class=" text-primary font-semibold text-center mt-4 lg:text-lg">
                        {{$history->type}} Ajo Savings
                    </h3>

                    <p class=" text-center text-sm md:text-base mt-2">
                        for {{$history->duration}} days
                    </p>
                </div>

                @empty

                <div class=" text-base text-gray-700 p-8 w-full">
                    <p class=" text-center">
                        No Ajo savings history
                    </p>
                </div>

                @endforelse


            </div>
        </section>

        {{-- Ongoing Ajo savings --}}
        <section class=" mt-12">
            <h2 class=" mx-auto font-bold p-2 border-b-4 border-blue-800 w-fit lg:text-xl">
                Ongoing
            </h2>

            <div class=" mt-8  grid gap-6 lg:grid-cols-2">
                @forelse ($savings as $saving)
                <a href="{{route('ajo-savings.show', $saving->hash_id)}}" class=" block  border-gray-200 p-4 rounded-xl border lg:p-6">
                    <div class=" flex items-center gap-3 lg:gap-4 ">
                        <div class=" bg-[#1400FB] h-20 w-20 rounded-xl flex items-center justify-center flex-shrink-0 p-3">
                            <img src="{{asset('ajo-savings-icon.png')}}" alt="" class=" h-10  ">
                        </div>

                        <div class=" space-y-2 w-full lg:space-y-3">
                            <h3 class="  font-semibold lg:text-lg">
                                {{$saving->type}} Ajo Savings
                            </h3>

                            <div class=" flex items-start justify-between text-sm md:text-base">
                                <p class=" font-medium">
                                    <x-naira-symbol /> {{ formatNumber($saving->balance)}}
                                </p>

                                @if ($saving->payback_amount > 0)
                                <p class=" bg-gray-200 px-2 py-1 rounded-xl text-xs md:text-sm lg:text-base">
                                    Payback
                                    <x-naira-symbol /> {{ formatNumber($saving->payback_amount)}}
                                </p>
                                @endif

                            </div>

                            <p class=" text-sm md:text-base lg:text-base">
                                Locked <span class=" fa fa-lock ml-1 text-xs"></span>
                            </p>

                            <div class=" flex items-center">
                                <div class=" h-2  w-full">
                                    <div class=" h-full bg-blue-600 rounded-xl w-4/5">

                                    </div>
                                </div>

                                <p class=" text-sm whitespace-nowrap">
                                    {{$saving->days_left}} days left
                                </p>
                            </div>
                        </div>
                    </div>
                </a>
                @empty

                <div class=" text-lg p-8 w-full text-gray-700">
                    <p class=" text-center">
                        You don't have any ongoing Ajo savings
                    </p>
                </div>

                @endforelse



            </div>
        </section>
    </div>
</div>