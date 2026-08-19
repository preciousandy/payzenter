<div class=" py-6 px-4">
    <div class="flex items-center space-x-2 bg-white p-2 border-2 border-gray-300 rounded-xl">
        <span class="fa fa-search text-xl text-gray-500"></span>
        <input wire:model.live='query' type="text" class="py-2 px-2 w-full text-sm bg-inherit border-none focus:outline-0 focus:ring focus:ring-transparent">
        <button type="button" wire:click="$set('query', '')" class="p-2 h-8 w-8 inline-flex justify-center items-center bg-gray-100 rounded-full"><span class="fa fa-close"></span></button>
    </div>

    <div class="  mt-10 space-y-4">
        @forelse ($beneficiaries as $account)
        <div class="  bg-white p-4 rounded-xl flex justify-between items-center cursor-pointer">


            <div class="  flex items-center gap-2" wire:click='transferMoney({{$account->id}})'>
                <img src="{{asset('https://ui-avatars.com/api/?background=random&name=' . $account->acct_name . '&rounded=true&bold=true')}}" alt="" class=" w-14 h-14">

                <div>
                    <h2 class=" font-semibold text-lg">
                        {{$account->acct_name}}
                    </h2>
                    <p class=" text-gray-500 text-sm mt-1 ">
                        <span>{{$account->acct_number ?? '@' . $account->swiftpay_username}}</span> | {{$account->bank_name ?? config('app.name')}}
                    </p>
                </div>
            </div>

            <div x-data="{showDeleteModal: false}">
                <button class=" text-lg text-gray-600 p-2 cursor-pointer" @click="showDeleteModal = !showDeleteModal">
                    <span class=" fa fa-trash"> </span>
                </button>

                <div x-show="showDeleteModal" x-transition x-cloak class=" fixed bg-black/70 w-screen h-screen z-50 left-0 top-0 flex justify-center items-center p-4">

                    <div class=" bg-white p-6 rounded-md w-full">

                        <div class=" flex justify-between items-center mb-4">
                            <h3 class=" text-xl font-semibold">

                            </h3>
                            <button @click="showDeleteModal = !showDeleteModal" class=" text-primary font-semibold text-2xl">
                                <span class=" fa fa-close"></span>
                            </button>
                        </div>

                        <h3 class=" text-xl text-center">
                            Are you sure you want to delete this beneficiary?
                        </h3>

                        <div class=" w-full grid grid-cols-2  gap-6 mt-4">
                            <x-secondary-button class=" rounded-lg " @click="showDeleteModal = !showDeleteModal">
                                Cancel
                            </x-secondary-button>

                            <x-primary-button class=" rounded-lg " wire:click='delete({{$account->id}})'>
                                Delete
                            </x-primary-button>
                        </div>

                    </div>

                </div>
            </div>



        </div>

        @empty

        <div>
            <h2 class=" text-lg text-center text-gray-500">
                You have no beneficiaries
            </h2>
        </div>

        @endforelse
    </div>


</div>
