<div class="bg-white">
    <div class=" px-4 py-6  max-w-4xl mx-auto min-h-screen lg:p-6">

        <h2 class=" text-lg lg:text-2xl font-semibold text-center">
            Money Exchange
        </h2>

        <div class=" space-y-4 lg:space-y-6 mt-8 lg:mt-10">

            <a href="{{route('bureau-da-change.sell-fx')}}" class=" p-4 bg-gray-50 rounded-lg flex items-start gap-4 lg:p-6 lg:shadow-sm">
                <img src="{{asset('buy-bu.png')}}" alt="" class=" h-10 w-10 rounded-full object-cover mt-2 lg:h-14 lg:w-14 ">

                <div>
                    <h3 class=" font-semibold mb-1 lg:text-lg">
                        Bureau The Change
                    </h3>

                    <p class="text-sm md:text-base text-gray-600 mt-1 lg:mt-2">
                        Sell and exchange at high rates, and get your hard currency in
                        cash or via Bank transfer.
                    </p>
                </div>
            </a>

            <a href="{{route('bureau-da-change.sell-crypto.index')}}" class=" block">
                <div class=" p-4 bg-gray-50 rounded-lg flex items-start gap-4 lg:p-6 lg:shadow-sm">
                    <img src="{{asset('buy-crypto.png')}}" alt="" class=" h-10 w-10 rounded-full object-cover mt-2 lg:h-14 lg:w-14 ">

                    <div>
                        <h3 class=" font-semibold lg:text-lg ">
                            Sell Cryptocurrency
                        </h3>

                        <p class="text-sm md:text-base text-gray-600 mt-1 lg:mt-2">
                            Sell your Crypto Swiftly at higher rates. Our SwiftPay to Peer
                            (S - P) Crypto exchange is fast and secured.
                        </p>
                    </div>
                </div>
            </a>
            <a href="{{route('coming-soon')}}" class=" block">
                <div class=" p-4 bg-gray-50 rounded-lg flex items-start gap-4 lg:p-6 lg:shadow-sm">
                    <img src="{{asset('buy-giftcard.png')}}" alt="" class=" h-10 w-10 rounded-full object-cover mt-2 lg:h-14 lg:w-14 ">

                    <div>
                        <h3 class=" font-semibold lg:text-lg ">
                            Sell Gift cards
                        </h3>

                        <p class="text-sm md:text-base text-gray-600 mt-1 lg:mt-2">
                            Redeem GiftCards Swiftly at affordable rates. Fast, 
                            easy & reliable.
                        </p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>