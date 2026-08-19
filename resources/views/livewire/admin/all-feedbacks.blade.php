<div class=" ">
    <h2 class=" text-xl font-semibold mb-6">
        All Users Feedbacks
    </h2>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full  text-sm text-left  ">
            <thead class=" text-black  bg-gray-50 dark:bg-gray-700 dark:text-gray-400 uppercase">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        User Name
                    </th>

                    <th scope="col" class="px-6 py-3">
                        User Email
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Subject
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Category
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3">
                        date
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Action
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($feedbacks as $feedback)
                <tr class="bg-white border-b font-normal">
                    <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                        {{$feedback->user_name}}
                    </td>
                    <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                        {{$feedback->user_email}}
                    </td>
                    <td class="px-6 py-4 ">
                        {{$feedback->subject}}
                    </td>
                    <td class="px-6 py-4 ">
                        {{$feedback->category}}
                    </td>
                    <td @class(['px-6 py-4', 'text-green-600'=> $feedback->status == 'seen', 'text-amber-600'=> $feedback->status == 'pending'])>
                        {{$feedback->status}}
                    </td>
                    <td class="px-6 py-4 ">
                        {{$feedback->created_at->diffForHumans()}}
                    </td>



                    <td class="px-6 py-4">
                        @livewire('admin.all-feedbacks.show', ['feedback' => $feedback], key($feedback->id))
                    </td>



                </tr>
                @endforeach


            </tbody>
        </table>


    </div>

</div>