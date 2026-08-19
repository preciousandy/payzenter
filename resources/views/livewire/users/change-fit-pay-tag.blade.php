<div class="p-6 md:p-10 bg-gray-50 min-h-screen flex justify-center items-start lg:items-center" x-data="{showModal : @entangle('showSuccessModal')}">

    <x-loading-spinner wire:target='update' />

    <div class="w-full max-w-lg bg-white rounded-xl shadow-2xl p-8 md:p-10 border border-gray-100 mt-10 lg:mt-0">
        
        <h3 class="text-3xl font-extrabold text-gray-900 tracking-tight">
            Update {{ config('app.name') }} Tag
        </h3>

        <p class="text-gray-500 mt-2 text-lg font-light">
            Your **{{ config('app.name') }} Tag** is your unique identifier for receiving money from other {{ config('app.name') }} users.
        </p>

        <form wire:submit.prevent='update'>
            <div class="mt-8">
                <label for="app-tag-input" class="block text-sm font-medium text-gray-700 mb-2">New {{ config('app.name') }} Tag</label>
                <div class="relative rounded-lg shadow-sm">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                        <x-lucide-at-sign class="h-6 w-6 text-gray-400" />
                    </div>
                    
                    <input 
                        type="text" 
                        id="app-tag-input" 
                        class="block w-full rounded-lg border-gray-300 py-3 pl-12 pr-4 text-gray-900 placeholder-gray-400 
                               focus:ring-2 focus:ring-[#1400FB] focus:border-[#1400FB] border transition duration-150 ease-in-out
                               text-base outline-none" 
                        wire:model.defer='tag' 
                        placeholder="e.g. jessicamust" 
                    />
                </div>
                <x-input-error :messages="$errors->get('tag')" class="mt-2" />
            </div>

            <x-primary-button 
                class="w-full mt-10 py-3 text-xl font-semibold rounded-full 
                       bg-[#1400FB] hover:bg-blue-600 shadow-xl shadow-[#1400FB]/40 transition-all duration-200"
            >
                Save Changes
            </x-primary-button>
        </form>
    </div>


    <div x-cloak x-show="showModal" x-transition.opacity class="fixed inset-0 z-[999] bg-black/70 flex items-center justify-center p-4">

        <div x-transition.duration.500ms.origin.bottom class="bg-white w-full max-w-sm rounded-2xl p-8 shadow-2xl transform scale-100">
            
            <div class="w-24 h-24 m-auto mb-4 bg-green-500 flex justify-center items-center rounded-full shadow-lg shadow-green-500/50">
                <span class="fas fa-check text-white block text-4xl"></span>
            </div>
            
            <div class="mt-4">
                <h4 class="text-2xl font-bold text-center text-gray-900">Tag Updated! 🎉</h4>

                <p class="text-gray-600 text-center text-base mt-2">
                    Let’s go! Your {{ config('app.name') }} tag has been changed successfully. You can now receive payments using your new tag.
                </p>

                <x-primary-button 
                    class="w-full rounded-full mt-8 py-3 text-lg font-semibold 
                           bg-[#1400FB] hover:bg-blue-600 shadow-lg shadow-[#1400FB]/40" 
                    wire:click='goHome'
                >
                    Okay, Go to Home
                </x-primary-button>
            </div>
        </div>
    </div>
</div>