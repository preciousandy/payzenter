<div x-data="{showModal: false}">
    <x-secondary-button @click="showModal = !showModal" class=" bg-white py-3">
        <span class=" fa fa-user-lock mr-1"></span> Change Account Status
    </x-secondary-button>

    <div x-show="showModal" x-transition x-cloak class=" fixed bg-black/70 z-50 left-0 top-0 w-full h-full flex justify-center items-center p-4">
        <div class=" bg-gray-50 p-4 lg:p-8 rounded-lg w-full md:w-full max-w-xl space-y-5">
            <div class=" flex justify-between items-center mb-6">
                <h2 class=" text-2xl font-semibold">Change {{$user->name}} Account Status</h2>
                <button @click="showModal = false">
                    <span class="fa fa-close text-2xl"></span>
                </button>
            </div>

            {{-- <div class=" bg-yellow-700 suspended blocked deleted"></div> --}}

            <div class=" mt-6 space-y-4" x-data="{status: @entangle('status')}">
                @foreach ($account_statuses as $key => $statusInfo)
                <div class=" border border-gray-200 rounded-lg cursor-pointer" wire:click="$set('status', '{{$key}}')">
                    <div class="   flex items-center gap-3 px-4 py-8 rounded-lg" :class="status == '{{$key}}' ? 'bg-blue-200 bg-{{$statusInfo['color']}}-700 font-bold text-white' : 'bg-white' ">
                        <span class=" {{$statusInfo['icon']}}"></span>

                        <h4>
                            {{$statusInfo['name']}}
                        </h4>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Suspension Duration Fields -->
            <div x-show="@this.status === 'suspended'" x-transition class="space-y-4">
                <div>
                    <x-input-label for="suspensionDuration" :value="__('Suspension Duration (Days)')" />
                    <x-text-input id="suspensionDuration" class="block mt-1 w-full" type="number" wire:model="suspensionDuration" min="1" max="365" placeholder="Enter number of days (1-365)" />
                    <x-input-error :messages="$errors->get('suspensionDuration')" class="mt-1" />
                </div>

                <div>
                    <x-input-label for="suspensionReason" :value="__('Suspension Reason')" />
                    <textarea id="suspensionReason" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" wire:model="suspensionReason" rows="3" placeholder="Enter reason for suspension..."></textarea>
                    <x-input-error :messages="$errors->get('suspensionReason')" class="mt-1" />
                </div>
            </div>

            <!-- Block Reason Field -->
            <div x-show="@this.status === 'blocked'" x-transition class="space-y-4">
                <div>
                    <x-input-label for="blockReason" :value="__('Block Reason')" />
                    <textarea id="blockReason" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" wire:model="blockReason" rows="3" placeholder="Enter reason for blocking the account..."></textarea>
                    <x-input-error :messages="$errors->get('blockReason')" class="mt-1" />
                </div>
            </div>

            <x-primary-button wire:click="changeStatus" class=" w-full">
                <span class=" fa fa-save mr-1"></span> Update User Account Status
            </x-primary-button>
        </div>
    </div>


</div>