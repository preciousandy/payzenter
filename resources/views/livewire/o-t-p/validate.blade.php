<div class=" px-4 py-6 max-w-2xl mx-auto lg:p-6">
    <x-loading-spinner wire:target='verifyOTP' />
    <h3 class=" text-lg font-semibold lg:text-xl lg:text-center">
        Enter Code
    </h3>

    <p class=" text-gray-600 mt-2 lg:text-lg lg:text-center">
        Enter the code sent to your email address <b>({{mask_email(auth()->user()->email)}})</b> to verify it is you before you can proceed.
    </p>

    <form wire:submit.prevent='verifyOTP'>


        <div class=" mt-4">
            <div x-data="{
                                            focusNext(el, nextId) {
                                                if (el.value.length === 1) {
                                                    const next = document.getElementById(nextId);
                                                    if (next) {
                                                        next.focus();
                                                    }
                                                }
                                            },
                                            focusPrev(el, prevId) {
                                                if (el.value.length === 0) {
                                                    const prev = document.getElementById(prevId);
                                                    if (prev) {
                                                        prev.focus();
                                                    }
                                                }
                                            },
                                            initPin() {
                                                $watch('$wire.pin', value => {
                                                    const digits = value.split('');
                                                    for (let i = 0; i < 6; i++) {
                                                        const input = document.getElementById(`pin-${i + 1}`);
                                                        input.value = digits[i] || '';
                                                    }
                                                });
                                            }
                                        }" x-init="initPin()" class="flex flex-col items-center justify-center  bg-gray-50 rounded-xl">
                <div class="p-4">
                    <h2 class="mb-2 text-base font-semibold text-center  text-gray-700">Enter Code</h2>
                    <div class="flex  space-x-4">
                        <template x-for="i in 6" :key="i">
                            <input type="password" :id="'pin-' + i" maxlength="1" class="w-10 h-10 text-center text-xl font-semibold border-2 border-gray-300 rounded-lg focus:border-primary focus:ring-2 focus:ring-primary focus:outline-none" x-on:input="
                                                            $wire.pin = Array.from(document.querySelectorAll('input[id^=pin-]')).map(input => input.value).join('');
                                                            focusNext($el, 'pin-' + (i + 1))
                                                        " x-on:keydown.backspace="focusPrev($el, 'pin-' + (i - 1))" x-on:keydown="if (!/^[0-9]$/.test($event.key) && !['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab'].includes($event.key)) $event.preventDefault()" inputmode="numeric" pattern="[0-9]*">
                        </template>
                    </div>


                </div>
            </div>
        </div>
        <x-input-error :messages="$errors->get('code')" class="mt-2" />

        <x-primary-button class=" w-full mt-4 lg:mt-6 lg:w-80 mx-auto">
            Verify
        </x-primary-button>
    </form>
</div>