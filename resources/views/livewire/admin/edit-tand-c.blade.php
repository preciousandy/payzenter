<div class="max-w-6xl">
    <div class=" flex justify-between items-center mb-6">
        <h2 class=" text-lg font-medium">
            Edit Terms & Conditions Content
        </h2>

        <x-primary-button wire:click='submit' class=" rounded-lg px-8 w-fit">
            <span class=" fa fa-save mr-2"></span> Save Changes
        </x-primary-button>
    </div>

    <div id="editor">
        <livewire:jodit-text-editor wire:model.live="content" />
        <x-input-error class="mt-2" :messages="$errors->get('content')" />
    </div>
</div>

@push('styles')
<style>
    #editor ol,
    #editor ul {
        list-style-type: disc !important;
        margin-left: 1.5rem !important;
        margin-bottom: 1rem !important;
        padding-left: 1rem !important;
    }

    #editor ol {
        list-style-type: decimal !important;
    }

    #editor li {
        margin-bottom: 0.5rem !important;
        padding-left: 0.5rem !important;
    }

    #editor ol ol,
    #editor ul ul {
        margin-top: 0.5rem !important;
        margin-bottom: 0.5rem !important;
    }
</style>
@endpush