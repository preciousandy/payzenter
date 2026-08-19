<div>
    {{-- hero section --}}
    <div class=" ">
        <header class=" px-4 py-6 max-w-7xl mx-auto md:py-12 lg:py-24  ">
            <div class="" data-taos-offset="400">
                <div class=" text-2xl md:text-3xl lg:text-4xl xl:text-5xl lg:font-semibold font-bold  leading-[2rem] xl:leading-[4.5rem] max-w-5xl mx-auto text-center lg:max-w-3xl">
                    Grow your business with <span class="text-primary">{{ config('app.name') }}</span>
                    <div class="typewriter text-primary h-12 md:h-20 ">
                        <span id="changingText"></span>
                        <span class="cursor"></span>

                    </div>
                </div>

                <p class=" mt-4  text-gray-600 md:text-lg lg:text-xl  text-center lg:max-w-2xl lg:mx-auto">
                    Accept payments via card, bank transfer, and USSD. Generate virtual accounts, track transactions, manage webhooks, and withdraw to your verified settlement account.
                </p>

                <div class=" mt-6 lg:max-w-xl lg:mx-auto grid grid-cols-1 lg:grid-cols-2 gap-4 ">
                    <a href="{{route('register')}}" class=" block bg-primary text-white text-center p-3 font-medium text-sm rounded-full lg:text-base">
                        Create Merchant Account
                    </a>
                    <a href="{{route('login')}}" class=" block bg-white border border-gray-200 text-gray-900 text-center p-3 font-semibold text-sm rounded-full lg:text-base hover:bg-gray-50">
                        Login
                    </a>

                </div>
            </div>

            <div class=" relative h-60 md:h-fit mt-6 md:mt-10 lg:mt-20 w-full max-w-7xl mx-auto">
                <img src="{{asset('new hero.png')}}" alt="" class="mx-auto md:h-auto w-full max-w-2xl" data-taos-offset="400">

                {{-- botton opacity --}}
                <div class=" bg-gradient-to-b from-transparent to-white z-10  absolute bottom-0 left-0  h-28 w-full md:h-40 lg:h-4/5"></div>

                <div class="bg-gradient-to-r from-transparent to-white absolute top-0 right-0 h-full  w-16 lg:w-60  z-10">

                </div>

                <div class="bg-gradient-to-l from-transparent to-white absolute top-0 left-0-0 h-full  w-16 lg:w-60  z-10">

                </div>
            </div>

        </header>
    </div>


    {{-- features section --}}
    <section class=" mt-20 px-4 py-6 max-w-7xl mx-auto ">
        <div class="" data-taos-offset="400">
            <h3 class=" text-lg md:text-2xl lg:text-4xl lg:leading-[4rem] font-semibold text-center">
                Everything you need to run payments
            </h3>

            <p class=" mt-2 text-sm text-gray-600 md:text-lg lg:text-xl text-center max-w-3xl mx-auto">
                Built for merchants in Nigeria: collections, virtual accounts, webhooks, developer API access, and fast settlements.
            </p>
        </div>

        <div class="mt-10 lg:mt-16 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <img src="{{ asset('icons/convert-card.png') }}" alt="" class="h-10 w-10">
                <h4 class="mt-4 text-lg font-bold text-gray-900">Accept Payments</h4>
                <p class="mt-2 text-sm text-gray-600">Collect payments via card, bank transfer, and USSD with a single integration.</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <img src="{{ asset('icons/bank.png') }}" alt="" class="h-10 w-10">
                <h4 class="mt-4 text-lg font-bold text-gray-900">Virtual Accounts</h4>
                <p class="mt-2 text-sm text-gray-600">Generate dedicated virtual accounts for collections and reconcile payments faster.</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <img src="{{ asset('icons/Database.png') }}" alt="" class="h-10 w-10">
                <h4 class="mt-4 text-lg font-bold text-gray-900">Transaction Dashboard</h4>
                <p class="mt-2 text-sm text-gray-600">View and monitor all payment activity, statuses, and customer references.</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <img src="{{ asset('icons/GlobeSimple.png') }}" alt="" class="h-10 w-10">
                <h4 class="mt-4 text-lg font-bold text-gray-900">Webhooks & Repush</h4>
                <p class="mt-2 text-sm text-gray-600">Track webhook deliveries and repush failed notifications when needed.</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <img src="{{ asset('icons/At.png') }}" alt="" class="h-10 w-10">
                <h4 class="mt-4 text-lg font-bold text-gray-900">API Keys</h4>
                <p class="mt-2 text-sm text-gray-600">Create and manage merchant API keys securely for your integrations.</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <img src="{{ asset('icons/HandCoins.png') }}" alt="" class="h-10 w-10">
                <h4 class="mt-4 text-lg font-bold text-gray-900">Withdrawals</h4>
                <p class="mt-2 text-sm text-gray-600">Withdraw your available balance to your verified settlement account.</p>
            </div>
        </div>

        <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{route('register')}}" class="inline-flex items-center justify-center px-6 py-3 rounded-full bg-primary text-white font-semibold">
                Get Started <span class="fa fa-arrow-right ml-2"></span>
            </a>
            <a href="{{route('terms-and-conditions')}}" class="inline-flex items-center justify-center px-6 py-3 rounded-full bg-white border border-gray-200 text-gray-900 font-semibold hover:bg-gray-50">
                Terms & Conditions
            </a>
        </div>

    </section>

    {{-- testimonial section --}}
    <section class=" px-4 py-16 bg-slate-50">
        <h2 class=" text-2xl md:text-3xl lg:text-4xl text-center font-semibold">
            What our customers are saying
        </h2>

        <div class=" mt-6 slider pb-4">
            <div class=" slide-track space-x-8 ">
                @foreach ($testimonials as $testimonial)
                <div class="  slide flex flex-col justify-between">
                    <p class=" bg-white border border-gray-300 rounded-2xl p-4 h-full shadow-sm">
                        {{$testimonial->message}}
                    </p>

                    <div class=" flex items-center justify-between gap-2 mt-4">
                        <div class=" flex items-center gap-2">
                            <img src="{{randomImage($testimonial->name)}}" alt="" class=" h-12">
                            <div>
                                <h3 class=" font-semibold ">{{$testimonial->name}}</h3>
                                <p class=" text-sm text-gray-500">
                                    {{$testimonial->profession}}
                                </p>
                            </div>
                        </div>

                        <div>
                            <ul class="flex items-center gap-1">
                                <li>
                                    <span class="fa fa-star text-[#FF5C02] lg:text-lg text-base"></span>
                                </li>
                                <li>
                                    <span class="fa fa-star text-[#FF5C02] lg:text-lg text-base"></span>
                                </li>
                                <li>
                                    <span class="fa fa-star text-[#FF5C02] lg:text-lg text-base"></span>
                                </li>
                                <li>
                                    <span class="fa fa-star text-[#FF5C02] lg:text-lg text-base"></span>
                                </li>
                                <li>
                                    <span class="fa fa-star text-[#FF5C02] lg:text-lg text-base"></span>
                                </li>
                            </ul>
                            <p class=" text-right font-medium">
                                4.1/5
                            </p>
                        </div>
                    </div>
                </div>
                @endforeach


            </div>
        </div>

        <div class=" mt-10  slider-right pb-4 pt-8 lg:pt-16">
            <div class=" slide-track-right space-x-8 ">
                @foreach ($testimonials as $testimonial)
                <div class="  slide flex flex-col justify-between">
                    <p class=" bg-white border border-gray-300 rounded-2xl p-4 h-full shadow-sm">
                        {{$testimonial->message}}
                    </p>

                    <div class=" flex items-center justify-between gap-2 mt-4">
                        <div class=" flex items-center gap-2">
                            <img src="{{randomImage($testimonial->name)}}" alt="" class=" h-12">
                            <div>
                                <h3 class=" font-semibold ">{{$testimonial->name}}</h3>
                                <p class=" text-sm text-gray-500">
                                    {{$testimonial->profession}}
                                </p>
                            </div>
                        </div>

                        <div>
                            <ul class="flex items-center gap-1">
                                <li>
                                    <span class="fa fa-star text-[#FF5C02] lg:text-lg text-base"></span>
                                </li>
                                <li>
                                    <span class="fa fa-star text-[#FF5C02] lg:text-lg text-base"></span>
                                </li>
                                <li>
                                    <span class="fa fa-star text-[#FF5C02] lg:text-lg text-base"></span>
                                </li>
                                <li>
                                    <span class="fa fa-star text-[#FF5C02] lg:text-lg text-base"></span>
                                </li>
                                <li>
                                    <span class="fa fa-star text-[#FF5C02] lg:text-lg text-base"></span>
                                </li>
                            </ul>
                            <p class=" text-right font-medium">
                                4.1/5
                            </p>
                        </div>
                    </div>
                </div>
                @endforeach


            </div>
        </div>
    </section>

    {{-- call to action --}}
    <div class=" mt-6 lg:mt-10 px-4 ">
        <h2 class=" text-lg md:text-xl lg:text-3xl md:font-semibold text-center font-medium mb-4">
            Start accepting payments with {{ config('app.name') }}
        </h2>
        <p class="text-center text-gray-600 max-w-2xl mx-auto">
            Create your merchant account, complete KYB onboarding, generate your API keys, and start collecting payments today.
        </p>
        <div class=" mt-6 lg:max-w-xl lg:mx-auto grid grid-cols-1 lg:grid-cols-2 gap-4 ">
            <a href="{{route('register')}}" class=" block bg-primary text-white text-center p-3 font-semibold text-sm rounded-full lg:text-base">
                Create Merchant Account
            </a>
            <a href="{{route('contact-us')}}" class=" block bg-white border border-gray-200 text-gray-900 text-center p-3 font-semibold text-sm rounded-full lg:text-base hover:bg-gray-50">
                Contact Us
            </a>
        </div>
    </div>



    {{-- 6th seaction --}}
    <section class=" px-4 py-16 lg:py-24 ">
        <div class="grid  gap-6 max-w-6xl mx-auto lg:grid-cols-5 lg:gap-12 ">

            <div class="lg:col-span-2">
                <h3 class=" text-3xl lg:text-4xl font-semibold ">
                    FAQ<span class=" text-3xl">s</span>
                </h3>

                <p class="  text-gray-700 mt-4 md:text-lg lg:text-xl">
                    {{$pageContent->faq_section_sub_heading}}
                </p>
            </div>
            <div x-data="{ open: null }" class=" divide-y-2 lg:col-span-3">

                @foreach ($faqs as $index => $faq)
                <div class="bg-white  overflow-hidden">
                    <button @click="open === {{$index}} ? open = null : open = {{$index}}" class="flex justify-between items-center w-full py-6 lg:py-8 font-medium gap-2 lg:text-xl" :class="open === {{$index}} ? ' bg-white' : ' ' ">
                        <span class=" text text-left">
                            {{$faq->question}}
                        </span>

                        <span class="fa fa-minus" x-show="open === {{$index}}"></span>
                        <span class="fa fa-plus" x-show="open !== {{$index}}"></span>
                    </button>
                    <div x-show="open === {{$index}}" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" x-cloak>
                        <div class="py-4 lg:text-lg">
                            <p class="text-gray-700">
                                {{$faq->answer}}
                            </p>
                        </div>
                    </div>
                </div>

                @endforeach
            </div>


        </div>
    </section>




