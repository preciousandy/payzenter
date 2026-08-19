<div class=" ">
    <div class=" flex justify-between items-center mb-6">
        <h2 class=" text-2xl font-bold">
            API Keys Management
        </h2>
    </div>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-8">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-black bg-gray-50 dark:bg-gray-700 dark:text-gray-400 uppercase">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        API Service
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Key Value
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Description
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Action
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($apiKeys as $index => $apiKey)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-sky-50 dark:hover:bg-gray-600">
                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ $apiKey['display_name'] }}
                    </td>
                    <td class="px-6 py-4 w-1/3">
                        <x-text-input wire:model="values.{{ $index }}.value" type="password" class="w-full" placeholder="Enter API key..." />
                        <x-input-error :messages="$errors->get('values.' . $index . '.value')" class="mt-1" />
                    </td>
                    <td class="px-6 py-4 w-1/4">
                        <x-text-input wire:model="values.{{ $index }}.description" type="text" class="w-full" placeholder="Enter description..." />
                    </td>
                    <td class="px-6 py-4">
                        <x-primary-button wire:click="updateApiKey({{ $index }})" class="rounded-xl text-xs" wire:loading.attr="disabled" wire:target="updateApiKey({{ $index }})">
                            <span wire:loading.remove wire:target="updateApiKey({{ $index }})">
                                <span class="fa fa-save mr-1"></span> Update
                            </span>
                            <span wire:loading wire:target="updateApiKey({{ $index }})">
                                <span class="fa fa-spinner fa-spin mr-1"></span> Updating...
                            </span>
                        </x-primary-button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded">
        <div class="flex">
            <div class="flex-shrink-0">
                <span class="fa fa-exclamation-triangle text-yellow-400"></span>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    <strong>Security Notice:</strong> API keys are sensitive information. Make sure to use secure keys and never share them publicly. Keys are masked for security.
                </p>
            </div>
        </div>
    </div>
</div>