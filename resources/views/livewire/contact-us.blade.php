<div class=" px-4 py-6 bg-[#F7FBFC]">
    <div class=" lg:mt-12">
        <h2 class=" font-bold text-xl lg:text-3xl xl:text-4xl text-center">
            Contact Us
        </h2>
        <p class=" mt-3 text-gray-700 text-center max-w-xl mx-auto lg:text-lg  ">
            We are here to help! Reach out with any questions, feedback, or inquiries. Our team is ready to assist you.
        </p>
    </div>

    <div class=" max-w-7xl mx-auto grid md:grid-cols-2 lg:grid-cols-3 gap-4 mt-10 lg:mt-16 text-sm">

        <div class=" flex items-center gap-x-3">
            <p class=" h-12 w-12 rounded-full flex items-center justify-center bg-white">
                <span class=" fa fa-phone text-[#473BF0] text-lg"></span>
            </p>
            <div>
                <h4 class=" font-semibold text-base">
                    Call Us
                </h4>
                <p class="text-gray-800 mt-2">
                    <a href="tel:+2349067515176">
                        {{get_website_content('contact_phone')}}
                    </a>
                </p>
            </div>
        </div>
        <div class=" flex items-center gap-x-3">
            <p class=" h-12 w-12 rounded-full flex items-center justify-center bg-white">
                <span class=" fa fa-envelope text-[#473BF0] text-lg"></span>
            </p>
            <div>
                <h4 class=" font-semibold text-base">
                    Email Us
                </h4>
                <p class="text-gray-800 mt-2">
                    <a href="mailto:{{get_website_content('contact_email')}}">
                        {{get_website_content('contact_email')}}
                    </a>
                </p>
            </div>
        </div>
        <div class=" flex items-center gap-x-3">
            <p class=" h-12 w-12 rounded-full flex items-center justify-center bg-white">
                <span class=" fa fa-location-dot text-[#473BF0] text-lg"></span>
            </p>
            <div>
                <h4 class=" font-semibold text-base">
                    Visit Us
                </h4>
                <p class="text-gray-800 mt-2">
                    {{get_website_content('contact_address')}}
                </p>
            </div>
        </div>

    </div>

    <form class=" mt-10 lg:mt-14  grid gap-4 lg:grid-cols-2 bg-white rounded-lg px-4 py-6 shadow-xl max-w-5xl mx-auto lg:px-8 lg:pt-14" wire:submit.prevent='submit'>
        <div>
            <x-input-label for="name" :value="__('Full Name')" />
            <x-text-input id="name" type="text" class="mt-2 block w-full" wire:model='name' required autofocus />
            <x-input-error class="mt-2 " :messages="$errors->get('name')" />
        </div>
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" class="mt-2 block w-full" wire:model='email' required />
            <x-input-error class="mt-2 " :messages="$errors->get('email')" />
        </div>
        <div>
            <x-input-label for="phone" :value="__('Phone Number')" />
            <x-text-input id="phone" type="tel" class="mt-2 block w-full" wire:model='phone' required />
            <x-input-error class="mt-2 " :messages="$errors->get('phone')" />
        </div>
        <div>
            <x-input-label for="name" :value="__('Subject')" />
            <x-text-input id="name" type="text" class="mt-2 block w-full" wire:model='subject' required />
            <x-input-error class="mt-2 " :messages="$errors->get('subject')" />
        </div>



        <div class=" lg:col-span-2">
            <x-input-label for="name" :value="__('Message')" />
            <textarea name="" id="" rows="4" class=" w-full rounded-md bg-white border border-gray-300 mt-2" wire:model='message' required></textarea>
            <x-input-error class="mt-2 " :messages="$errors->get('message')" />
        </div>

        <div class=" lg:col-span-2">
            <x-primary-button class=" rounded-full px-10 md:px-12 lg:px-14">
                Send
            </x-primary-button>
        </div>


    </form>
</div>