<div class=" px-4 py-6">

    <x-blank-page-header title="Card Order" />

    <div class=" relative mt-4 ">
        <img src="{{asset('card-demo.png')}}" alt="" class=" w-full absolute top-0 left-0 h-full">
        <div class=" relative z-10 text-white p-3 space-y-4">
            <img src="{{asset('logo-white.png')}}" alt="" class=" h-7">

            <div class=" flex justify-between items-center">
                <img src="{{asset('chip.png')}}" alt="" class=" h-10">
                <img src="{{asset('Wireless.png')}}" alt="" class=" h-10">
            </div>
            <div class=" flex justify-between items-center">
                <p class=" font-semibold">XXXX XXXX XXXX XXXX </p>
                <img src="{{asset('card.png')}}" alt="" class=" h-10">
            </div>
            <div class=" flex justify-between items-center">
                <p class=" font-semibold uppercase">{{auth()->user()->name}} </p>
                <div class=" flex items-center text-xs uppercase gap-2">
                    <p class="">
                        Valid <br>
                        Thru
                    </p>
                    <p class=" font-bold">
                        08/29
                    </p>
                </div>
            </div>


        </div>
    </div>

    <div class=" space-y-4 mt-6">
        <div class=" flex items-center  gap-3">
            <span class="fa fa-bolt  text-primary text-xl"></span>
            <div>
                <h3 class="  font-semibold text-gray-700">
                    Instant Access
                </h3>

                <p class=" text-sm text-gray-600 mt-1">
                    Instant activation once card is issued.
                </p>
            </div>
        </div>
        <div class=" flex items-center  gap-3">
            <span class="fa fa-shield  text-primary text-xl"></span>
            <div>
                <h3 class="  font-semibold text-gray-700">
                    Easy Online purchase
                </h3>

                <p class=" text-sm text-gray-600 mt-1">
                    Accepted by both local and international merchants.
                </p>
            </div>
        </div>
        <div class=" flex items-center  gap-3">
            <span class="fa fa-lock  text-primary text-xl"></span>
            <div>
                <h3 class="  font-semibold text-gray-700">
                    Security
                </h3>

                <p class=" text-sm text-gray-600 mt-1">
                    <span class=" font-medium text-primary">CBN</span> licensed, <span class=" font-medium text-primary">NDIC</span> Insured
                </p>
            </div>
        </div>



        <div class=" absolute bottom-5 left-5 right-5">
            <div>
                <label for="terms" class="inline-flex items-center">
                    <input id="terms" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-primary shadow-sm focus:ring-primary dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" required>
                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Accept Terms & Conditions') }}</span>
                </label>
            </div>
            <a href="{{route('coming-soon')}}" class=" mt-2 block">
                <x-primary-button class="">
                    Get Now
                </x-primary-button>
            </a>
        </div>
    </div>
</div>
</div>
