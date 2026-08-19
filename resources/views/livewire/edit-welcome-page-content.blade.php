<div class=" max-w-6xl">
    <form action="" wire:submit.prevent='update'>
        <div class=" flex justify-end mb-8">
            <x-primary-button class=" rounded-lg px-8 text-base py-4 w-fit">
                <span class=" fa fa-save mr-2"></span> Save Changes
            </x-primary-button>
        </div>

        <div class=" space-y-4">

            @foreach ($columns as $column)

            @continue(in_array($column, ['id', 'created_at', 'updated_at']))

            <div class=" bg-white border border-gray-300 rounded-xl p-10 grid grid-cols-5 items-center">
                <div class=" col-span-2">
                    <h4 class=" text-lg font-semibold">{{ucfirst(str_replace("_", " ", $column))}}</h4>
                </div>
                <div class=" col-span-3">
                    <textarea name="" id="" rows="4" class=" w-full rounded-md bg-white border border-gray-300 mt-2" wire:model='welcomeContent.{{$column}}' required></textarea>
                </div>
                <x-input-error class="mt-2 " :messages="$errors->get('welcomeContent.{{$column}}')" />
            </div>

            @endforeach
        </div>
    </form>
</div>