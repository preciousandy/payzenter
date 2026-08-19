<div>
    <div class=" flex justify-between items-center">
        <h2 class=" text-xl font-semibold">
            Settings
        </h2>

        <x-primary-button wire:click="save" class=" rounded-xl w-40">
            Save Settings
        </x-primary-button>
    </div>

    <div class="mt-6 bg-white rounded-xl p-4 shadow-sm border border-gray-100">
        <div class="font-semibold text-gray-900">Gateway Providers</div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                <div>
                    <div class="font-medium text-gray-900">Flutterwave</div>
                </div>
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" wire:model.defer="enable_flutterwave" class="sr-only peer">
                    <div class="relative w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-indigo-600"></div>
                </label>
            </div>
            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                <div>
                    <div class="font-medium text-gray-900">PalmPay</div>
                </div>
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" wire:model.defer="enable_palmpay" class="sr-only peer">
                    <div class="relative w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-indigo-600"></div>
                </label>
            </div>
        </div>
        <div class="mt-3">
            <x-primary-button wire:click="saveGatewaySettings" class="rounded-xl w-full md:w-auto">
                Save Gateway Settings
            </x-primary-button>
        </div>
    </div>

    <div class="mt-6 bg-white rounded-xl p-4 shadow-sm border border-gray-100">
        <div class="font-semibold text-gray-900">Add Setting</div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-3">
            <div>
                <x-input-label value="Key" />
                <x-text-input wire:model.defer="new_key" type="text" class="w-full mt-1" />
                <x-input-error :messages="$errors->get('new_key')" class="mt-2" />
            </div>
            <div>
                <x-input-label value="Value" />
                <x-text-input wire:model.defer="new_value" type="text" class="w-full mt-1" />
                <x-input-error :messages="$errors->get('new_value')" class="mt-2" />
            </div>
            <div class="flex items-end">
                <x-primary-button wire:click="addSetting" class="rounded-xl w-full">
                    Add
                </x-primary-button>
            </div>
        </div>
    </div>


    <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-8">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-black  bg-gray-50 dark:bg-gray-700 dark:text-gray-400 uppercase">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        Key
                    </th>

                    <th scope="col" class="px-6 py-3">
                        Value
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Type
                    </th>


                </tr>
            </thead>
            <tbody>
                @foreach ($settings as $setting)

                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-sky-50 dark:hover:bg-gray-600 ">
                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ucfirst(str_replace("_", " ", $setting->key))}}
                    </td>
                    <td class="px-6 py-4 ">
                        <x-text-input wire:model="values.{{$loop->index}}.value" type="text" />
                    </td>
                    <td class="px-6 py-4 ">
                        {{$setting->type}}
                    </td>
                </tr>
                @endforeach


            </tbody>
        </table>


    </div> 
    
    

</div>
