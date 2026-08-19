<div class="relative overflow-x-auto shadow-md sm:rounded-lg w-full ">
    <table class=" text-sm text-left  w-full">
        <thead class="text-xs text-black  bg-gray-50 dark:bg-gray-700  dark:text-gray-400 uppercase">
            <tr>

                <th scope="col" class="px-6 py-3">
                    Currency
                </th>
                <th scope="col" class="px-6 py-3">
                    Symbol
                </th>
                <th scope="col" class="px-6 py-3">
                    Official Rate
                </th>
                <th scope="col" class="px-6 py-3">
                    Buy Rate
                </th>
                <th scope="col" class="px-6 py-3">
                    Quantity
                </th>
                <th scope="col" class="px-6 py-3">
                    Status
                </th>

                <th scope="col" class="px-6 py-3">
                    Actions
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rates as $rate)

            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-sky-50 dark:hover:bg-gray-600 0">
                <td class="px-6 py-4 flex items-center gap-2">
                    <img src="{{$rate->logo_url}}" alt="" class=" h-10 w-10 rounded-full object-cover">
                    {{$rate->currency}}
                </td>

                <td class="px-6 py-4">
                    {{$rate->currency_symbol}}
                </td>
                <td class="px-6 py-4">
                    {{formatNumber($rate->rate)}}
                </td>
                <td class="px-6 py-4">
                    {{formatNumber($rate->price)}}
                </td>
                <td class="px-6 py-4">
                    {{formatNumber($rate->volume)}}
                </td>
                <td class="px-6 py-4">
                    <span class="{{$rate->status}}">
                        {{$rate->status}}
                    </span>
                </td>

                <td class="px-6 py-4">
                    @livewire('admin.exchange-rate.edit', ['rate' => $rate], key($rate->id))
                </td>
            </tr>
            @endforeach


        </tbody>
    </table>


</div>