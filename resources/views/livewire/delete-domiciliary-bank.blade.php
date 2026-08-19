<div x-data="{showDeleteModal:false}">
    <x-secondary-button @click="showDeleteModal = !showDeleteModal" class=" text-red-700 border-red-700">
        <span class="fa fa-trash mr-1"></span> Delete
    </x-secondary-button>

    <div x-show="showDeleteModal" x-transition x-cloak class=" fixed bg-black/70 z-50 left-0 top-0 w-full h-full flex justify-center items-center p-4">
        <div class=" bg-gray-50 p-4 lg:p-8 rounded-lg w-full  max-w-xl ">
            <div class=" flex justify-between items-center mb-6">
                <h2 class=" text-lg font-semibold">Delete Bank</h2>
                <button @click="showDeleteModal = false">
                    <span class="fa fa-close text-2xl"></span>
                </button>
            </div>

            <p class=" text-xl text-center  text-gray-700 whitespace-normal">
                Are you sure you want to delete {{$bank?->bank_name}} from the list of domiciliary banks?
            </p>

            <div class=" grid grid-cols-2 gap-x-4 mt-8">
                <button @click="showDeleteModal = false" class=" text-center font-semibold bg-gray-100  rounded-lg">
                    Cancel
                </button>
                <x-primary-button class=" w-full bg-red-800 rounded-xl" wire:click='deleteBank({{$bank->id}})'>
                    Yes, Delete bank
                </x-primary-button>
            </div>
        </div>
    </div>
</div>