@props(['id' => 'swiftpay'])
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
                                    for (let i = 0; i < 4; i++) {
                                        const input = document.getElementById(`{{$id}}-${i + 1}`);
                                        input.value = digits[i] || '';
                                    }
                                });
                            }
                        }" x-init="initPin()" class="flex flex-col items-center justify-center  bg-gray-50 rounded-xl">
        <div class="p-4">
            <h2 class="mb-2 lg:mb-3 text-base font-semibold text-center  text-gray-700 lg:text-lg">Enter PIN </h2>
            <div class="flex  space-x-4">
                <template x-for="i in 4" :key="i">
                    <input type="password" :id="'{{$id}}-' + i" maxlength="1" class="w-12 h-12 lg:w-14 lg:h-14 text-center text-xl font-semibold border-2 border-gray-300 rounded-lg focus:border-primary focus:ring-2 focus:ring-primary focus:outline-none" x-on:input="
                                            $wire.pin = Array.from(document.querySelectorAll('input[id^={{$id}}-]')).map(input => input.value).join('');
                                            focusNext($el, '{{$id}}-' + (i + 1))
                                        " x-on:keydown.backspace="focusPrev($el, '{{$id}}-' + (i - 1))" x-on:keydown="if (!/^[0-9]$/.test($event.key) && !['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab'].includes($event.key)) $event.preventDefault()" inputmode="numeric" pattern="[0-9]*">
                </template>
            </div>


        </div>
    </div>
    <x-input-error class="mt-2 " :messages="$errors->get('pin')" />
</div>