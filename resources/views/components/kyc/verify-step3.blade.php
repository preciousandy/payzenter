<div class=" mt-4">
    <h2 class=" text-xl font-semibold md:text-2xl text-center">Capture a Selfie</h2>




    <div class="my-card bg-blue-100 p-4 mt-4 rounded">

        <h3 class=" text-lg font-semibold mb-3">Instructions</h3>
        <ul class=" space-y-2 list-decimal list-inside text-sm">
            <li>

                Your head must be in the middle of the photograph.
            </li>

            <li>

                No sunglasses, head wear or face painting.
            </li>

            <li>

                You must be facing forwards in your photo with a neutral expression (no smiling)
            </li>

            <li>
                Please make sure you are in a well lit area
            </li>


        </ul>
    </div>

    <div class=" grid grid-cols-2 gap-x-4 mt-6">
        <x-secondary-button class="  rounded-xl px-4" type="button" wire:click="$set('currentStep', 2)">
            <span class=" fa fa-chevron-left mr-1"> </span> {{ __('Prev') }}
        </x-secondary-button>
        <button type="button" class=" bg-blue-700 text-white px-4 py-3 text-center block rounded-md " onClick="openWebcam()"><span class=" fa fa-camera mr-2"> </span> Take Photo</button>
    </div>


    <div id="captured_image" class=" hidden mt-6" wire:ignore>
        <div id="results">Your captured image will appear here...</div>
        <div class=" grid grid-cols-2 gap-x-2 mt-4">
            <x-secondary-button onClick="openWebcam()">
                <span class=" fa fa-camera mr-2"></span> {{ __('Retake') }}
            </x-secondary-button>
            <x-primary-button class=" " wire:click='submitStep3'>
                {{ __('Submit ') }}
            </x-primary-button>

        </div>
    </div>

    <div class=" mt-6 p-4 md:p-6 hidden" id="webcam" wire:ignore>
        <div class=" grid lg:grid-cols-2 gap-10">
            <div>
                <div id="my_camera" class=" rounded-md w-full"></div>
                <button type="button" class=" bg-blue-700 text-white px-4 py-3 text-center block rounded-md mt-4" onClick="take_snapshot()">Take Snapshot</button>
                <input type="hidden" name="image" class="image-tag">
            </div>

        </div>

    </div>
</div>