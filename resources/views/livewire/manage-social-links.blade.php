<div class=" w-4/5">
    <form action="" wire:submit.prevent='update'>
        <div class=" flex justify-end mb-8">
            <x-primary-button class=" rounded-lg px-8 text-base  w-fit">
                <span class=" fa fa-save mr-2"></span> Save Changes
            </x-primary-button>
        </div>



        <div class="  bg-white divide-y-4">

            @foreach ($socialLinks as $socialLink)


            <div class=" p-6 grid grid-cols-5 items-center">
                <div class=" col-span-2">
                    <h4 class=" text-lg font-semibold">{{ucfirst(str_replace("_", " ", $socialLink['name']))}}</h4>
                </div>
                <div class=" col-span-3">
                    <div>

                        <x-text-input id="end_data" type="text" class="mt-1 block w-full" wire:model="socialLinks.{{$loop->index}}.link" />
                        <x-input-error class="mt-2 " :messages="$errors->get('price')" />
                    </div>
                </div>
                {{-- <x-input-error class="mt-2 " :messages="$errors->get('price')" /> --}}
            </div>

            @endforeach
        </div>
    </form>
</div>