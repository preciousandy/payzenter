<div class="min-h-screen bg-gray-50 px-4 py-10" wire:poll.5s="checkStatus">
    <div class="max-w-lg mx-auto">
        <div class="text-center">
            <div class="text-2xl font-extrabold text-gray-900">{{ config('app.name') }}</div>
            <div class="text-sm text-gray-600 mt-2">Secure Payment Gateway</div>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-8 mt-6">
            <div class="flex items-start justify-between gap-4 border-b border-gray-100 pb-6">
                <div>
                    <div class="text-xs text-gray-500 uppercase tracking-wider font-bold">Merchant</div>
                    <div class="font-bold text-gray-900 text-lg">
                        {{ $tx->merchant?->company_name ?: ($tx->merchant?->name ?: 'Merchant') }}
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-xs text-gray-500 uppercase tracking-wider font-bold">Amount</div>
                    <div class="text-3xl font-black text-primary">
                        ₦{{ formatNumber(($tx->amount ?? 0) / 100) }}
                    </div>
                </div>
            </div>

            @if($step === 'success' || $tx->status === 'success')
                <div class="mt-8 text-center py-10">
                    <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa fa-check text-4xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900">Payment Successful</h3>
                    <p class="text-gray-600 mt-2">Your payment has been confirmed.</p>
                    @if($tx->return_url)
                        <a href="{{ $tx->return_url }}" class="mt-8 inline-block bg-primary text-white px-8 py-3 rounded-xl font-bold">
                            Return to Merchant
                        </a>
                    @endif
                </div>
            @elseif($tx->status === 'closed')
                <div class="mt-8 text-center py-10">
                    <div class="w-20 h-20 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa fa-times text-4xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900">Payment Expired</h3>
                    <p class="text-gray-600 mt-2">This payment session has timed out.</p>
                </div>
            @elseif($step === 'info')
                <div class="mt-8">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Enter your details</h3>
                    <form wire:submit.prevent="setInfo" class="space-y-4">
                        <div>
                            <x-input-label value="Email Address" />
                            <x-text-input wire:model.defer="customer_email" type="email" class="w-full mt-1 h-12" placeholder="email@example.com" required />
                            <x-input-error :messages="$errors->get('customer_email')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label value="Full Name" />
                            <x-text-input wire:model.defer="customer_name" class="w-full mt-1 h-12" placeholder="Your Name" />
                            <x-input-error :messages="$errors->get('customer_name')" class="mt-2" />
                        </div>
                        <x-primary-button class="w-full rounded-xl py-4 text-lg font-bold">
                            Continue
                        </x-primary-button>
                    </form>
                </div>
            @elseif($step === 'method')
                <div class="mt-8">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Choose payment method</h3>
                    <div class="grid grid-cols-1 gap-4">
                        @if($enable_flutterwave || $enable_palmpay)
                        <button wire:click="selectMethod('card')" class="flex items-center justify-between p-4 border border-gray-200 rounded-xl hover:border-primary hover:bg-indigo-50 transition-all group">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-indigo-100 text-primary rounded-lg flex items-center justify-center">
                                    <i class="fa fa-credit-card text-xl"></i>
                                </div>
                                <div class="text-left">
                                    <div class="font-bold text-gray-900">Pay with Card</div>
                                    <div class="text-xs text-gray-500">Visa, Mastercard, Verve</div>
                                </div>
                            </div>
                            <i class="fa fa-chevron-right text-gray-300 group-hover:text-primary"></i>
                        </button>
                        @endif

                        @if($enable_flutterwave || $enable_palmpay)
                        <button wire:click="selectMethod('ussd')" class="flex items-center justify-between p-4 border border-gray-200 rounded-xl hover:border-primary hover:bg-indigo-50 transition-all group">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center">
                                    <i class="fa fa-mobile-alt text-xl"></i>
                                </div>
                                <div class="text-left">
                                    <div class="font-bold text-gray-900">USSD Payment</div>
                                    <div class="text-xs text-gray-500">Pay using bank USSD code</div>
                                </div>
                            </div>
                            <i class="fa fa-chevron-right text-gray-300 group-hover:text-primary"></i>
                        </button>
                        @endif

                        <button wire:click="selectMethod('transfer')" class="flex items-center justify-between p-4 border border-gray-200 rounded-xl hover:border-primary hover:bg-indigo-50 transition-all group">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-green-100 text-green-600 rounded-lg flex items-center justify-center">
                                    <i class="fa fa-university text-xl"></i>
                                </div>
                                <div class="text-left">
                                    <div class="font-bold text-gray-900">Bank Transfer</div>
                                    <div class="text-xs text-gray-500">Pay into a virtual account</div>
                                </div>
                            </div>
                            <i class="fa fa-chevron-right text-gray-300 group-hover:text-primary"></i>
                        </button>


                    </div>
                    <x-input-error :messages="$errors->get('payment_method')" class="mt-4" />
                </div>

            @elseif($step === 'card_form')
                <div class="mt-8">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-900">Card Payment</h3>
                        <button wire:click="goBackToMethod" class="text-sm text-gray-400 hover:text-primary transition-colors">
                            <i class="fa fa-arrow-left mr-1"></i> Back
                        </button>
                    </div>

                    @if($cardError)
                        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                            <i class="fa fa-exclamation-circle mr-2"></i>{{ $cardError }}
                        </div>
                    @endif

                    <form wire:submit.prevent="submitCard" class="space-y-4">
                        <div>
                            <x-input-label value="Card Number" />
                            <div class="relative mt-1">
                                <x-text-input wire:model.defer="card_number" type="text" inputmode="numeric" maxlength="19" class="w-full h-12 pl-12 pr-4" placeholder="0000 0000 0000 0000" required />
                                <i class="fa fa-credit-card absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            </div>
                            <x-input-error :messages="$errors->get('card_number')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label value="Cardholder Name" />
                            <x-text-input wire:model.defer="card_name" type="text" class="w-full mt-1 h-12" placeholder="e.g. John Doe" />
                            <x-input-error :messages="$errors->get('card_name')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label value="Expiry Date" />
                                <div class="grid grid-cols-2 gap-2 mt-1">
                                    <x-text-input wire:model.defer="card_expiry_month" type="text" inputmode="numeric" maxlength="2" class="h-12 text-center" placeholder="MM" required />
                                    <x-text-input wire:model.defer="card_expiry_year" type="text" inputmode="numeric" maxlength="2" class="h-12 text-center" placeholder="YY" required />
                                </div>
                                <x-input-error :messages="$errors->get('card_expiry_month')" class="mt-2" />
                                <x-input-error :messages="$errors->get('card_expiry_year')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label value="CVV" />
                                <div class="relative mt-1">
                                    <x-text-input wire:model.defer="card_cvv" type="password" inputmode="numeric" maxlength="4" class="w-full h-12 pr-12 text-center tracking-widest" placeholder="•••" required />
                                    <i class="fa fa-lock absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                </div>
                                <x-input-error :messages="$errors->get('card_cvv')" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <x-input-label value="Card PIN (Optional, required for some cards)" />
                            <div class="relative mt-1">
                                <x-text-input wire:model.defer="card_pin" type="password" inputmode="numeric" maxlength="4" class="w-full h-12 tracking-widest text-center" placeholder="••••" />
                                <i class="fa fa-shield-alt absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            </div>
                            <x-input-error :messages="$errors->get('card_pin')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-center gap-2 text-xs text-gray-400 pt-2">
                            <i class="fa fa-lock text-green-500"></i>
                            <span>Your card details are secured with 256-bit SSL encryption</span>
                        </div>

                        <x-primary-button class="w-full rounded-xl py-4 text-lg font-bold" :disabled="$processing">
                            <span wire:loading.remove wire:target="submitCard">
                                Pay ₦{{ formatNumber(($tx->charged_amount ?? $tx->amount) / 100) }}
                            </span>
                            <span wire:loading wire:target="submitCard" class="flex items-center justify-center gap-2">
                                <i class="fa fa-spinner fa-spin"></i> Processing...
                            </span>
                        </x-primary-button>
                    </form>
                </div>

            @elseif($step === 'otp_form')
                <div class="mt-8 text-center">
                    <div class="w-16 h-16 bg-indigo-100 text-primary rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa fa-key text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Enter OTP</h3>
                    @if($auth_hint)
                        <p class="text-sm text-gray-600 mb-6">{{ $auth_hint }}</p>
                    @else
                        <p class="text-sm text-gray-600 mb-6">Please enter the OTP sent to your registered phone number or email.</p>
                    @endif

                    @if($otpError)
                        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm text-left">
                            <i class="fa fa-exclamation-circle mr-2"></i>{{ $otpError }}
                        </div>
                    @endif

                    <form wire:submit.prevent="submitOtp" class="space-y-4">
                        <div>
                            <x-text-input wire:model.defer="otp" type="text" inputmode="numeric" maxlength="10" class="w-full h-16 text-center text-2xl font-black tracking-widest" placeholder="000000" required />
                            <x-input-error :messages="$errors->get('otp')" class="mt-2" />
                        </div>

                        <x-primary-button class="w-full rounded-xl py-4 text-lg font-bold" :disabled="$processing">
                            <span wire:loading.remove wire:target="submitOtp">
                                Verify & Pay
                            </span>
                            <span wire:loading wire:target="submitOtp" class="flex items-center justify-center gap-2">
                                <i class="fa fa-spinner fa-spin"></i> Verifying...
                            </span>
                        </x-primary-button>

                        <button type="button" wire:click="goBackToMethod" class="text-sm text-gray-400 hover:text-primary transition-colors">
                            <i class="fa fa-arrow-left mr-1"></i> Cancel and change method
                        </button>
                    </form>
            @elseif($step === 'ussd_form')
                <div class="mt-8">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-900">Pay with USSD</h3>
                        <button wire:click="goBackToMethod" class="text-sm text-gray-400 hover:text-primary transition-colors">
                            <i class="fa fa-arrow-left mr-1"></i> Back
                        </button>
                    </div>

                    @if($ussdError)
                        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                            <i class="fa fa-exclamation-circle mr-2"></i>{{ $ussdError }}
                        </div>
                    @endif

                    <form wire:submit.prevent="submitUssd" class="space-y-4">
                        <div>
                            <x-input-label value="Select Your Bank" />
                            <select wire:model.defer="ussd_bank" class="w-full mt-1 h-12 rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring-primary text-gray-900 font-medium" required>
                                <option value="">Choose bank...</option>
                                @foreach($this->ussdBanks as $bank)
                                    <option value="{{ $bank['code'] }}">{{ $bank['name'] }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('ussd_bank')" class="mt-2" />
                        </div>

                        <x-primary-button class="w-full rounded-xl py-4 text-lg font-bold" :disabled="$processing">
                            <span wire:loading.remove wire:target="submitUssd">
                                Generate USSD Code
                            </span>
                            <span wire:loading wire:target="submitUssd" class="flex items-center justify-center gap-2">
                                <i class="fa fa-spinner fa-spin"></i> Generating...
                            </span>
                        </x-primary-button>
                    </form>
                </div>

            @elseif($step === 'ussd_code')
                <div class="mt-8 text-center">
                    <div class="w-16 h-16 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa fa-phone-alt text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Dial USSD Code</h3>
                    <p class="text-sm text-gray-600 mb-6">Dial the code below on your phone to complete payment.</p>

                    <div class="bg-gray-50 p-6 rounded-2xl border border-dashed border-purple-200 mb-6">
                        <div class="text-xs text-gray-500 uppercase font-bold tracking-widest mb-2">USSD Dial Code</div>
                        <div class="text-3xl font-black text-purple-700 tracking-wider flex items-center justify-center gap-2">
                            {{ $ussd_code }}
                            <button onclick="navigator.clipboard.writeText('{{ $ussd_code }}'); alert('Copied USSD code!')" class="text-xs bg-white p-1.5 rounded-lg border border-gray-200 text-gray-500 hover:text-purple-700 shadow-sm">
                                <i class="fa fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    @if($verificationMessage)
                        <div class="p-4 mb-4 bg-amber-50 border border-amber-200 text-amber-900 rounded-xl text-sm font-medium text-left flex items-start gap-3 shadow-sm">
                            <i class="fa fa-info-circle text-amber-600 text-lg mt-0.5"></i>
                            <span>{{ $verificationMessage }}</span>
                        </div>
                    @endif

                    <button wire:click="verifyPayment" wire:loading.attr="disabled" class="w-full bg-primary hover:bg-indigo-700 text-white font-bold py-4 px-6 rounded-xl shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 text-base cursor-pointer mb-4">
                        <span wire:loading.remove wire:target="verifyPayment" class="flex items-center gap-2">
                            <i class="fa fa-check-circle text-lg"></i> I have completed payment
                        </span>
                        <span wire:loading wire:target="verifyPayment" class="flex items-center justify-center gap-2">
                            <i class="fa fa-spinner fa-spin text-lg"></i> Verifying...
                        </span>
                    </button>

                    <button type="button" wire:click="goBackToMethod" class="text-sm text-gray-400 hover:text-primary transition-colors">
                        <i class="fa fa-arrow-left mr-1"></i> Change payment method
                    </button>
                </div>

            @elseif($step === 'payment' && $payment_method === 'transfer')
                <div class="mt-8 text-center" x-data="{
                    timeLeft: @entangle('timeLeft'),
                    get formattedTime() {
                        const mins = Math.floor(this.timeLeft / 60);
                        const secs = this.timeLeft % 60;
                        return `${mins}:${secs.toString().padStart(2, '0')}`;
                    }
                }" x-init="setInterval(() => { if(timeLeft > 0) timeLeft-- }, 1000)">

                    <div class="bg-amber-50 text-amber-800 p-4 rounded-xl text-sm mb-6 flex items-center justify-center gap-2">
                        <i class="fa fa-clock animate-pulse"></i>
                        <span>Confirm within <span class="font-black" x-text="formattedTime"></span></span>
                    </div>

                    <div class="space-y-6">
                        <p class="text-gray-600 text-sm px-4">
                            Transfer the exact amount to the account details below.
                        </p>

                        <div class="bg-gray-50 p-6 rounded-2xl border border-dashed border-gray-300 relative group">
                            <div class="space-y-4">
                                <div>
                                    <div class="text-xs text-gray-500 uppercase font-bold tracking-widest mb-1">Bank Name</div>
                                    <div class="text-xl font-black text-gray-900 uppercase">{{ $tx->bank_transfer_bank }}</div>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-500 uppercase font-bold tracking-widest mb-1">Account Number</div>
                                    <div class="text-3xl font-black text-primary tracking-tighter flex items-center justify-center gap-2">
                                        {{ $tx->bank_transfer_account }}
                                        <button onclick="navigator.clipboard.writeText('{{ $tx->bank_transfer_account }}'); alert('Copied!')" class="text-xs bg-white p-1 rounded border border-gray-200 text-gray-400 hover:text-primary">
                                            <i class="fa fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-500 uppercase font-bold tracking-widest mb-1">Account Name</div>
                                    <div class="text-sm font-bold text-gray-900">{{ $tx->bank_transfer_name }}</div>
                                </div>
                                <div class="pt-2 border-t border-gray-200">
                                    <div class="text-xs text-gray-500 uppercase font-bold tracking-widest mb-1">Amount to Pay</div>
                                    <div class="text-2xl font-black text-gray-900">₦{{ formatNumber(($tx->charged_amount ?? $tx->amount) / 100) }}</div>
                                </div>
                            </div>
                        </div>

                        @if($verificationMessage)
                            <div class="p-4 bg-amber-50 border border-amber-200 text-amber-900 rounded-xl text-sm font-medium text-left flex items-start gap-3 shadow-sm">
                                <i class="fa fa-info-circle text-amber-600 text-lg mt-0.5"></i>
                                <span>{{ $verificationMessage }}</span>
                            </div>
                        @endif

                        <button wire:click="verifyPayment" wire:loading.attr="disabled" class="w-full bg-primary hover:bg-indigo-700 text-white font-bold py-4 px-6 rounded-xl shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 text-base cursor-pointer">
                            <span wire:loading.remove wire:target="verifyPayment" class="flex items-center gap-2">
                                <i class="fa fa-check-circle text-lg"></i> I have made this payment
                            </span>
                            <span wire:loading wire:target="verifyPayment" class="flex items-center justify-center gap-2">
                                <i class="fa fa-spinner fa-spin text-lg"></i> Verifying payment...
                            </span>
                        </button>

                        <div class="flex items-center justify-center gap-3 py-2">
                            <div class="w-2 h-2 bg-primary rounded-full animate-bounce"></div>
                            <div class="w-2 h-2 bg-primary rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                            <div class="w-2 h-2 bg-primary rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
                            <span class="text-xs text-gray-400 font-bold uppercase tracking-widest">Waiting for payment</span>
                        </div>

                        <button wire:click="goBackToMethod" class="text-sm text-gray-400 hover:text-primary transition-colors">
                            <i class="fa fa-arrow-left mr-1"></i> Change payment method
                        </button>
                    </div>
                </div>
            @endif
        </div>

        <div class="mt-8 text-center">
            <div class="flex items-center justify-center gap-2 text-xs text-gray-400 uppercase tracking-widest font-bold">
                <i class="fa fa-shield-alt text-green-500"></i>
                Secured by {{ config('app.name') }}
            </div>
        </div>
    </div>
</div>
