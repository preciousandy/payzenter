<div class=" px-4 py-6 bg-white min-h-screen">
    <div class=" bg-gray-200 p-4 rounded-md grid grid-cols-2 gap-x-4 gap-y-6">
        <div>
            <h3 class=" font-semibold">Name</h3>
            <p class=" text-lg">{{$ajo->name}}</p>
        </div>
        <div>
            <h3 class=" font-semibold">Start Date</h3>
            <p class=" text-lg">{{formatDate($ajo->start_date)}}</p>
        </div>
        <div>
            <h3 class=" font-semibold">Group Target</h3>
            <p class=" text-lg">
                <x-naira-symbol /> {{formatNumber($ajo->amount * $ajo->no_of_members)}}
            </p>
        </div>
        <div>
            <h3 class=" font-semibold">Contribution Amount</h3>
            <p class=" text-lg">
                <x-naira-symbol /> {{formatNumber($ajo->amount)}}
            </p>
        </div>
        <div>
            <h3 class=" font-semibold">Balance</h3>
            <p class=" text-lg">
                <x-naira-symbol /> {{formatNumber($ajo->activeRound()->balance)}}
            </p>
        </div>
        <div>
            <h3 class=" font-semibold">Estimated Members</h3>
            <p class=" text-lg">
                {{$ajo->no_of_members}}
            </p>
        </div>

    </div>

    <x-primary-button class=" mt-6  block  w-full py-4" wire:click='joinAjoContribution'>
        Join Contribution
    </x-primary-button>
</div>