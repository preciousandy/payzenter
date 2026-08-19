<div class=" px-4 py-6 min-h-screen max-w-3xl">

    <x-blank-page-header title="Rates" />


    <p class=" text-gray-600 text-sm md:text-base lg:text-lg mt-4">
        Here are all the official rates for conversion for both Currency pairs and Crypto.
    </p>

    <div class=" mt-6">
        <h4 class=" font-medium lg:text-lg lg:font-semibold">
            Currency Pairs
        </h4>

        <div class=" mt-3 space-y-2">
            <div class=" flex items-center gap-x-3 hover:bg-gray-100 py-3 rounded-lg cursor-pointer">
                <div class=" bg-primary h-10 w-10 rounded-full flex items-center justify-center">
                    <div class=" bg-white h-7 w-7 rounded-full text-primary font-semibold flex items-center justify-center">
                        <p class=" text-2xl">
                            $
                        </p>
                    </div>
                </div>
                <div>
                    <p class=" font-semibold">
                        USD to Naira
                    </p>
                    <p class=" text-gray-600">
                        1 USD = {{number_format($rates['USD'])}} NGN
                    </p>
                </div>
            </div>
            <div class=" flex items-center gap-x-3 hover:bg-gray-100 py-3 rounded-lg cursor-pointer">
                <div class=" bg-primary h-10 w-10 rounded-full flex items-center justify-center">
                    <div class=" bg-white h-7 w-7 rounded-full text-primary font-semibold flex items-center justify-center">
                        <p class=" text-2xl">
                            €
                        </p>
                    </div>
                </div>
                <div>
                    <p class=" font-semibold">
                        EURO to Naira
                    </p>
                    <p class=" text-gray-600">
                        1 EUR = {{number_format($rates['EUR'])}} NGN
                    </p>
                </div>
            </div>
            <div class=" flex items-center gap-x-3 hover:bg-gray-100 py-3 rounded-lg cursor-pointer">
                <div class=" bg-primary h-10 w-10 rounded-full flex items-center justify-center">
                    <div class=" bg-white h-7 w-7 rounded-full text-primary font-semibold flex items-center justify-center">
                        <p class=" text-2xl">
                            £
                        </p>
                    </div>
                </div>
                <div>
                    <p class=" font-semibold">
                        Pound to Naira
                    </p>
                    <p class=" text-gray-600">
                        1 GBP = {{number_format($rates['GBP'])}} NGN
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class=" mt-6">
        <h4 class=" font-medium lg:text-lg lg:font-semibold">
            Cryptocurrency
        </h4>

        <div class=" mt-3 space-y-2">


            @foreach ($cryptos as $symbol => $item)
            <div class=" flex items-center gap-x-3 hover:bg-gray-100 py-3 rounded-lg cursor-pointer">
                <img src="{{asset($item['icon'])}}" alt="" class=" h-8">
                <div>
                    <p class=" font-semibold">
                        {{$item['name']}} to Naira
                    </p>
                    <p class=" text-gray-600">
                        1 {{$symbol}} = {{number_format($rates['USD'] * $item['price_in_usd'])}} NGN
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>