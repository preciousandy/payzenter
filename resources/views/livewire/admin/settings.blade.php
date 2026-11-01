<div>
    <style>
        .provider-btn {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            padding: 6px 14px !important;
            border-radius: 8px !important;
            font-size: 12px !important;
            font-weight: 700 !important;
            line-height: 1.2 !important;
            cursor: pointer !important;
            transition: all 0.15s ease-in-out !important;
            text-decoration: none !important;
            outline: none !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
        }
        .provider-btn-disable {
            background-color: #fee2e2 !important;
            color: #dc2626 !important;
            border: 1px solid #fca5a5 !important;
        }
        .provider-btn-disable:hover {
            background-color: #fecaca !important;
            color: #b91c1c !important;
            border-color: #f87171 !important;
        }
        .provider-btn-disable i, .provider-btn-disable span {
            color: #dc2626 !important;
        }
        .provider-btn-enable {
            background-color: #16a34a !important;
            color: #ffffff !important;
            border: 1px solid #15803d !important;
        }
        .provider-btn-enable:hover {
            background-color: #15803d !important;
            color: #ffffff !important;
        }
        .provider-btn-enable i, .provider-btn-enable span {
            color: #ffffff !important;
        }
        .provider-badge-enabled {
            display: inline-flex !important;
            align-items: center !important;
            padding: 2px 8px !important;
            border-radius: 6px !important;
            font-size: 10px !important;
            font-weight: 800 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            background-color: #dcfce7 !important;
            color: #15803d !important;
            border: 1px solid #86efac !important;
        }
        .provider-badge-disabled {
            display: inline-flex !important;
            align-items: center !important;
            padding: 2px 8px !important;
            border-radius: 6px !important;
            font-size: 10px !important;
            font-weight: 800 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            background-color: #fee2e2 !important;
            color: #b91c1c !important;
            border: 1px solid #fca5a5 !important;
        }
        .provider-card-enabled {
            border: 1px solid #a7f3d0 !important;
            background-color: #f0fdf4 !important;
        }
        .provider-card-disabled {
            border: 1px solid #e2e8f0 !important;
            background-color: #f8fafc !important;
        }
    </style>

    <div class=" flex justify-between items-center">
        <h2 class=" text-xl font-semibold">
            Settings
        </h2>

        <x-primary-button wire:click="save" class=" rounded-xl w-40">
            Save Settings
        </x-primary-button>
    </div>

    <div class="mt-6 bg-white rounded-xl p-6 shadow-sm border border-gray-100 space-y-6">
        <div>
            <div class="font-semibold text-gray-900 text-lg">Gateway & Virtual Account Providers</div>
            <p class="text-xs text-gray-500 mt-1">Enable or disable virtual account providers and control which provider third-party users/merchants use.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- PalmPay Provider Toggle -->
            <div class="flex items-center justify-between p-4 rounded-xl transition {{ $enable_palmpay ? 'provider-card-enabled' : 'provider-card-disabled' }}">
                <div>
                    <div class="font-bold text-gray-900 flex items-center gap-1.5">
                        <span>PalmPay</span>
                        @if($enable_palmpay)
                            <span class="provider-badge-enabled">Enabled</span>
                        @else
                            <span class="provider-badge-disabled">Disabled</span>
                        @endif
                    </div>
                    <div class="text-xs text-gray-500 mt-0.5">Virtual Accounts & Payout</div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" 
                        wire:click="togglePalmPay"
                        class="provider-btn {{ $enable_palmpay ? 'provider-btn-disable' : 'provider-btn-enable' }}"
                        style="{{ $enable_palmpay ? 'background-color: #fee2e2 !important; color: #dc2626 !important; border: 1px solid #fca5a5 !important;' : 'background-color: #16a34a !important; color: #ffffff !important; border: 1px solid #15803d !important;' }}"
                        title="{{ $enable_palmpay ? 'Click to disable PalmPay' : 'Click to enable PalmPay' }}">
                        @if($enable_palmpay)
                            <i class="fa fa-ban" style="font-size: 11px; color: #dc2626 !important;"></i>
                            <span style="color: #dc2626 !important;">Disable</span>
                        @else
                            <i class="fa fa-check" style="font-size: 11px; color: #ffffff !important;"></i>
                            <span style="color: #ffffff !important;">Enable</span>
                        @endif
                    </button>
                    <label class="relative inline-flex items-center cursor-pointer" title="{{ $enable_palmpay ? 'Click to disable' : 'Click to enable' }}">
                        <input type="checkbox" wire:click="togglePalmPay" {{ $enable_palmpay ? 'checked' : '' }} class="sr-only">
                        <div style="{{ $enable_palmpay ? 'background-color: #16a34a;' : 'background-color: #cbd5e1;' }}" class="w-11 h-6 rounded-full transition-colors relative">
                            <span style="{{ $enable_palmpay ? 'transform: translateX(20px);' : 'transform: translateX(2px);' }}" class="absolute top-[2px] left-0 w-5 h-5 bg-white rounded-full transition-transform shadow-xs"></span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Udara360 Provider Toggle -->
            <div class="flex items-center justify-between p-4 rounded-xl transition {{ $enable_udara360 ? 'provider-card-enabled' : 'provider-card-disabled' }}">
                <div>
                    <div class="font-bold text-gray-900 flex items-center gap-1.5">
                        <span>Udara360</span>
                        @if($enable_udara360)
                            <span class="provider-badge-enabled">Enabled</span>
                        @else
                            <span class="provider-badge-disabled">Disabled</span>
                        @endif
                    </div>
                    <div class="text-xs text-gray-500 mt-0.5">Virtual Accounts API</div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" 
                        wire:click="toggleUdara360"
                        class="provider-btn {{ $enable_udara360 ? 'provider-btn-disable' : 'provider-btn-enable' }}"
                        style="{{ $enable_udara360 ? 'background-color: #fee2e2 !important; color: #dc2626 !important; border: 1px solid #fca5a5 !important;' : 'background-color: #16a34a !important; color: #ffffff !important; border: 1px solid #15803d !important;' }}"
                        title="{{ $enable_udara360 ? 'Click to disable Udara360' : 'Click to enable Udara360' }}">
                        @if($enable_udara360)
                            <i class="fa fa-ban" style="font-size: 11px; color: #dc2626 !important;"></i>
                            <span style="color: #dc2626 !important;">Disable</span>
                        @else
                            <i class="fa fa-check" style="font-size: 11px; color: #ffffff !important;"></i>
                            <span style="color: #ffffff !important;">Enable</span>
                        @endif
                    </button>
                    <label class="relative inline-flex items-center cursor-pointer" title="{{ $enable_udara360 ? 'Click to disable' : 'Click to enable' }}">
                        <input type="checkbox" wire:click="toggleUdara360" {{ $enable_udara360 ? 'checked' : '' }} class="sr-only">
                        <div style="{{ $enable_udara360 ? 'background-color: #16a34a;' : 'background-color: #cbd5e1;' }}" class="w-11 h-6 rounded-full transition-colors relative">
                            <span style="{{ $enable_udara360 ? 'transform: translateX(20px);' : 'transform: translateX(2px);' }}" class="absolute top-[2px] left-0 w-5 h-5 bg-white rounded-full transition-transform shadow-xs"></span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Flutterwave Provider Toggle -->
            <div class="flex items-center justify-between p-4 rounded-xl transition {{ $enable_flutterwave ? 'provider-card-enabled' : 'provider-card-disabled' }}">
                <div>
                    <div class="font-bold text-gray-900 flex items-center gap-1.5">
                        <span>Flutterwave</span>
                        @if($enable_flutterwave)
                            <span class="provider-badge-enabled">Enabled</span>
                        @else
                            <span class="provider-badge-disabled">Disabled</span>
                        @endif
                    </div>
                    <div class="text-xs text-gray-500 mt-0.5">Hosted Checkout & Card</div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" 
                        wire:click="toggleFlutterwave"
                        class="provider-btn {{ $enable_flutterwave ? 'provider-btn-disable' : 'provider-btn-enable' }}"
                        style="{{ $enable_flutterwave ? 'background-color: #fee2e2 !important; color: #dc2626 !important; border: 1px solid #fca5a5 !important;' : 'background-color: #16a34a !important; color: #ffffff !important; border: 1px solid #15803d !important;' }}"
                        title="{{ $enable_flutterwave ? 'Click to disable Flutterwave' : 'Click to enable Flutterwave' }}">
                        @if($enable_flutterwave)
                            <i class="fa fa-ban" style="font-size: 11px; color: #dc2626 !important;"></i>
                            <span style="color: #dc2626 !important;">Disable</span>
                        @else
                            <i class="fa fa-check" style="font-size: 11px; color: #ffffff !important;"></i>
                            <span style="color: #ffffff !important;">Enable</span>
                        @endif
                    </button>
                    <label class="relative inline-flex items-center cursor-pointer" title="{{ $enable_flutterwave ? 'Click to disable' : 'Click to enable' }}">
                        <input type="checkbox" wire:click="toggleFlutterwave" {{ $enable_flutterwave ? 'checked' : '' }} class="sr-only">
                        <div style="{{ $enable_flutterwave ? 'background-color: #16a34a;' : 'background-color: #cbd5e1;' }}" class="w-11 h-6 rounded-full transition-colors relative">
                            <span style="{{ $enable_flutterwave ? 'transform: translateX(20px);' : 'transform: translateX(2px);' }}" class="absolute top-[2px] left-0 w-5 h-5 bg-white rounded-full transition-transform shadow-xs"></span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Paystack Card Payment Enable/Disable Toggle -->
            <div class="flex items-center justify-between p-4 rounded-xl transition {{ $enable_paystack_card ? 'provider-card-enabled' : 'provider-card-disabled' }}">
                <div>
                    <div class="font-bold text-gray-900 flex items-center gap-1.5">
                        <span>Paystack Card</span>
                        @if($enable_paystack_card)
                            <span class="provider-badge-enabled">Enabled</span>
                        @else
                            <span class="provider-badge-disabled">Disabled</span>
                        @endif
                    </div>
                    <div class="text-xs text-gray-500 mt-0.5">Direct Card Payments</div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" 
                        wire:click="togglePaystackCard"
                        class="provider-btn {{ $enable_paystack_card ? 'provider-btn-disable' : 'provider-btn-enable' }}"
                        style="{{ $enable_paystack_card ? 'background-color: #fee2e2 !important; color: #dc2626 !important; border: 1px solid #fca5a5 !important;' : 'background-color: #16a34a !important; color: #ffffff !important; border: 1px solid #15803d !important;' }}"
                        title="{{ $enable_paystack_card ? 'Click to disable Paystack card payment' : 'Click to enable Paystack card payment' }}">
                        @if($enable_paystack_card)
                            <i class="fa fa-ban" style="font-size: 11px; color: #dc2626 !important;"></i>
                            <span style="color: #dc2626 !important;">Disable</span>
                        @else
                            <i class="fa fa-check" style="font-size: 11px; color: #ffffff !important;"></i>
                            <span style="color: #ffffff !important;">Enable</span>
                        @endif
                    </button>
                    <label class="relative inline-flex items-center cursor-pointer" title="{{ $enable_paystack_card ? 'Click to disable' : 'Click to enable' }}">
                        <input type="checkbox" wire:click="togglePaystackCard" {{ $enable_paystack_card ? 'checked' : '' }} class="sr-only">
                        <div style="{{ $enable_paystack_card ? 'background-color: #16a34a;' : 'background-color: #cbd5e1;' }}" class="w-11 h-6 rounded-full transition-colors relative">
                            <span style="{{ $enable_paystack_card ? 'transform: translateX(20px);' : 'transform: translateX(2px);' }}" class="absolute top-[2px] left-0 w-5 h-5 bg-white rounded-full transition-transform shadow-xs"></span>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-100 pt-4">
            <x-input-label value="Default Virtual Account Provider (For Third-Party Users & Checkout)" class="font-semibold text-gray-800" />
            <p class="text-xs text-gray-500 mb-2">When third-party API users generate virtual accounts without specifying a provider, this provider will be used.</p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <label class="flex items-center gap-2 p-3 border rounded-xl cursor-pointer transition {{ $enable_palmpay ? 'border-gray-200 hover:border-gray-300' : 'opacity-50 cursor-not-allowed bg-gray-50' }}">
                    <input type="radio" wire:model.defer="default_va_provider" value="palmpay" @disabled(!$enable_palmpay) class="text-indigo-600 focus:ring-indigo-500">
                    <span class="font-medium text-sm text-gray-800">PalmPay {{ !$enable_palmpay ? '(Disabled)' : '(Default)' }}</span>
                </label>
                <label class="flex items-center gap-2 p-3 border rounded-xl cursor-pointer transition {{ $enable_udara360 ? 'border-gray-200 hover:border-gray-300' : 'opacity-50 cursor-not-allowed bg-gray-50' }}">
                    <input type="radio" wire:model.defer="default_va_provider" value="udara360" @disabled(!$enable_udara360) class="text-indigo-600 focus:ring-indigo-500">
                    <span class="font-medium text-sm text-gray-800">Udara360 {{ !$enable_udara360 ? '(Disabled)' : '' }}</span>
                </label>
                <label class="flex items-center gap-2 p-3 border rounded-xl cursor-pointer transition {{ $enable_flutterwave ? 'border-gray-200 hover:border-gray-300' : 'opacity-50 cursor-not-allowed bg-gray-50' }}">
                    <input type="radio" wire:model.defer="default_va_provider" value="flutterwave" @disabled(!$enable_flutterwave) class="text-indigo-600 focus:ring-indigo-500">
                    <span class="font-medium text-sm text-gray-800">Flutterwave {{ !$enable_flutterwave ? '(Disabled)' : '' }}</span>
                </label>
            </div>
        </div>

        <!-- Udara360 API Credentials & Configuration -->
        <div class="border-t border-gray-100 pt-4 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <div class="font-semibold text-gray-900 text-sm">Udara360 API Credentials & Settings</div>
                    <p class="text-xs text-gray-500">Configure credentials from your Udara360 CBA Portal.</p>
                </div>
                <div class="text-xs bg-slate-100 text-slate-700 px-3 py-1.5 rounded-lg font-mono">
                    Webhook URL: <span class="font-semibold text-indigo-600">{{ url('/webhook/udara360') }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <x-input-label value="Udara360 Base URL" />
                    <x-text-input wire:model.defer="udara360_base_url" type="text" placeholder="https://openapi.udara360.io" class="w-full mt-1 text-sm font-mono" />
                    <x-input-error :messages="$errors->get('udara360_base_url')" class="mt-1" />
                </div>
                <div>
                    <x-input-label value="Client ID" />
                    <x-text-input wire:model.defer="udara360_client_id" type="text" placeholder="Enter Client ID" class="w-full mt-1 text-sm font-mono" />
                    <x-input-error :messages="$errors->get('udara360_client_id')" class="mt-1" />
                </div>
                <div>
                    <x-input-label value="Client Secret" />
                    <x-text-input wire:model.defer="udara360_client_secret" type="password" placeholder="Enter Client Secret" class="w-full mt-1 text-sm font-mono" />
                    <x-input-error :messages="$errors->get('udara360_client_secret')" class="mt-1" />
                </div>
                <div>
                    <x-input-label value="Parent Account Number" />
                    <x-text-input wire:model.defer="udara360_parent_account" type="text" placeholder="Corporate/Parent Account No." class="w-full mt-1 text-sm font-mono" />
                    <x-input-error :messages="$errors->get('udara360_parent_account')" class="mt-1" />
                </div>
                <div>
                    <x-input-label value="Product Code (e.g. 301)" />
                    <x-text-input wire:model.defer="udara360_product_code" type="text" placeholder="301" class="w-full mt-1 text-sm font-mono" />
                    <x-input-error :messages="$errors->get('udara360_product_code')" class="mt-1" />
                </div>
                <div>
                    <x-input-label value="Virtual Account Bank Name" />
                    <x-text-input wire:model.defer="udara360_bank_name" type="text" placeholder="Providus Bank" class="w-full mt-1 text-sm" />
                    <x-input-error :messages="$errors->get('udara360_bank_name')" class="mt-1" />
                </div>
                <div>
                    <x-input-label value="Virtual Account Bank Code" />
                    <x-text-input wire:model.defer="udara360_bank_code" type="text" placeholder="000023" class="w-full mt-1 text-sm font-mono" />
                    <x-input-error :messages="$errors->get('udara360_bank_code')" class="mt-1" />
                </div>
                <div>
                    <x-input-label value="Webhook Secret / Auth Token (Optional)" />
                    <x-text-input wire:model.defer="udara360_webhook_secret" type="password" placeholder="Optional webhook verification secret" class="w-full mt-1 text-sm font-mono" />
                    <x-input-error :messages="$errors->get('udara360_webhook_secret')" class="mt-1" />
                </div>
            </div>
        </div>

        <div class="pt-2">
            <x-primary-button wire:click="saveGatewaySettings" class="rounded-xl px-6 py-2.5">
                Save Gateway & Udara360 Settings
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
