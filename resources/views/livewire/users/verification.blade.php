<div class=" min-h-screen p-4 relative lg:h-screen">

    <a href="#" class=" block w-fit ">
        <img src="{{asset('logo 3.png')}}" alt="" class=" h-20 hidden lg:block">
    </a>


    <x-loading-spinner wire:target='verify, resendOtp' />

    <div class="lg:absolute lg:top-1/2  lg:left-1/2 lg:-translate-x-1/2  lg:-translate-y-1/2 max-w-xl w-full mx-auto">
        <form method="POST" class=" lg:p-8 rounded-xl w-full mt-12" wire:submit.prevent='verify'>
            <div class=" flex items-center space-x-4  w-fit mx-auto">
                {{-- <button class=" bg-primary h-12 w-12 rounded-full text-white">
                    <span class=" fa fa-chevron-left"></span>
                </button> --}}
                <h3 class=" text-xl lg:text-2xl font-semibold text-center ">Verify your Account </h3>
            </div>

            <p class="  text-center mt-4 text-gray-600 text-sm mb-10 lg:text-lg">Check your email or your SMS for an OTP from us</p>
            @csrf

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
                                    }" x-init="initPin()" class="flex flex-col items-center justify-center  bg-gray-50 rounded-xl w-fit mx-auto ">
                    <div class="p-4">
                        <h2 class="mb-2 lg:mb-4 lg:text-lg text-base font-semibold text-center  text-gray-700">Enter Code</h2>
                        <div class="flex  space-x-4">
                            <template x-for="i in 6" :key="i">
                                <input type="password" :id="'pin-' + i" maxlength="1" class="w-10 h-10 lg:w-12 lg:h-12 text-center text-xl font-semibold border-2 border-gray-300 rounded-lg focus:border-primary focus:ring-2 focus:ring-primary focus:outline-none" x-on:input="
                                                        $wire.pin = Array.from(document.querySelectorAll('input[id^=pin-]')).map(input => input.value).join('');
                                                        focusNext($el, 'pin-' + (i + 1))
                                                    " x-on:keydown.backspace="focusPrev($el, 'pin-' + (i - 1))" x-on:keydown="if (!/^[0-9]$/.test($event.key) && !['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab'].includes($event.key)) $event.preventDefault()" inputmode="numeric" pattern="[0-9]*">
                            </template>
                        </div>


                    </div>
                </div>
            </div>

            <div x-data="otpCountdown({{ $remainingTime }})" x-init="startCountdown()" class=" mt-2 lg:mt-4">

                <div x-show="remainingTime > 0" class=" text-gray-700 text-sm lg:text-base font-medium">
                    Resend in <span x-text="formatTime(remainingTime)"></span>
                </div>

                <button x-show="remainingTime === 0" x-on:click.prevent="resendOtp()" wire:click="resendOtp" class=" text-primary font-semibold">
                    Resend OTP
                </button>

            </div>


            <div class=" mt-6">
                <x-primary-button class=" w-full  rounded-lg ">
                    {{ __('Verify') }}
                </x-primary-button>
            </div>


        </form>
    </div>
</div>

@push('scripts')
<script>
    function otpCountdown(initialTime) {
        return {
            remainingTime: initialTime,
            
            startCountdown() {
                const countdownInterval = setInterval(() => {
                    if (this.remainingTime > 0) {
                        this.remainingTime--;
                    } else {
                        clearInterval(countdownInterval);
                    }
                }, 1000);
            },
            
            formatTime(time) {
                const minutes = Math.floor(time / 60);
                const seconds = time % 60;
                return `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
            },
            
            resendOtp() {
                // Trigger Livewire resend method
                @this.call('resendOtp');
                
                // Restart the countdown
                this.remainingTime = initialTime;
                this.startCountdown();
            }
        }
    }
</script>
@endpush