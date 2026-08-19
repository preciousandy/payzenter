<form wire:submit.prevent='@if($currentStep == 1) firstStepSubmit @else store @endif' class="w-full space-y-6">

    <div class="space-y-4 text-center">
        <img src="{{asset('logo/pzwhlogo.png')}}" alt="Logo" class="h-20 lg:h-28 mx-auto">
        <h3 class="text-2xl font-bold pt-2 lg:text-3xl">
            {{ $currentStep == 1 ? 'Create Account' : 'Address Information' }}
        </h3>
        <p class="text-sm text-gray-500">Step {{ $currentStep }} of 2</p>
    </div>

    @csrf

    {{-- STEP 1: User Information --}}
    @if ($currentStep == 1)
        @include('livewire.register-steps.step1-user-info')

        <x-primary-button type="button" wire:click="firstStepSubmit" class="w-full mt-4 rounded-xl py-3 text-lg font-semibold">
            Next: Address Info
        </x-primary-button>
    @endif

    {{-- STEP 2: Address Information --}}
    @if ($currentStep == 2)
        @include('livewire.register-steps.step2-address-info')
        
        <div class="flex justify-between gap-4 mt-6">
            <x-secondary-button type="button" wire:click="back(1)" class="flex-1 rounded-xl py-3 text-lg">
                Back
            </x-secondary-button>
            <x-primary-button class="flex-1 rounded-xl py-3 text-lg font-semibold">
                Complete Registration
            </x-primary-button>
        </div>
    @endif
    
    <p class="text-center mt-6 text-gray-600 dark:text-gray-400 lg:text-base">
        Already have an account? 
        <a href="{{ route('login') }}" class="text-primary font-bold hover:underline">Login!</a>
    </p>
</form>