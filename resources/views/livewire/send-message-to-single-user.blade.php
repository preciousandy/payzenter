<div x-data="{showAdduserModal: false}">
    <x-secondary-button @click="showAdduserModal = !showAdduserModal" class=" bg-white py-3">
        <span class=" fa fa-envelope mr-1"></span> Send Message
    </x-secondary-button>

    <div x-show="showAdduserModal" x-transition x-cloak class=" fixed bg-black/70 z-50 left-0 top-0 w-full h-full flex justify-center items-center p-4">
        <div class=" bg-gray-50 p-4 lg:p-8 rounded-lg w-full md:w-full max-w-xl space-y-5">
            <div class=" flex justify-between items-center mb-6">
                <h2 class=" text-2xl font-semibold">Send Email</h2>
                <button @click="showAdduserModal = false">
                    <span class="fa fa-close text-2xl"></span>
                </button>
            </div>

            <form action="" class=" " wire:submit.prevent='sendMessage'>
                <div class="  space-y-4">
                    <div>
                        <x-input-label for="subject" :value="__('Email Subject')" />
                        <x-text-input id="subject" type="text" class="mt-2 block w-full" wire:model='subject' required />
                        <x-input-error class="mt-2 " :messages="$errors->get('subject')" />
                    </div>

                    <div>
                        <x-input-label for="message" :value="__('Message')" />
                        <textarea id="message" rows="5" class=" w-full rounded-md bg-white border mt-2 border-gray-300" wire:model='message' required></textarea>
                        <x-input-error class="mt-2 " :messages="$errors->get('message')" />
                    </div>
                </div>

                <x-primary-button class=" w-full rounded-xl mt-8 ">
                    {{ __('Send Message') }}
                </x-primary-button>
            </form>
        </div>
    </div>


</div>
