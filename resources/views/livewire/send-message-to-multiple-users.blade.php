<div x-data="{showAdduserModal: @entangle('showModal')}">
    {{-- <x-secondary-button @click="showAdduserModal = !showAdduserModal" class=" bg-white py-3">
        <span class=" fa fa-envelope mr-1"></span> Send Message
    </x-secondary-button> --}}

    <div x-show="showAdduserModal" x-transition x-cloak class=" fixed bg-black/70 z-50 left-0 top-0 w-full h-full flex justify-center items-center p-4">
        <div class=" bg-gray-50 p-4 lg:p-8 rounded-lg w-full md:w-full max-w-2xl space-y-5 max-h-[90%] overflow-y-auto">
            <div class=" flex justify-between items-center mb-6">
                <h2 class=" text-xl font-semibold">Send Bulk Email</h2>
                <button @click="showAdduserModal = false">
                    <span class="fa fa-close text-3xl text-gray-700"></span>
                </button>
            </div>

            <form action="" class=" ">
                <div class="  space-y-4">

                    <div>
                        <x-input-label for="subject" :value="__('Email Subject')" />

                        <x-text-input id="subject" type="text" class="mt-2 block w-full" wire:model='subject' required />
                        <x-input-error class="mt-2 " :messages="$errors->get('subject')" />
                    </div>


                    <div id="editor">
                        <x-input-label for="" :value="__('Enter Message here')" />
                        <livewire:jodit-text-editor wire:model.live="message" />
                        <x-input-error class="mt-2" :messages="$errors->get('message')" />
                    </div>


                </div>

                <x-primary-button type="button" class=" w-full rounded-xl mt-8 " wire:click='sendMessage'>
                    {{ __('Send Message') }}
                </x-primary-button>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    #editor ol,
    #editor ul {
        list-style-type: disc !important;
        margin-left: 1.5rem !important;
        margin-bottom: 1rem !important;
        padding-left: 1rem !important;
    }

    #editor ol {
        list-style-type: decimal !important;
    }

    #editor li {
        margin-bottom: 0.5rem !important;
        padding-left: 0.5rem !important;
    }

    #editor ol ol,
    #editor ul ul {
        margin-top: 0.5rem !important;
        margin-bottom: 0.5rem !important;
    }
</style>
@endpush
