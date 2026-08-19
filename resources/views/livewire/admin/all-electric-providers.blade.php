<div class=" ">
    <h2 class=" text-xl font-semibold mb-6">
        All Electricity Providers
    </h2>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-black  bg-gray-50 dark:bg-gray-700 dark:text-gray-400 uppercase">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        Image
                    </th>

                    <th scope="col" class="px-6 py-3">
                        Name
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Action
                    </th>

                </tr>
            </thead>
            <tbody>
                @foreach ($providers as $provider)

                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-sky-50 dark:hover:bg-gray-600 ">
                    <td class="px-6 py-4">
                        <img src="{{asset($provider->logo_url)}}" alt="" class=" h-12 w-12  rounded-full object-cover">
                    </td>
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{$provider->name}}
                    </th>
                    <td class="px-6 py-4">
                        <div x-data="{showEditModal: false}">
                            <x-secondary-button @click="showEditModal = true" class=" rounded-xl">
                                <span class=" fa fa-pen mr-2"></span> Edit
                            </x-secondary-button>

                            <div x-show="showEditModal" x-transition x-cloak class=" fixed bg-black/70 z-50 left-0 top-0 w-full h-full flex justify-center items-center p-4">
                                <div class=" bg-gray-50 p-4 lg:p-8 rounded-lg w-full  max-w-xl space-y-5">
                                    <div class=" flex justify-between items-center mb-6">
                                        <h2 class=" text-2xl font-semibold">Edit Electricity provider</h2>
                                        <button @click="showEditModal = false">
                                            <span class="fa fa-close text-2xl"></span>
                                        </button>
                                    </div>

                                    <form action="" class=" space-y-4" wire:submit.prevent='updateElectricityProvider({{$provider->id}})'>
                                        <div>
                                            <x-input-label for="amt" :value="__('Logo Image')" />
                                            <x-text-input id="amt" class="block mt-1 w-full" type="file" wire:model="logo_upload" required autofocus />
                                            <x-input-error :messages="$errors->get('logo_upload')" class="mt-2" />
                                        </div>
                                        <x-primary-button class=" w-full rounded-xl ">
                                            {{ __('Update') }}
                                        </x-primary-button>
                                    </form>
                                </div>
                            </div>


                        </div>
                    </td>



                </tr>
                @endforeach


            </tbody>
        </table>


    </div>

</div>