<div class=" min-h-screen  bg-white flex flex-col justify-center  px-4 py-8">

    <div class=" space-y-8 max-w-2xl mx-auto">
        <span class="fa fa-thumbs-up text-primary block w-fit mx-auto text-7xl"></span>

        <h1 class=" text-xl font-bold text-center ">Ajo Contribution Created</h1>

        <p class=" text-center text-lg">
            use the code below to invite your friends to join the group
        </p>


        <div>
            <p class=" border-primary p-2 rounded-lg border-2  text-center w-11/12 mx-auto font-semibold text-lg">
                {{$ajoContribution->hash_id}}
            </p>

            <x-primary-button class=" mt-4 mx-auto block w-11/12 py-3" onclick="copyToClipboard('{{$ajoContribution->hash_id}}')">
                <i class="fa fa-copy mr-1"></i>
                <span class="copy">Copy Code</span>
            </x-primary-button>
            <x-primary-button class=" mt-4 mx-auto block w-11/12 py-3" onclick="copyToClipboard('{{route('ajo-contributions.join', ['code' => $ajoContribution->hash_id])}}')">
                <i class="fa fa-link mr-1"></i>
                <span id="copy">Copy Link</span>
            </x-primary-button>
        </div>


        <a href=" {{route('ajo-contributions.show', $ajoContribution->hash_id )}}" class=" block">
            <x-secondary-button class=" mt-4 mx-auto block w-11/12 py-3">Proceed to Ajo Contribution</x-secondary-button>
        </a>
    </div>

</div>