</div>

@push('scripts')
<script>
    const words = ["Card Payments.", "Bank Transfers.", "USSD Payments.", "Virtual Accounts.",];
    let wordIndex = 0;
    let charIndex = 0;
    let isDeleting = false;
    const typingSpeed = 150;
    const deletingSpeed = 100;
    const pauseBeforeDelete = 1000;
    const textElement = document.getElementById("changingText");

    function type() {
      const currentWord = words[wordIndex];
      
      if (isDeleting) {
        // Deleting text
        charIndex--;
        textElement.textContent = currentWord.substring(0, charIndex);
        
        if (charIndex === 0) {
          isDeleting = false;
          wordIndex = (wordIndex + 1) % words.length;
          setTimeout(type, typingSpeed);
        } else {
          setTimeout(type, deletingSpeed);
        }
      } else {
        // Typing text
        charIndex++;
        textElement.textContent = currentWord.substring(0, charIndex);
        
        if (charIndex === currentWord.length) {
          isDeleting = true;
          setTimeout(type, pauseBeforeDelete);
        } else {
          setTimeout(type, typingSpeed);
        }
      }
    }

    // Start the typing effect
    type();
</script>
@endpush

@push('styles')
<style>
    .slider {
        margin: auto;
        position: relative;
        display: grid;
        place-items: center;
        margin-top: 100px;
        overflow: hidden;
    }


    .slide-track {
        display: flex;
        width: calc(250px * 18);
        animation: scroll 60s linear infinite;
    }

    .slide {
        width: 100vw;


    }



    @keyframes scroll {
        0% {
            transform: translatex(0);
        }

        100% {
            transform: translateX(calc(-250px * 9));
        }
    }

    .slider-right {
        margin: auto;
        position: relative;
        display: grid;
        place-items: center;
        overflow: hidden;
    }

    .slide-track-right {
        display: flex;
        width: calc(250px * 18);
        animation: scrollRight 60s linear infinite;
    }

    @keyframes scrollRight {
        0% {
            transform: translateX(calc(-250px * 9));
        }

        100% {
            transform: translateX(0);
        }
    }
</style>

@endpush
