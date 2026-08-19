<div x-show="showEditModal" x-transition x-cloak class=" fixed bg-black/70 z-50 left-0 top-0 w-full h-full flex justify-center items-center p-4">
    <div class=" bg-gray-50 p-4 lg:p-8 rounded-lg w-full md:w-full space-y-5 max-w-xl">
        <div class=" flex justify-between items-center mb-6">
            <h2 class=" text-2xl font-semibold">Edit Question</h2>
            <button @click="showEditModal = false">
                <span class="fa fa-close text-2xl"></span>
            </button>
        </div>

        <form action="" class=" space-y-4" wire:submit.prevent='update'>
            <div>
                <x-input-label for="name" :value="__('Question')" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" wire:model="faq.question" required autofocus />
                <x-input-error :messages="$errors->get('question')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Answer')" />
                <textarea name="" id="" rows="4" class=" w-full rounded-md bg-white border border-gray-300 mt-2" wire:model='faq.answer' required></textarea>
                <x-input-error :messages="$errors->get('answer')" class="mt-2" />
            </div>

            <x-primary-button class=" w-full rounded-xl ">
                {{ __('update Question') }}
            </x-primary-button>
        </form>
    </div>
</div>