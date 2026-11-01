<div class="bg-gradient-to-br from-gray-900 via-slate-800 to-indigo-950 rounded-2xl p-6 lg:p-8 text-white shadow-2xl relative overflow-hidden border border-slate-700/50">

    <!-- Decorative ambient glows -->
    <div class="absolute -top-24 -right-24 w-64 h-64 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-purple-500/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative z-10">
        
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-slate-700/60 pb-4 mb-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-indigo-600/30 border border-indigo-400/40 flex items-center justify-center text-indigo-300 text-xl shadow-inner">
                    <i class="fa fa-credit-card"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold tracking-tight text-white flex items-center gap-2">
                        Paystack Card Payment
                        @if ($isCardPaymentEnabled)
                            <span class="text-xs bg-indigo-500/20 text-indigo-300 font-medium px-2.5 py-0.5 rounded-full border border-indigo-500/30">Direct Paystack</span>
                        @else
                            <span class="text-xs bg-rose-500/20 text-rose-300 font-medium px-2.5 py-0.5 rounded-full border border-rose-500/30">Currently Disabled</span>
                        @endif
                    </h3>
                    <p class="text-xs text-slate-400">Secure, instant card authorization powered by Paystack</p>
                </div>
            </div>
            
            <div class="flex items-center gap-2 text-slate-400 text-xs bg-slate-800/80 px-3 py-1.5 rounded-lg border border-slate-700">
                <i class="fa fa-lock text-emerald-400"></i> 256-bit Encrypted
            </div>
        </div>

        @if (!$isCardPaymentEnabled)
            <div class="mb-6 bg-amber-500/10 border border-amber-500/30 text-amber-200 px-5 py-4 rounded-xl flex items-start gap-3.5 text-sm">
                <i class="fa fa-ban text-amber-400 text-xl mt-0.5"></i>
                <div class="flex-1">
                    <span class="font-bold text-base block text-amber-300">Card Payment Currently Disabled</span>
                    <span class="text-xs text-amber-200/90 leading-relaxed block mt-1">
                        Card payments through Paystack are currently turned off by administrator. Please use Bank Transfer or your Dedicated Virtual Account above to fund your wallet instantly.
                    </span>
                </div>
            </div>
        @endif

        @if ($errorMessage)
            <div class="mb-6 bg-red-500/10 border border-red-500/40 text-red-300 px-4 py-3 rounded-xl flex items-start gap-3 text-sm">
                <i class="fa fa-exclamation-circle text-red-400 text-lg mt-0.5"></i>
                <div class="flex-1">
                    <span class="font-semibold block">Payment Notice</span>
                    <span>{{ $errorMessage }}</span>
                </div>
            </div>
        @endif

        @if ($successMessage)
            <div class="mb-6 bg-emerald-500/10 border border-emerald-500/40 text-emerald-300 px-4 py-4 rounded-xl flex items-center gap-3 text-sm">
                <i class="fa fa-check-circle text-emerald-400 text-2xl"></i>
                <div>
                    <span class="font-bold text-base block">Success!</span>
                    <span>{{ $successMessage }}</span>
                </div>
            </div>
        @endif

        @if (!$successMessage && $isCardPaymentEnabled)
            
            <!-- Interactive Visual Card Component -->
            <div class="mb-8 max-w-md mx-auto">
                <div class="w-full h-52 rounded-2xl bg-gradient-to-tr from-indigo-900 via-indigo-700 to-purple-800 p-6 flex flex-col justify-between shadow-2xl border border-indigo-400/30 relative overflow-hidden transition-all duration-300 hover:scale-[1.01]">
                    
                    <!-- Card overlay patterns -->
                    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-white/10 via-transparent to-black/20 pointer-events-none"></div>
                    <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full blur-2xl"></div>
                    
                    <div class="flex justify-between items-start relative z-10">
                        <div class="flex items-center gap-2">
                            <!-- EMV Chip Icon -->
                            <div class="w-11 h-8 bg-amber-400/90 rounded-md border border-amber-300 flex items-center justify-center overflow-hidden shadow-sm">
                                <div class="w-full h-full border-t border-b border-amber-600/40 flex flex-col justify-around py-1 px-1">
                                    <div class="h-0.5 bg-amber-700/50 w-full"></div>
                                    <div class="h-0.5 bg-amber-700/50 w-full"></div>
                                </div>
                            </div>
                            <!-- Contactless symbol -->
                            <i class="fa fa-wifi text-white/70 rotate-90 text-sm"></i>
                        </div>

                        <!-- Card Brand Logo -->
                        <div class="h-8 flex items-center">
                            @if ($this->cardBrand === 'visa')
                                <span class="font-black italic text-2xl tracking-tighter text-white drop-shadow">VISA</span>
                            @elseif ($this->cardBrand === 'mastercard')
                                <div class="flex items-center">
                                    <div class="w-7 h-7 bg-red-500 rounded-full opacity-90"></div>
                                    <div class="w-7 h-7 bg-amber-400 rounded-full opacity-90 -ml-4"></div>
                                </div>
                            @elseif ($this->cardBrand === 'verve')
                                <span class="font-black text-xl italic text-emerald-400 tracking-wider">Verve</span>
                            @elseif ($this->cardBrand === 'american-express')
                                <span class="font-extrabold text-lg text-blue-300 tracking-wide">AMEX</span>
                            @else
                                <span class="text-xs uppercase font-bold tracking-widest text-slate-300 bg-white/10 px-2 py-1 rounded">CARD</span>
                            @endif
                        </div>
                    </div>

                    <!-- Card Number Display -->
                    <div class="relative z-10 my-2">
                        <div class="text-xs text-indigo-200/70 font-mono uppercase tracking-wider mb-1">Card Number</div>
                        <div class="text-xl sm:text-2xl font-mono tracking-widest font-semibold text-white drop-shadow">
                            {{ $cardNumber ? wordwrap(str_pad(preg_replace('/\D/', '', $cardNumber), 16, '•'), 4, ' ', true) : '•••• •••• •••• ••••' }}
                        </div>
                    </div>

                    <!-- Footer: Cardholder & Expiry -->
                    <div class="flex justify-between items-end relative z-10">
                        <div class="max-w-[70%]">
                            <div class="text-[10px] text-indigo-200/70 font-mono uppercase tracking-wider">Card Holder</div>
                            <div class="text-sm font-semibold truncate tracking-wide text-white uppercase">
                                {{ $cardHolder ?: 'CARDHOLDER NAME' }}
                            </div>
                        </div>

                        <div>
                            <div class="text-[10px] text-indigo-200/70 font-mono uppercase tracking-wider">Expires</div>
                            <div class="text-sm font-mono font-semibold text-white">
                                {{ $expiryDate ?: 'MM/YY' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Amount Selection Chips -->
            <div class="mb-6">
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Select or Enter Amount (NGN)</label>
                <div class="grid grid-cols-4 gap-2 mb-3">
                    @foreach ([1000, 5000, 10000, 50000] as $preset)
                        <button type="button" 
                                wire:click="selectAmount({{ $preset }})"
                                class="py-2.5 px-3 rounded-xl text-xs font-bold transition-all border {{ $amount == $preset ? 'bg-indigo-600 border-indigo-400 text-white shadow-lg shadow-indigo-600/30' : 'bg-slate-800/80 border-slate-700 text-slate-300 hover:bg-slate-700/70' }}">
                            ₦{{ number_format($preset) }}
                        </button>
                    @endforeach
                </div>

                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 font-bold">₦</span>
                    <input type="number" 
                           wire:model.live="amount" 
                           min="100"
                           class="w-full bg-slate-900/90 border border-slate-700 rounded-xl pl-8 pr-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-semibold"
                           placeholder="Enter custom amount">
                </div>
            </div>

            <!-- Card Input Form -->
            <form wire:submit.prevent="processCardPayment" class="space-y-4">
                
                <!-- Card Number Input -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Card Number</label>
                    <div class="relative">
                        <input type="text" 
                               wire:model.live="cardNumber"
                               maxlength="19"
                               placeholder="1234 5678 9012 3456" 
                               class="w-full bg-slate-900/90 border border-slate-700 rounded-xl pl-4 pr-12 py-3 text-white font-mono placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                            @if ($this->cardBrand === 'visa')
                                <span class="font-black italic text-xs text-indigo-400">VISA</span>
                            @elseif ($this->cardBrand === 'mastercard')
                                <span class="font-black italic text-xs text-amber-400">MC</span>
                            @elseif ($this->cardBrand === 'verve')
                                <span class="font-black italic text-xs text-emerald-400">VERVE</span>
                            @else
                                <i class="fa fa-credit-card"></i>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Expiry Date & CVV -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Expiry Date</label>
                        <input type="text" 
                               wire:model.live="expiryDate"
                               maxlength="5"
                               placeholder="MM/YY" 
                               class="w-full bg-slate-900/90 border border-slate-700 rounded-xl px-4 py-3 text-white font-mono placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-center">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">CVV / CVC</label>
                        <input type="password" 
                               wire:model.live="cvv"
                               maxlength="4"
                               placeholder="•••" 
                               class="w-full bg-slate-900/90 border border-slate-700 rounded-xl px-4 py-3 text-white font-mono placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-center">
                    </div>
                </div>

                <!-- Cardholder Name -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Cardholder Name</label>
                    <input type="text" 
                           wire:model.live="cardHolder"
                           placeholder="Full Name as on Card" 
                           class="w-full bg-slate-900/90 border border-slate-700 rounded-xl px-4 py-3 text-white uppercase placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" 
                            wire:loading.attr="disabled"
                            class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold py-4 rounded-xl shadow-lg shadow-indigo-600/30 transition-all flex items-center justify-center gap-2 text-base active:scale-[0.99] disabled:opacity-50">
                        <span wire:loading.remove wire:target="processCardPayment" class="flex items-center gap-2">
                            <i class="fa fa-lock"></i> Pay ₦{{ number_format((float) $amount, 2) }} with Paystack
                        </span>
                        <span wire:loading wire:target="processCardPayment" class="flex items-center gap-2">
                            <i class="fa fa-spinner fa-spin"></i> Processing Secure Payment...
                        </span>
                    </button>
                </div>
            </form>

            <!-- Standard Paystack Redirect Option / Alternative -->
            <div class="mt-4 pt-4 border-t border-slate-700/60 text-center">
                <form action="{{ route('user.paystack.card') }}" method="POST">
                    @csrf
                    <input type="hidden" name="amount" value="{{ $amount }}">
                    <button type="submit" class="text-xs text-indigo-300 hover:text-indigo-200 underline font-medium inline-flex items-center gap-1">
                        Prefer Paystack Popup Window? Click here <i class="fa fa-external-link text-[10px]"></i>
                    </button>
                </form>
            </div>
        @endif

        <!-- OTP Modal -->
        @if ($requiresOtp)
            <div class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
                <div class="bg-slate-900 border border-indigo-500/40 rounded-2xl p-6 max-w-md w-full shadow-2xl text-white">
                    <div class="text-center mb-4">
                        <div class="w-12 h-12 bg-indigo-600/30 text-indigo-400 rounded-full flex items-center justify-center mx-auto text-xl mb-3 border border-indigo-400/30">
                            <i class="fa fa-key"></i>
                        </div>
                        <h4 class="text-lg font-bold">Enter Authorization OTP</h4>
                        <p class="text-xs text-slate-300 mt-1">{{ $displayText }}</p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <input type="text" 
                                   wire:model="otpCode" 
                                   placeholder="Enter OTP" 
                                   class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-white text-center font-mono text-xl tracking-widest focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>

                        <div class="flex gap-3">
                            <button type="button" 
                                    wire:click="$set('requiresOtp', false)"
                                    class="w-1/2 bg-slate-800 hover:bg-slate-700 text-slate-300 py-3 rounded-xl font-semibold text-sm">
                                Cancel
                            </button>
                            <button type="button" 
                                    wire:click="submitOtp"
                                    wire:loading.attr="disabled"
                                    class="w-1/2 bg-indigo-600 hover:bg-indigo-500 text-white py-3 rounded-xl font-semibold text-sm flex items-center justify-center gap-2">
                                <span wire:loading.remove wire:target="submitOtp">Submit OTP</span>
                                <span wire:loading wire:target="submitOtp"><i class="fa fa-spinner fa-spin"></i></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- PIN Modal -->
        @if ($requiresPin)
            <div class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
                <div class="bg-slate-900 border border-purple-500/40 rounded-2xl p-6 max-w-md w-full shadow-2xl text-white">
                    <div class="text-center mb-4">
                        <div class="w-12 h-12 bg-purple-600/30 text-purple-400 rounded-full flex items-center justify-center mx-auto text-xl mb-3 border border-purple-400/30">
                            <i class="fa fa-shield"></i>
                        </div>
                        <h4 class="text-lg font-bold">Enter Card PIN</h4>
                        <p class="text-xs text-slate-300 mt-1">{{ $displayText }}</p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <input type="password" 
                                   wire:model="pinCode" 
                                   maxlength="4"
                                   placeholder="••••" 
                                   class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-white text-center font-mono text-2xl tracking-widest focus:ring-2 focus:ring-purple-500 focus:outline-none">
                        </div>

                        <div class="flex gap-3">
                            <button type="button" 
                                    wire:click="$set('requiresPin', false)"
                                    class="w-1/2 bg-slate-800 hover:bg-slate-700 text-slate-300 py-3 rounded-xl font-semibold text-sm">
                                Cancel
                            </button>
                            <button type="button" 
                                    wire:click="submitPin"
                                    wire:loading.attr="disabled"
                                    class="w-1/2 bg-purple-600 hover:bg-purple-500 text-white py-3 rounded-xl font-semibold text-sm flex items-center justify-center gap-2">
                                <span wire:loading.remove wire:target="submitPin">Submit PIN</span>
                                <span wire:loading wire:target="submitPin"><i class="fa fa-spinner fa-spin"></i></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- 3D Secure Redirect Modal -->
        @if ($requires3Ds)
            <div class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
                <div class="bg-slate-900 border border-blue-500/40 rounded-2xl p-6 max-w-md w-full shadow-2xl text-white text-center">
                    <div class="w-12 h-12 bg-blue-600/30 text-blue-400 rounded-full flex items-center justify-center mx-auto text-xl mb-3 border border-blue-400/30">
                        <i class="fa fa-globe"></i>
                    </div>
                    <h4 class="text-lg font-bold">3D Secure Authorization</h4>
                    <p class="text-xs text-slate-300 my-3">Your bank requires 3D Secure verification to approve this transaction.</p>
                    
                    <a href="{{ $threeDsUrl }}" target="_blank" class="inline-flex items-center justify-center gap-2 w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-3.5 rounded-xl shadow-lg">
                        Complete Bank Authorization <i class="fa fa-external-link"></i>
                    </a>
                </div>
            </div>
        @endif

    </div>
</div>
