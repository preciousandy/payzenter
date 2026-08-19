@props(['title', 'value', 'if_currency' => false])

<div class="  rounded-md flex  items-center space-x-4 my-card px-4 py-6 border-l-4 border-blue-600 text-black bg-white shadow">
    <div>
        <p class=" text-base font-medium text-gray-700 ">{{$title}}</p>

        <h1 class=" text-5xl font-bold text-blue-600 mt-2">
            @if($if_currency)
            <x-naira-symbol />
            @endif
            {{number_format($value)}}
        </h1>
    </div>
</div>