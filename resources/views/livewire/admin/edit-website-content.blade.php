<div class=" max-w-6xl">
    <form action="" wire:submit.prevent='saveChanges'>
        <div class=" flex justify-end ">
            <x-primary-button class=" rounded-lg px-8 text-base py-4 w-fit">
                <span class=" fa fa-save mr-2"></span> Save Changes
            </x-primary-button>
        </div>

        <div class="  divide-y bg-white border border-gray-300 rounded-xl p-6 mt-6">
            <div class="  grid grid-cols-3 items-center py-5">
                <div class=" col-span-1 flex items-center">
                    @if ($logo_path)
                    <img src="{{$logo_path}}" alt="" class=" h-10 mr-2">
                    @endif
                    <h4 class=" text-lg font-medium">Logo</h4>
                </div>
                <div class=" w-full col-span-2">
                    <x-text-input id="logo" type="file" class=" block w-full" wire:model='logo' />
                    <x-input-error class=" " :messages="$errors->get('logo')" />
                </div>
            </div>
            <div class="  grid grid-cols-3 items-center py-5">
                <div class=" col-span-1 flex items-center">
                    @if ($favicon_path)
                    <img src="{{$favicon_path}}" alt="" class=" h-10 mr-2">
                    @endif
                    <h4 class=" text-lg font-medium">Favicon</h4>
                </div>
                <div class=" w-full col-span-2">
                    <x-text-input id="favicon" type="file" class=" block w-full" wire:model='favicon' />
                    <x-input-error class=" " :messages="$errors->get('favicon')" />
                </div>
            </div>
            <div class="  grid grid-cols-3 items-center py-5">
                <div class=" col-span-1">
                    <h4 class=" text-lg font-medium">Contact Phone Number</h4>
                </div>
                <div class=" w-full col-span-2">
                    <x-text-input id="contact_phone" type="text" class=" block w-full" wire:model='contact_phone' />
                    <x-input-error class=" " :messages="$errors->get('contact_phone')" />
                </div>
            </div>
            <div class="  grid grid-cols-3 items-center py-5">
                <div class=" col-span-1">
                    <h4 class=" text-lg font-medium">Contact Email</h4>
                </div>
                <div class=" w-full col-span-2">
                    <x-text-input id="contact_email" type="text" class=" block w-full" wire:model='contact_email' />
                    <x-input-error class=" " :messages="$errors->get('contact_email')" />
                </div>
            </div>
            <div class="  grid grid-cols-3 items-center py-5">
                <div class=" col-span-1">
                    <h4 class=" text-lg font-medium">Contact Address</h4>
                </div>
                <div class=" w-full col-span-2">
                    <x-text-input id="contact_address" type="text" class=" block w-full" wire:model='contact_address' />
                    <x-input-error class=" " :messages="$errors->get('contact_address')" />
                </div>
            </div>
            <div class="  grid grid-cols-3 items-center py-5">
                <div class=" col-span-1">
                    <h4 class=" text-lg font-medium">App Store link</h4>
                </div>
                <div class=" w-full col-span-2">
                    <x-text-input id="contact_address" type="text" class=" block w-full" wire:model='app_store_link' />
                    <x-input-error class=" " :messages="$errors->get('app_store_link')" />
                </div>
            </div>
            <div class="  grid grid-cols-3 items-center py-5">
                <div class=" col-span-1">
                    <h4 class=" text-lg font-medium">Play store link</h4>
                </div>
                <div class=" w-full col-span-2">
                    <x-text-input id="contact_address" type="text" class=" block w-full" wire:model='play_store_link' />
                    <x-input-error class=" " :messages="$errors->get('play_store_link')" />
                </div>
            </div>
            <div class="  grid grid-cols-3 items-center py-5">
                <div class=" col-span-1">
                    <h4 class=" text-lg font-medium">Website description</h4>
                </div>
                <div class=" w-full col-span-2">
                    <textarea id="description" type="text" class=" block w-full border-gray-300 rounded-lg shadow-sm" wire:model='description' rows="3"></textarea>
                    <x-input-error class=" " :messages="$errors->get('description')" />
                </div>
            </div>


        </div>
    </form>
</div>