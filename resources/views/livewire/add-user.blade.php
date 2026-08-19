<div x-data="{showAdduserModal: false}">
    <x-secondary-button @click="showAdduserModal = !showAdduserModal" class=" bg-white py-3">
        <span class=" fa fa-user-plus mr-1"></span> Add User
    </x-secondary-button>

    <div x-show="showAdduserModal" x-transition x-cloak class=" fixed bg-black/70 z-50 left-0 top-0 w-full h-full flex justify-center items-center p-4">
        <div class=" bg-gray-50 p-4 lg:p-8 rounded-lg w-full md:w-full max-w-xl space-y-5">
            <div class=" flex justify-between items-center mb-6">
                <h2 class=" text-2xl font-semibold">Add User to Swiftpay</h2>
                <button @click="showAdduserModal = false">
                    <span class="fa fa-close text-2xl"></span>
                </button>
            </div>

            <form action="" class=" " wire:submit.prevent='addUser'>
                <div class="  space-y-4">

                    <div class=" grid grid-cols-2 gap-x-6">
                        <div>
                            <x-input-label for="name" :value="__('First Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" wire:model="first_name" required />
                            <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="name" :value="__('Last Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" wire:model="last_name" required />
                            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                        </div>
                    </div>
                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" wire:model="email" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="password" :value="__('Password')" />
                        <x-text-input id="password" class="block mt-1 w-full" type="password" wire:model="password" required />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>


                    <div>
                        <x-input-label :value="__('User roles')" />
                        <div class=" grid grid-cols-2 gap-3 mt-1">
                            @foreach ($roles as $role)
                            <div class=" text-lg font-medium">
                                <input type="checkbox" id="{{$role}}" value="{{$role}}" class=" mr-2" wire:model.defer='user_roles'>
                                <label for="{{$role}}" class=" cursor-pointer">{{ucfirst(str_replace('_', ' ', $role))}}</label>
                            </div>

                            @endforeach
                        </div>
                    </div>
                </div>

                <x-primary-button class=" w-full rounded-xl mt-8 ">
                    {{ __('Add User') }}
                </x-primary-button>
            </form>
        </div>
    </div>


</div>