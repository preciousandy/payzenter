<div class="bg-white">
    <div class=" min-h-screen   flex flex-col justify-center  px-4 py-8 max-w-2xl mx-auto  ">

        <div class=" space-y-6 lg:space-y-8">
            <span class="fa fa-thumbs-up text-primary block w-fit mx-auto text-7xl lg:text-9xl"></span>

            <h1 class=" text-xl lg:text-3xl font-bold text-center ">Group Savings Created</h1>

            <p class=" text-center md:text-lg text-gray-700">
                use the code below to invite your friends to join the group
            </p>

            <div>
                <p class=" border-primary p-2 rounded-lg border-2  text-center w-11/12 mx-auto font-semibold text-lg lg:p-3 lg:text-xl">
                    {{$groupSavings->hash_id}}
                </p>

                <x-primary-button class=" mt-2 mx-auto block w-11/12 " onclick="copyToClipboard('{{$groupSavings->hash_id}}')">
                    <i class="fa fa-copy mr-1"></i>
                    <span class="copy">Copy Code</span>
                </x-primary-button>

                <p class=" border-primary p-2 rounded-lg border-2  text-center w-11/12 mx-auto text-sm overflow-hidden whitespace-nowrap overflow-ellipsis text-gray-600 mt-4 md:mt-6 lg:mt-8 md:text-base lg:p-3 lg:text-lg font-medium ">
                    {{route('savings.group-savings.invite', $groupSavings->hash_id)}}
                </p>


                <x-primary-button class=" mt-2 mx-auto block w-11/12 " onclick="copyToClipboard('{{route('savings.group-savings.invite', $groupSavings->hash_id)}}')">
                    <span class=" fa fa-link mr-1"></span>
                    <span id="copy">Copy Link</span>
                </x-primary-button>
            </div>


            <a href="{{route('savings.group-savings.show', $groupSavings->hash_id )}}" class=" block ">
                <x-secondary-button class="  mx-auto block w-11/12 py-3">Proceed to Group</x-secondary-button>
            </a>
        </div>

    </div>
</div>