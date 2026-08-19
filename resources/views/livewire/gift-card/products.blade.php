<div class=" px-4 py-6 max-w-4xl lg:p-6">

    <h3 class=" text-lg lg:text-xl font-semibold">
        Select the Gift Card you want to buy
    </h3>


    <div class="flex items-center space-x-2 bg-white p-2 lg:p-3 border border-gray-300 rounded-lg mt-6">
        <span class="fa fa-search text-base text-gray-500"></span>
        <input wire:model.live='query' type="text" class=" px-2 w-full text-sm md:text-base bg-inherit border-none focus:outline-0 focus:ring focus:ring-transparent">
        <button type="button" wire:click="$set('query', '')" class=" h-4 w-4 inline-flex justify-center items-center bg-gray-100 rounded-full"><span class="fa fa-close text-sm"></span></button>
    </div>

    <div class=" space-y-4 mt-6 lg:mt-8 lg:space-y-6">
        @foreach ($gift_cards as $gift_card)
        <a href="{{route('gift-cards.buy', $gift_card['productId'])}}" class="  bg-white p-4 rounded-xl flex items-center gap-3 lg:p-6 lg:gap-4 lg:shadow-sm">
            <img src="{{asset($gift_card['logoUrls'][0])}}" alt="" class=" h-12 w-12 lg:h-14 lg:w-14 rounded-full">

            <div>
                <h4 class=" font-medium  lg:text-xl">
                    {{$gift_card['productName']}}
                </h4>

                <p class=" text-sm text-gray-600 mt-1 lg:text-base lg:mt-2">
                    {{$gift_card['recipientCurrencyCode']}}
                </p>
            </div>
        </a>

        @endforeach
    </div>
</div>