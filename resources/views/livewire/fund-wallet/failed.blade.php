<div class=" px-2 py-6">
    <div class=" max-w-3xl mx-auto bg-white px-4 py-10 lg:py-14 rounded-xl">

        <p class=" text-center ">
            <span class="fa fa-circle-exclamation text-red-600  fa-8x"></span>
        </p>

        <h3 class=" text-lg font-semibold text-center mt-10 md:text-2xl">There was an error when processing your payment</h3>

        <p class=" text-center mt-6 md:text-xl">
            Either you cancelled the payment or there was an error when processing your payment. <br>
            Please try again or contact us if the problem persists.
        </p>
        <p class="  mt-4 md:text-xl font-medium">
            You can try any of the following options:
        </p>

        <ul class=" list-disc space-y-2 pl-4 mt-2">
            <li>Confirm your account balance</li>
            <li>Confirm your card details</li>
            <li>Try another card</li>
            <li>Try another payment method</li>
            <li>Try again later</li>
        </ul>

        <div class=" grid grid-cols-2 items-center mt-8 max-w-lg mx-auto">
            <a href="{{route('live-chat-welcome')}}" class=" block font-semibold text-center ">
                Contact us
            </a>

            <a href="{{route('home')}}">
                <x-secondary-button class=" w-full">
                    Return home
                </x-secondary-button>
            </a>
        </div>



    </div>
</div>