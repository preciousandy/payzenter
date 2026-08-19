<div class=" ">
    <h2 class=" text-xl font-semibold mb-6">
        All Network Providers
    </h2>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-black  bg-gray-50 dark:bg-gray-700 dark:text-gray-400 uppercase">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        Image
                    </th>

                    <th scope="col" class="px-6 py-3">
                        Name
                    </th>
                    <th scope="col" class="px-6 py-3">
                        data plans
                    </th>

                </tr>
            </thead>
            <tbody>
                @foreach ($networks as $network)

                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-sky-50 dark:hover:bg-gray-600 0">
                    <td class="px-6 py-4">
                        <img src="{{asset($network->img_url)}}" alt="" class=" h-12 rounded-lg object-cover">
                    </td>
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{$network->name}}
                    </th>
                    <td class="px-6 py-4">
                        {{number_format($network->data_plans_count)}}
                    </td>



                </tr>
                @endforeach


            </tbody>
        </table>


    </div>

</div>