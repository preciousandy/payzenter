<div class="   min-h-screen   lg:h-screen p-2  relative ">

    <a href="{{route('welcome')}}" class=" block w-fit ">
        <img src="{{asset('logo 3.png')}}" alt="" class=" h-20 hidden lg:block mt-4">
    </a>

    <div class=" lg:absolute lg:top-1/2  lg:left-1/2 lg:-translate-x-1/2  lg:-translate-y-1/2  max-w-xl w-full mx-auto  ">
        <form wire:submit.prevent='resetPassword' class=" p-2 py-4 lg:p-6 rounded-xl  w-full  ">
            <a href="{{route('welcome')}}" class=" block w-fit lg:hidden ">
                <img src="{{asset('logo 3.png')}}" alt="" class=" h-14">
            </a>
            <h3 class=" text-lg lg:text-3xl font-semibold mt-4">Reset Password</h3>
            <p class=" mt-1 lg:mt-2 lg:text-lg">
                Enter a new strong password
            </p>


            <div class=" space-y-4 mt-6">
                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full disabled:bg-gray-100" type="email" wire:model="email" required autocomplete="username" disabled />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>


                <!-- Password -->
                <div class="relative">
                    <x-input-label for="password" :value="__('New Password')" />

                    <div class="relative" x-data="{ show: false }">
                        <input id="password" class="border-gray-300 dark:border-gray-700  focus:border-indigo-500  focus:ring-indigo-500  rounded-md shadow-sm px-3 py-2 w-full pr-10 mt-1 lg:py-4" :type="show ? 'text' : 'password'" wire:model="password" required autocomplete="new-password" />

                        <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center" x-on:click="show = !show">
                            <span x-show="show" class="fa fa-eye-slash"></span>
                            <span x-show="!show" class="fa fa-eye"></span>

                        </button>
                    </div>

                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div class="relative">
                    <x-input-label for="password" :value="__('Confirm New Password')" />

                    <div class="relative" x-data="{ show: false }">
                        <input id="password" class="border-gray-300 dark:border-gray-700  focus:border-indigo-500  focus:ring-indigo-500  rounded-md shadow-sm px-3 py-2 w-full pr-10 mt-1 lg:py-4" :type="show ? 'text' : 'password'" wire:model="password_confirmation" required autocomplete="new-password" />

                        <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center" x-on:click="show = !show">
                            <span x-show="show" class="fa fa-eye-slash"></span>
                            <span x-show="!show" class="fa fa-eye"></span>

                        </button>
                    </div>

                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
            </div>

            <div class=" ">
                <x-primary-button class=" w-full mt-8 rounded-lg">
                    {{ __('Change Password') }}
                </x-primary-button>
            </div>




        </form>
    </div>
</div>