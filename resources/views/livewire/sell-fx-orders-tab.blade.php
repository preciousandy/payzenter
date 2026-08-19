<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-black  bg-gray-50 dark:bg-gray-700 dark:text-gray-400 uppercase">
            <tr>
                <th scope="col" class="px-6 py-3">
                    User Name
                </th>

                <th scope="col" class="px-6 py-3">
                    User email
                </th>
                <th scope="col" class="px-6 py-3">
                    Currency
                </th>
                <th scope="col" class="px-6 py-3">
                    Amount
                </th>
                <th scope="col" class="px-6 py-3">
                    Price in NGN
                </th>


                <th scope="col" class="px-6 py-3">
                    Status
                </th>
                <th scope="col" class="px-6 py-3 text-center">
                    Actions
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)

            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-sky-50 dark:hover:bg-gray-600 0">
                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{$order->user->name}}
                </td>
                <td class="px-6 py-4 ">
                    {{$order->user->email}}
                </td>
                <td class="px-6 py-4 ">
                    {{$order->currency_name}}
                </td>
                <td class="px-6 py-4">
                    {{formatNumber($order->amount)}}
                </td>
                <td class="px-6 py-4">
                    {{formatNumber($order->naira_amount)}}
                </td>


                <td class="px-6 py-4  ">
                    <span class="{{$order->status}}">
                        {{$order->status}}
                    </span>
                </td>

                <td class="px-6 py-4 text-center">


                </td>



            </tr>
            @endforeach


        </tbody>
    </table>


</div>