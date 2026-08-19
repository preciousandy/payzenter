@props(['headerTitle' => ''])
<nav class="bg-white print:hidden border-b border-gray-300  ">
    <header class=" flex justify-between items-center py-2 px-3 lg:py-5 lg:px-6" x-data="{showMobileMenu: false}">
        <div class=" flex items-center gap-3">
            <!-- Logo for Desktop & Mobile -->
            <a href="{{ route('home') }}" class=" lg:hidden">
                <img src="{{asset('logo/pzwhlogo.png')}}" alt="Logo" class="h-8">
            </a>
            <a href="{{ route('home') }}" class=" hidden lg:block">
                <img src="{{asset('logo/pzwhlogo.png')}}" alt="Logo" class="h-10">
            </a>

            <div class=" ml-2">
                <h2 class=" font-semibold md:hidden text-sm md:text-base">
                    Hi, {{auth()->user()->first_name}}
                </h2>
                <h2 class=" font-semibold hidden md:block ">
                    {{$headerTitle}}
                </h2>
            </div>
        </div>

        <div class=" hidden lg:block">

        </div>



        <div class="flex items-center gap-x-6">
            {{-- <button class=" text-2xl" @click="showNotificationModal = true">
                <span class="fa fa-refresh"></span>
            </button> --}}
         
            {{-- user notifications --}}
            @livewire('user-notifications')

            {{-- mobile menu button --}}
            <button class=" text-3xl text-black lg:hidden" @click="showMobileMenu =true">
                <span class="fa fa-bars"></span>
            </button>

            <div class=" relative hidden lg:block" x-data="{showDropdown: false}">
                <div class=" bg-white flex items-center gap-2 rounded-lg hover:bg-gray-100  cursor-pointer" @click="showDropdown = !showDropdown">
                    <img src="{{auth()->user()->profile_photo}}" alt="" class=" h-12 w-12 rounded-full object-cover">

                    <div>
                        <p class=" font-semibold">{{auth()->user()->name}}</p>
                    </div>

                    <button>
                        <span class=" fa fa-caret-down text-xl text-gray-600 ml-2"> </span>
                    </button>
                </div>

                <div class=" absolute min-w-[22rem] bg-white z-50 top-14 right-0 rounded-xl shadow shadow-primary p-4" x-show="showDropdown" x-cloak x-transition>
                    <div class=" flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
                        <a href="{{ route('home') }}" class="flex items-center gap-2">
                            <img src="{{asset('logo/pzwhlogo.png')}}" alt="Logo" class="h-8">
                        </a>
                    </div>
                    <div class="  flex items-center gap-4">
                        <img src="{{auth()->user()->profile_photo}}" alt="" class=" h-12 w-12 rounded-full object-cover">

                        <div>
                            <p class=" font-semibold">{{auth()->user()->name}}</p>
                            <p class=" text-sm text-gray-600">{{auth()->user()->email}}</p>
                        </div>
                    </div>

                    <div class="  flex items-center gap-4 mt-4">
                        <x-lucide-wallet class="h-6 text-gray-500" />

                        <p class=" text-2xl font-bold">
                            <x-naira-symbol /> {{formatNumber(auth()->user()->wallet_balance)}}
                        </p>
                    </div>

                    <ul class="mt-4 divide-y divide-gray-300 border-t border-gray-200">
                        <li>
                            <a href="{{route('profile')}}" class=" font-medium  text-gray-700 flex items-center gap-2 hover:bg-gray-50  py-2">
                                <span class="fa fa-user-circle mr-1 text-lg"></span> Profile
                            </a>
                        </li>
                        <li>
                            <a href="{{route('contact-support')}}" class=" font-medium  text-gray-700 flex items-center gap-2  py-2 hover:bg-gray-50">
                                <span class="fa fa-user-edit mr-1 text-lg"></span> Edit Profile
                            </a>
                        </li>
                        <li>
                            <a href="{{route('profile.change-password')}}" class=" font-medium  text-gray-700 flex items-center gap-2  py-2 hover:bg-gray-50">
                                <span class="fa fa-key mr-1 text-lg"></span> Change Password
                            </a>
                        </li>
                        <li>
                            <a href="{{route('profile.change-transfer-pin')}}" class=" font-medium  text-gray-700 flex items-center gap-2  py-2 hover:bg-gray-50">
                                <span class="fa fa-lock mr-1 text-lg"></span> Change Transaction PIN
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- mobile menu --}}
        <div class=" fixed top-0 left-0 w-screen h-screen bg-gray-50 z-[99999] overflow-y-auto " x-show="showMobileMenu" x-cloak x-transition>

            <div class=" flex justify-end p-3">
                <button class=" text-3xl text-black" @click="showMobileMenu =false">
                    <span class="fa fa-close"></span>
                </button>
            </div>

            <livewire:menu />

        </div>
    </header>
</nav>