<div class="p-4 sm:p-6 bg-gray-50 min-h-screen">
    <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <div class="text-2xl font-bold text-gray-900">Email Center</div>
                <div class="text-sm text-gray-600 mt-1">Send a single email or a bulk email</div>
            </div>

            <div class="flex gap-2">
                <button type="button"
                    class="px-4 py-2 rounded-lg border text-sm font-semibold {{ $mode === 'single' ? 'bg-[#1400FB] text-white border-[#1400FB]' : 'bg-white text-gray-800 border-gray-200' }}"
                    wire:click="$set('mode','single')"
                >
                    Single
                </button>
                <button type="button"
                    class="px-4 py-2 rounded-lg border text-sm font-semibold {{ $mode === 'bulk' ? 'bg-[#1400FB] text-white border-[#1400FB]' : 'bg-white text-gray-800 border-gray-200' }}"
                    wire:click="$set('mode','bulk')"
                >
                    Bulk
                </button>
            </div>
        </div>

        <div class="mt-6">
            @if($mode === 'single')
                <div class="grid grid-cols-1 gap-4 max-w-2xl">
                    <div>
                        <x-input-label value="Recipient Email" />
                        <x-text-input wire:model.defer="to_email" class="w-full mt-1" placeholder="customer@example.com" />
                        <x-input-error :messages="$errors->get('to_email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label value="Subject" />
                        <x-text-input wire:model.defer="subject" class="w-full mt-1" />
                        <x-input-error :messages="$errors->get('subject')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label value="Message (HTML supported)" />
                        <textarea wire:model.defer="message" rows="8" class="w-full mt-1 rounded-xl border-gray-300"></textarea>
                        <x-input-error :messages="$errors->get('message')" class="mt-2" />
                    </div>

                    <x-primary-button type="button" wire:click="sendSingle" class="rounded-xl px-6 py-3 w-fit">
                        Send Email
                    </x-primary-button>
                </div>
            @else
                <div class="grid grid-cols-1 gap-4 max-w-2xl">
                    <div>
                        <x-input-label value="Audience" />
                        <select wire:model.defer="audience" class="w-full mt-1 rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="merchants">Merchants</option>
                            <option value="all">All Users</option>
                        </select>
                        <x-input-error :messages="$errors->get('audience')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label value="Subject" />
                        <x-text-input wire:model.defer="subject" class="w-full mt-1" />
                        <x-input-error :messages="$errors->get('subject')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label value="Message (HTML supported)" />
                        <textarea wire:model.defer="message" rows="10" class="w-full mt-1 rounded-xl border-gray-300"></textarea>
                        <x-input-error :messages="$errors->get('message')" class="mt-2" />
                    </div>

                    <x-primary-button type="button" wire:click="sendBulk" class="rounded-xl px-6 py-3 w-fit">
                        Send Bulk Email
                    </x-primary-button>
                </div>
            @endif
        </div>
    </div>
</div>

