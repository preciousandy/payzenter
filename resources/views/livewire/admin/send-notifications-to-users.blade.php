<div x-data="{showAdduserModal: @entangle('showModal')}">
    {{-- <x-secondary-button @click="showAdduserModal = !showAdduserModal" class=" bg-white py-3">
        <span class=" fa fa-envelope mr-1"></span> Send Notifications
    </x-secondary-button> --}}

    <div x-show="showAdduserModal" x-transition x-cloak class=" fixed bg-black/70 z-50 left-0 top-0 w-full h-full flex justify-center items-center p-4">
        <div class=" bg-gray-50 p-4 lg:p-8 rounded-lg w-full md:w-full max-w-xl space-y-5 max-h-[90%] overflow-y-auto">
            <div class=" flex justify-between items-center mb-6">
                <h2 class=" text-xl font-semibold">Send Notifications to SwifyPay Users</h2>
                <button @click="showAdduserModal = false">
                    <span class="fa fa-close text-3xl text-gray-700"></span>
                </button>
            </div>

            <form action="" class=" ">
                <div class="space-y-4">
                    <div>
                        <x-input-label for="subject" :value="__('Subject')" />

                        <x-text-input id="subject" type="text" class="mt-2 block w-full" wire:model='subject' required />
                        <x-input-error class="mt-2 " :messages="$errors->get('subject')" />
                    </div>

                    <div class=" " x-data="{ currentTab: @entangle('channel') }">
                        <p class=" mb-2 font-semibold">
                            Channel
                        </p>

                        @foreach ($channels as $channel)
                        <button type="button" class=" py-3 px-5  rounded-lg whitespace-nowrap" :class="currentTab == '{{ $channel }}' ? '  font-semibold  bg-primary text-white' : 'bg-white text-primary'" wire:click="$set('channel', '{{ $channel }}')">
                            <span x-text="'{{ $channel }}'"></span>
                        </button>
                        @endforeach

                    </div>

                    <div class="">
                        <x-input-label for="message" :value="__('Message')" />

                        <textarea name="" id="message" rows="2" class=" w-full rounded-md bg-white border border-gray-300 mt-2" wire:model='message' required></textarea>

                        <x-input-error class="mt-2 " :messages="$errors->get('message')" />
                    </div>
                </div>

                <x-primary-button type="button" class=" w-full rounded-xl mt-8 " wire:click='sendMessage'>
                    {{ __('Send Message') }}
                </x-primary-button>
            </form>
        </div>
    </div>
</div>