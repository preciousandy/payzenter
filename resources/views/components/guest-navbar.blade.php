<nav class="  border-b sticky top-0 z-20   duration-1000   bg-white " data-taos-offset="500">
    <header class=" flex justify-between items-center px-2 py-3 lg:py-6 max-w-[90rem] mx-auto" x-data="{showMobileMenu: false}">
        <a href="/" class=" ">
            <img src="{{asset('logo/pzwhlogo.png')}}" alt="payzenter logo" class="h-16 lg:h-20">
        </a>


        <ul class="  space-x-4 lg:flex items-center hidden ">

            <li><a href="{{route('welcome')}}" @class(['font-medium p-2', 'text-primary border-b-4  border-primary'=> request()->routeIs('welcome')])>Home</a></li>
            <li><a href="{{route('about-us')}}" @class(['font-medium p-2', 'text-primary border-b-4  border-primary'=> request()->routeIs('about-us')])>About Us</a></li>
            <li><a href="{{route('contact-us')}}" @class(['font-medium p-2', 'text-primary border-b-4  border-primary'=> request()->routeIs('contact-us')])>Contact Us</a></li>

        </ul>

        @auth
        <a href="{{ route('home')}}" class="lg:block  bg-primary px-12 text-white rounded-full font-medium py-3 hidden">
            Profile
        </a>

        @else
        <div class=" lg:flex items-center  space-x-4 hidden">
            <a href="{{route('register')}}">
                <x-primary-button class=" rounded-full w-36 py-4 ">
                    Sign Up
                </x-primary-button>
            </a>

            <a href="{{route('login')}}">
                <x-secondary-button class=" rounded-full w-36 py-4">
                    Login
                </x-secondary-button>
            </a>
        </div>
        @endauth

        {{-- mobile menu --}}
        <div class=" lg:hidden" x-data="{isOpen: false}">
            <button @click="isOpen = !isOpen" class=" text-2xl lg:hidden">
                <span class=" fa fa-bars"></span>
            </button>

            <!-- Navigation menu -->
            <div class=" lg:hidden">
                <nav x-show="isOpen" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="fixed inset-y-0 left-0 w-full  shadow-lg transform p-2 h-screen bg-white flex flex-col justify-between pb-20">
                    <div>
                        <div class=" flex justify-end">
                            <button @click="isOpen = !isOpen" class=" text-2xl">
                                <span class=" fa fa-times"></span>
                            </button>
                        </div>
                        <ul class=" space-y-2 divide-y divide-gray-300 mt-4">
                            <li><a href="{{route('welcome')}}" class="block py-3 px-4 text-gray-800 hover:bg-indigo-100 hover:text-primary rounded-md font-medium hover:font-semibold">Home</a></li>

                            <li><a href="{{route('about-us')}}" class="block py-3 px-4 text-gray-800 hover:bg-indigo-100 hover:text-primary rounded-md font-medium hover:font-semibold">About Us</a></li>

                            <li><a href="{{route('contact-us')}}" class="block py-3 px-4 text-gray-800 hover:bg-indigo-100 hover:text-primary rounded-md font-medium hover:font-semibold">Contact Us</a></li>
                        </ul>
                    </div>

                    @guest
                    <div>
                        <a href="{{route('register')}}" class=" block">
                            <x-secondary-button class=" w-full">
                                Sign up
                            </x-secondary-button>
                        </a>
                        <a href="{{route('login')}}" class=" block mt-3">
                            <x-primary-button class=" w-full">
                                Login
                            </x-primary-button>
                        </a>
                    </div>

                    @else
                    <div>
                        <a href="{{route('home')}}" class=" block">
                            <button class=" bg-blue-100 rounded-sm text-left w-full p-3 text-primary font-medium">
                                <span class="fa fa-user mr-2"></span> Profile
                            </button>
                        </a>
                    </div>
                    @endguest


                </nav>
            </div>
        </div>



    </header>
</nav>