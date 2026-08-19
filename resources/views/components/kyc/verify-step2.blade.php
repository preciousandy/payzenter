@props(['documentTypes', 'document_front_upload'])

<div class=" mt-6 ">
    <div>
        <x-input-label for="phone" :value="__('Document type')" />

        <select wire:model="document_type" id="" class=" p-3 w-full rounded-lg border-gray-300 mt-2" required>
            <option value="">Select type</option>
            @foreach ($documentTypes as $key => $name)
            <option value="{{ $key }}">{{ $name }}</option>
            @endforeach

        </select>
        <x-input-error class="mt-2" :messages="$errors->get('document_type')" />
    </div>
    <div class=" mt-4">
        <x-input-label for="document_number" :value="__('Document Number')" />
        <x-text-input id="document_number" class="block mt-2 w-full placeholder:text-gray-400" type="text" wire:model='document_number' required />
    </div>

    <div class=" mt-4">
        <p class=" mb-1">
            Upload the front of the document
        </p>

        @if ($document_front_upload)
        <div>
            <img src="{{ $document_front_upload?->temporaryUrl() }}" alt="Preview" style="max-width: 200px;">
            {{-- {{$document_front_upload}} --}}
        </div>
        @endif

        <div class=" relative">
            <img src="{{asset('card-front.png')}}" alt="" class=" w-full ">
            <label for="card-front-upload" class=" bg-primary h-14 w-14 rounded-full flex items-center justify-center text-white absolute right-2 -bottom-6">
                <span class=" fa fa-camera text-2xl"></span>
            </label>
        </div>
        <x-text-input id="card-front-upload" type="file" class=" block w-full disabled:bg-gray-100 disabled:cursor-not-allowed border-0 mt-2" wire:model='document_front_upload' required />
        <x-input-error :messages="$errors->get('document_front_upload')" class="mt-2" />
    </div>

    <div class=" mt-8">
        <p class=" mb-1">
            Upload the back of the document
        </p>

        <div class=" relative">
            <img src="{{asset('card-back.png')}}" alt="" class=" w-full ">
            <label for="card-back-upload" class=" bg-primary h-14 w-14 rounded-full flex items-center justify-center text-white absolute right-2 -bottom-6">
                <span class=" fa fa-camera text-2xl"></span>
            </label>
        </div>
        <x-text-input id="card-back-upload" type="file" class=" block w-full disabled:bg-gray-100 disabled:cursor-not-allowed border-0 mt-2" wire:model='document_back_upload' required />
        <x-input-error :messages="$errors->get('document_back_upload')" class="mt-2" />
    </div>



    <div class=" flex justify-end mt-10">
        <x-primary-button class="  rounded-xl w-36" wire:click='submitStep2'>
            {{ __('Next') }} <span class=" fa fa-chevron-right ml-1"> </span>
        </x-primary-button>
    </div>





</div>