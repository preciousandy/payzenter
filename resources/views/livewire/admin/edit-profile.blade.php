<div class=" px-4 py-6 max-w-2xl">
    <form action="" class=" space-y-4" wire:submit.prevent='updateProfile'>

        <div class="  mt-4">
            <x-input-label :value="__('Change Profile Image')" />

            @if ( $tempCoverImage)
            <div>
                <img src="{{ $tempCoverImage }}" alt="Preview" class=" rounded-full h-48 w-48 object-cover  mt-2 ">
            </div>
            @else
            <label for="profile-pic" class=" flex items-center justify-center cursor-pointer    bg-purple-100 border-2 border-purple-200  rounded-full h-48 w-48 border-dashed mt-2">
                <span class=" fa fa-user text-primary fa-5x"></span>
            </label>
            @endif

            <x-text-input id="profile-pic" type="file" class=" block w-full disabled:bg-gray-100 disabled:cursor-not-allowed  mt-4 rounded-full border border-gray-200 p-3 bg-white" wire:model='photo' />

            <x-input-error class="mt-2" :messages="$errors->get('photo')" />
        </div>

        <div class="">
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" type="text" class="mt-1 block w-full py-3" wire:model='name' required />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>
        <div class="">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="text" class="mt-1 block w-full py-3" wire:model='email' required />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <x-primary-button class="  w-48">
            Update Profile
        </x-primary-button>
    </form>

    <h3 class=" text-xl font-semibold mt-12 mb-4">
        Change Account Password

    </h3>

    <form action="" class=" space-y-4" wire:submit.prevent='changePassword'>

        <div class="">
            <x-input-label for="old_password" :value="__('Old Password')" />
            <x-text-input id="old_password" type="password" class="mt-1 block w-full py-3" wire:model='old_password' required />
            <x-input-error class="mt-2" :messages="$errors->get('old_password')" />
        </div>
        <div class="">
            <x-input-label for="password" :value="__('New Password')" />
            <x-text-input id="password" type="password" class="mt-1 block w-full py-3" wire:model='password' required />
            <x-input-error class="mt-2" :messages="$errors->get('password')" />
        </div>
        <div class="">
            <x-input-label for="password_confirmation" :value="__('Confirm New Password')" />
            <x-text-input id="password_confirmation" type="password" class="mt-1 block w-full py-3" wire:model='password_confirmation' required />
            <x-input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
        </div>

        <x-primary-button class=" w-48">
            Change Password
        </x-primary-button>
    </form>
</div>