<div>
    <div class=" flex justify-between items-center">
        <h2 class=" text-2xl font-bold">
            Manage User Testimonials
        </h2>

        <div x-data="{showAddModal: false}">
            <x-primary-button @click="showAddModal = true" class=" rounded-xl">
                <span class=" fa fa-plus-circle mr-2"></span> Add Testimonial
            </x-primary-button>

            <div x-show="showAddModal" x-transition x-cloak class=" fixed bg-black/70 z-50 left-0 top-0 w-full h-full flex justify-center items-center p-4">
                <div class=" bg-gray-50 p-4 lg:p-8 rounded-lg w-full md:w-full space-y-5 max-w-xl">
                    <div class=" flex justify-between items-center mb-6">
                        <h2 class=" text-2xl font-semibold">Add User Testimonial</h2>
                        <button @click="showAddModal = false">
                            <span class="fa fa-close text-2xl"></span>
                        </button>
                    </div>

                    <form action="" class=" space-y-4" wire:submit.prevent='addTestimonial'>
                        <div class=" grid grid-cols-2 gap-x-4">
                            <div>
                                <x-input-label for="name" :value="__('Name')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" wire:model="name" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="profession" :value="__('Profession')" />
                                <x-text-input id="profession" class="block mt-1 w-full" type="text" wire:model="profession" required autofocus />
                                <x-input-error :messages="$errors->get('profession')" class="mt-2" />
                            </div>
                        </div>
                        <div>
                            <x-input-label for="email" :value="__('Message')" />
                            <textarea name="" id="" rows="4" class=" w-full rounded-md bg-white border border-gray-300 mt-2" wire:model='message' required></textarea>
                            <x-input-error :messages="$errors->get('message')" class="mt-2" />
                        </div>

                        <x-primary-button class=" w-full rounded-xl ">
                            {{ __('Add Testimonial') }}
                        </x-primary-button>
                    </form>
                </div>
            </div>


        </div>
    </div>





    {{-- user testimonials --}}

    <div class=" mt-8 space-y-4">
        @foreach ($testimonials as $testimonial)

        <div class=" bg-white border border-gray-300 rounded-xl p-6 slide flex flex-col justify-between">

            <div class=" flex items-center justify-between">
                <div>
                    <h3 class=" font-semibold text-lg ">{{$testimonial->name}}</h3>
                    <p class=" text-gray-600">
                        {{$testimonial->profession}}
                    </p>
                </div>

                <div class=" flex items-center gap-x-4">
                    <div x-data="{showEditModal: false}">
                        <x-secondary-button @click="showEditModal = true" class=" rounded-lg">
                            <span class=" fa fa-edit"></span>
                        </x-secondary-button>

                        @livewire('edit-user-testimonial', ['testimonial' => $testimonial], key($testimonial->id))
                    </div>

                    <div x-data="{showDeleteModal: false}">
                        <x-danger-button @click="showDeleteModal = true" class=" py-3">
                            <span class=" fa fa-trash"></span>
                        </x-danger-button>

                        {{-- delete role modal --}}
                        <div x-show="showDeleteModal" x-transition x-cloak class=" fixed bg-black/70 z-50 left-0 top-0 w-full h-full flex justify-center items-center p-4">
                            <div class=" bg-gray-50 p-4 lg:p-8 rounded-lg w-full md:w-full max-w-lg ">
                                <div class=" flex justify-between items-center mb-6">
                                    <h2 class=" text-2xl font-semibold">Delete User testimonial</h2>
                                    <button @click="showDeleteModal = false">
                                        <span class="fa fa-close text-2xl"></span>
                                    </button>
                                </div>

                                <h2 class=" text-xl text-center  font-medium">
                                    Are you sure you want to delete this user testimonial?
                                    <br>
                                    This action cannot be undone.
                                </h2>

                                <div class=" grid grid-cols-2 gap-x-4 mt-8">
                                    <button @click="showDeleteModal = false" class=" text-center font-semibold">
                                        Cancel
                                    </button>
                                    <x-primary-button class=" w-full bg-red-800 rounded-xl" wire:click='deleteTestimonial({{$testimonial->id}})'>
                                        Yes, Delete
                                    </x-primary-button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <p class=" mt-4">
                {{$testimonial->message}}
            </p>
        </div>

        @endforeach
    </div>
</div>