<div x-data="{showChangeRoleModal: false}">
    <x-secondary-button @click="showChangeRoleModal = !showChangeRoleModal" class=" bg-white py-3">
        <span class=" fa fa-key mr-1"></span> Change Role
    </x-secondary-button>

    <div x-show="showChangeRoleModal" x-transition x-cloak class=" fixed bg-black/70 z-50 left-0 top-0 w-full h-full flex justify-center items-center p-4">
        <div class=" bg-gray-50 p-4 lg:p-8 rounded-lg w-full md:w-full max-w-xl space-y-5">
            <div class=" flex justify-between items-center mb-6">
                <h2 class=" text-2xl font-semibold">Change {{$user->name}} Role</h2>
                <button @click="showChangeRoleModal = false">
                    <span class="fa fa-close text-2xl"></span>
                </button>
            </div>

            <form action="" class=" " wire:submit.prevent='updateRoles'>
                <div class="  space-y-4">
                    @foreach ($roles as $role)
                    <div class=" text-lg font-medium">
                        <input type="checkbox" id="{{$role}}" value="{{$role}}" class=" mr-2" wire:model.defer='user_roles'>
                        <label for="{{$role}}" class=" cursor-pointer">{{ucfirst(str_replace('_', ' ', $role))}}</label>
                    </div>

                    @endforeach
                </div>

                <x-primary-button class=" w-full rounded-xl mt-8 ">
                    {{ __('Update Roles') }}
                </x-primary-button>
            </form>
        </div>
    </div>


</div>