<div class="min-h-screen lg:h-screen">

    {{-- The Livewire loading spinner must be outside the form container --}}
    <x-loading-spinner wire:target='store, firstStepSubmit, secondStepSubmit' />

    {{-- Mobile View: Centered Form --}}
    <div class="p-4 mt-12 lg:p-8 rounded-xl w-full max-w-xl mx-auto lg:hidden">
        {{-- Inject the multi-step form logic here --}}
        @include('livewire.register-steps.multi-step-form')
    </div>

    {{-- Desktop View: Grid Layout (e.g., Form on 1/3, Image/Marketing on 2/3) --}}
    <div class="hidden lg:grid grid-cols-3 h-full">
        
        {{-- Column 1: The Form (Takes 1/3 of the screen) --}}
        <div class="p-6 mt-12 overflow-y-auto"> {{-- Add overflow-y-auto for long forms --}}
            <div class="max-w-xl mx-auto">
                {{-- Inject the multi-step form logic here --}}
                @include('livewire.register-steps.multi-step-form')
            </div>
        </div>

        {{-- Column 2 & 3: Marketing/Image (Takes 2/3 of the screen) --}}
        <div class="col-span-2 bg-gray-100 dark:bg-gray-900 flex items-center justify-center p-12">
            <h1 class="text-4xl font-extrabold text-gray-700 dark:text-gray-300">
                Welcome to Our Platform!
            </h1>
            {{--  --}}
        </div>
    </div>
</div>