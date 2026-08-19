<div class="bg-white">
    <div class="  min-h-screen relative  py-6 px-4 max-w-3xl">
        <x-blank-page-header title="Customer Care Support" />
        <div class=" mt-6 ">

            <div class="border-2 border-primary rounded-full h-24  w-fit flex items-center justify-center bg-gray-100 mx-auto">
                <img src="{{asset('logo 3.png')}}" alt="" class=" w-24 ">
            </div>
            <h3 class=" font-semibold mb-4 text-center mt-6  text-lg lg:text-xl">
                Our Service
            </h3>
            <div class=" flex justify-end flex-col items-end gap-4 lg: mt-6" x-data="{topic : @entangle('topic')}">
                @foreach ($topics as $item)

                <div wire:click="$set('topic', '{{$item->hash_id}}')" type="button" class="   p-3 rounded-lg border border-gray-300 text-sm lg:text-base md:px-6  text-left flex items-center gap-x-2 cursor-pointer w-fit" :class="topic == '{{$item->hash_id}}' ? ' bg-primary text-white' : 'bg-slate-100'">

                    <div>
                        <h3>
                            {{$item->name}}
                        </h3>

                    </div>

                </div>

                @endforeach
            </div>
        </div>

        @if ($topic)
        <a href="{{route('live-chat', $topic)}}" class=" bg-indigo-50 absolute bottom-0 left-0 right-0 p-3 flex items-center gap-2 lg:relative lg:bg-inherit lg:mt-8">
            <x-primary-button wire:click="startChat" class=" w-full">
                Start <span class="fa fa-arrow-right ml-2"></span>
            </x-primary-button>
        </a>
        @endif
    </div>
</div>
