<div class=" ">
    <h2 class=" text-xl font-semibold mb-6">
        All Payment Transactions
    </h2>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-black  bg-gray-50 dark:bg-gray-700 dark:text-gray-400 uppercase">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        user
                    </th>

                    <th scope="col" class="px-6 py-3">
                        transaction ID
                    </th>
                    <th scope="col" class="px-6 py-3">
                        transaction reference
                    </th>
                    <th scope="col" class="px-6 py-3">
                        transaction amount
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Charge Fee
                    </th>
                    <th scope="col" class="px-6 py-3">
                        payment method
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Date
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transactions as $transaction)

                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-sky-50 dark:hover:bg-gray-600 0">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{$transaction->user->name}}
                    </th>
                    <td class="px-6 py-4">
                        {{$transaction->transaction_id}}
                    </td>
                    <td class="px-6 py-4">
                        {{$transaction->transaction_reference}}
                    </td>
                    <td class="px-6 py-4">
                        {{formatNumber($transaction->amount)}}
                    </td>
                    <td class="px-6 py-4">
                        {{formatNumber($transaction->charge_fee)}}
                    </td>
                    <td class="px-6 py-4">
                        {{$transaction->payment_method}}
                    </td>

                    <td class="px-6 py-4">
                        {{$transaction->created_at->diffForHumans()}}
                    </td>

                </tr>
                @endforeach


            </tbody>
        </table>

        <div class="p-4">
            {{$transactions->links()}}
        </div>
    </div>

</div>