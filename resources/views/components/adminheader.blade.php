<nav class="bg-white">
    <header class="flex justify-between items-center px-4 lg:py-6">
        <!-- Mobile menu button for sidebar -->
        <button @click="$dispatch('toggle-mobile-sidebar')" class="lg:hidden text-gray-600 hover:text-gray-900 focus:outline-none mr-4">
           <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <a href="{{route('admin.dashboard')}}" class="font-semibold flex items-center space-x-1 lg:hidden">
       
            <span class="text-primary text-sm">Admin</span>
        </a>

        <div class="hidden lg:block">

        </div>

        <div class="flex-1 flex justify-end">
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <p class="font-semibold text-sm md:text-lg cursor-pointer flex items-center gap-2">
                        <img src="{{auth()->user()->profile_photo}}" alt="" class="h-8 w-8 md:h-12 md:w-12 rounded-full object-cover">
                        <span class="hidden sm:inline">Hello, {{auth()->user()->first_name}}</span>
                        <span class="fa fa-caret-down ml-1"></span>
                    </p>
                </x-slot>

                <x-slot name="content">
                    <x-dropdown-link :href="route('admin.edit-profile')" class="">
                        <span class="fa fa-user mr-2"></span> {{ __('Profile') }}
                    </x-dropdown-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <x-dropdown-link :href="route('logout')" onclick="event.preventDefault();
                                                                            this.closest('form').submit();">
                            <span class="fa fa-right-from-bracket mr-2"></span> {{ __('Log Out') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>
    </header>
</nav>