<div class=" px-4 py-6">

    <h2 class=" text-lg font-semibold">
        Give Us your Feedback
    </h2>
    <p class=" mt-2">
        We are always looking for ways to improve our services. Please let us know how we can serve you better
    </p>

    <form class=" mt-6 space-y-4" wire:submit.prevent='create'>
        <div>
            <x-input-label for="name" :value="__('Subject')" />
            <x-text-input id="name" type="text" class="mt-2 block w-full" wire:model='subject' required autofocus />
            <x-input-error class="mt-2 " :messages="$errors->get('subject')" />
        </div>

        <div>
            <x-input-label for="phone" :value="__('Select Category')" />

            <select wire:model="category" id="" class=" p-3 w-full rounded-lg border-gray-300 mt-2" required>
                <option value="complaint">Complaint</option>
                <option value="suggestion">Suggestion</option>
                <option value="enquiry">Enquiry</option>

            </select>
            <x-input-error class="mt-2" :messages="$errors->get('category')" />
        </div>

        <div>
            <x-input-label for="name" :value="__('Message')" />
            <textarea name="" id="" rows="4" class=" w-full rounded-md bg-white border mt-2" wire:model='message' required></textarea>
            <x-input-error class="mt-2 " :messages="$errors->get('message')" />
        </div>

        <x-primary-button class=" w-full">
            Submit Feedback
        </x-primary-button>


    </form>

</div>