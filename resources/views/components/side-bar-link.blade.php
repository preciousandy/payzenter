@props(['active'])

<a {{ $attributes }} href="{{route('home')}}" @class(['font-medium p-3 w-full flex items-center hover:bg-indigo-500 rounded-lg', 'text-primary2 border-b-2 border-primary bg-white  font-semibold'=> $active])>
    <img src="{{asset('logo-3.svg')}}" alt="" class=" mr-1"> home
</a>