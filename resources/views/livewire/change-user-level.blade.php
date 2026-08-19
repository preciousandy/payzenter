<div x-data="{showModal: false}">
    <x-secondary-button @click="showModal = !showModal" class=" bg-white py-3">
        <span class=" fa fa-award mr-1"></span> Change Badge
    </x-secondary-button>

    <div x-show="showModal" x-transition x-cloak class=" fixed bg-black/70 z-50 left-0 top-0 w-full h-full flex justify-center items-center p-4">
        <div class=" bg-gray-50 p-4 lg:p-8 rounded-lg w-full md:w-full max-w-xl space-y-5">
            <div class=" flex justify-between items-center mb-6">
                <h2 class=" text-2xl font-semibold">Change {{$user->name}} Badge</h2>
                <button @click="showModal = false">
                    <span class="fa fa-close text-2xl"></span>
                </button>
            </div>

            <div class=" mt-6 space-y-4" x-data="{level: @entangle(
                    'current_level'
                )}">
                @foreach ($levels as $level => $limit)
                <div class=" border border-gray-200 rounded-lg cursor-pointer" wire:click="$set('current_level', '{{$level}}')">
                    <div class="  flex items-center gap-3 px-4 py-8 rounded-lg" :class="level == '{{$level}}' ? 'bg-blue-200 text-primary font-bold' : 'bg-white' ">
                        <img src="{{asset('badges/'. $level  .'.png')}}" alt="" class=" h-10">

                        <h4>
                            {{ucfirst($level)}} Badge
                        </h4>
                    </div>
                </div>
                @endforeach
            </div>

            <x-primary-button wire:click="changeBadge" class=" w-full">
                <span class=" fa fa-save mr-1"></span> Update User Badge
            </x-primary-button>
        </div>
    </div>


</div>