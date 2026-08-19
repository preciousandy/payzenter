@if (!auth()->user()->is_bvn_verified)

<a href="{{route('bvn.verify')}}" class=" block bg-red-300 p-3 text-red-800 font-medium text-sm m-3 rounded-xl">

    Please verify your BVN to continue using our services. <br>
    <span class=" font-semibold">
        Click here
    </span>
</a>

@endif


@if (!auth()->user()->virtual_account_number)

<a href="{{route('create-account-number')}}" class=" block bg-red-300 p-3 text-red-800 font-medium text-sm m-3 rounded-xl">

    You don't have a virtual Account number yet <br>
    <span class=" font-semibold">
        Generate one now
    </span>
</a>

@endif