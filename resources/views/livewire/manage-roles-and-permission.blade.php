<div class=" px-4 py-6">

    <h2 class=" text-2xl font-bold">
        Manage Roles and Permissions
    </h2>



    <div class=" mt-6 grid grid-cols-6 gap-x-4">
        <div class=" col-span-2">
            <h3 class=" text-lg font-semibold">
                Roles
            </h3>

            <form action="" class=" mt-4 flex items-center gap-4" wire:submit.prevent='addRole'>
                <x-text-input type="text" wire:model='role_name' class=" py-3 w-full flex-1" placeholder="Role Name" required />

                <x-primary-button class=" rounded-lg flex-shrink-0 w-fit px-6">
                    Add Role
                </x-primary-button>
            </form>

            <div class=" mt-6 space-y-3">
                @foreach ($roles as $role)
                <div x-data="{showDeleteModal: false}">

                    <div @class([' border-2 border-blue-700 cursor-pointer rounded-xl flex items-center justify-between ', ' bg-blue-700 text-white font-semibold'=> $selectedRole->id == $role->id, ' bbg-white text-primary'=> $selectedRole->id !== $role->id ]) >
                        <h4 wire:click="$set('roleId', '{{$role->id}}')" class=" text-lg font-medium  p-4 w-full">
                            {{$role->name}}
                        </h4>

                        <button class=" text-xl px-5" @click="showDeleteModal = !showDeleteModal">
                            <span class=" fa fa-trash"></span>
                        </button>
                    </div>

                    {{-- delete role modal --}}
                    <div x-show="showDeleteModal" x-transition x-cloak class=" fixed bg-black/70 z-50 left-0 top-0 w-full h-full flex justify-center items-center p-4">
                        <div class=" bg-gray-50 p-4 lg:p-8 rounded-lg w-full md:w-full lg:w-1/3 xl:w-1/4 ">
                            <div class=" flex justify-between items-center mb-6">
                                <h2 class=" text-2xl font-semibold">Delete {{$role->name}} Role</h2>
                                <button @click="showDeleteModal = false">
                                    <span class="fa fa-close text-2xl"></span>
                                </button>
                            </div>

                            <h2 class=" text-xl text-center  font-medium">
                                Are you sure you want to delete this role? <br>
                                All associated permissions will be revoked.
                            </h2>

                            <div class=" grid grid-cols-2 gap-x-4 mt-8">
                                <button @click="showDeleteModal = false" class=" text-center font-semibold">
                                    Cancel
                                </button>
                                <x-primary-button class=" w-full bg-red-800 rounded-xl" wire:click='deleteRole({{$role->id}})'>
                                    Yes, Delete Role
                                </x-primary-button>
                            </div>
                        </div>
                    </div>
                </div>

                @endforeach
            </div>
        </div>

        <div class=" col-span-4">
            <div class=" flex justify-between items-center">
                <h3 class=" text-lg font-semibold">
                    {{$selectedRole->name}} Role Permissions
                </h3>

                <x-primary-button class=" rounded-lg w-fit px-6" wire:click='updatePermissions'>
                    Update Permissions
                </x-primary-button>
            </div>

            <div class=" mt-4 space-y-3">
                @foreach ($permissions as $permission)
                <div class=" text-base font-medium">
                    <input type="checkbox" id="{{$permission->id}}" value="{{$permission->name}}" class=" mr-2" wire:model.defer='selectedPermissions'>
                    <label for="{{$permission->id}}" class=" cursor-pointer">{{ucfirst(str_replace('_', ' ', $permission->name))}}</label>
                </div>

                @endforeach
            </div>
        </div>
    </div>
</div>