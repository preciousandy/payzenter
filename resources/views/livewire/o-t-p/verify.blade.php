<div class=" px-4 py-6 max-w-2xl lg:p-6 mx-auto" x-data="{showModal: @entangle('isCodeSent')}">
    <x-loading-spinner wire:target='sendOTP' />
    <h3 class=" text-lg font-semibold lg:text-xl lg:text-center">
        Before you Continue
    </h3>

    <p class=" text-gray-600 mt-2 lg:mt-4 lg:text-center lg:text-lg">
        An OTP will be sent to your email address to verify it is you before you can proceed.
    </p>

    <x-primary-button class=" w-full mt-4 lg:w-80 mx-auto lg:mt-6" wire:click="sendOTP">
        Send Code
    </x-primary-button>

    <div x-cloak x-show="showModal" x-transition class=" bg-black/60 fixed h-full w-full left-0 top-0 z-[999] flex justify-center items-center p-4">

        <div class=" bg-white w-full rounded-2xl p-6 max-w-xl lg:p-8">
            <span class=" fa fa-envelope text-[#407BFF] block text-7xl mx-auto w-fit lg:text-8xl"></span>

            <h4 class=" text-xl font-medium text-center mt-3  lg:mt-4 lg:font-semibold">Code sent</h4>

            <p class=" text-gray-700 text-center mt-1 lg:text-lg">
                A code has been sent to your email address <b>({{mask_email(auth()->user()->email)}})</b>. Please check your email.
            </p>

            <a href="{{route('otp.validate')}}" class=" mt-4 block lg:mt-6">
                <x-primary-button class=" w-full rounded-full " style="border-radius: 9999px">
                    Enter Code
                </x-primary-button>
            </a>
        </div>
    </div>

</div>