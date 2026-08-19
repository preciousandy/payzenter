<div x-data="{showFailedModal: false}">
    <x-secondary-button @click="showFailedModal = true" class=" py-3">
        <span class="fa fa-eye mr-2"></span> View
    </x-secondary-button>

    <div x-show="showFailedModal" x-transition x-cloak class=" fixed bg-black/70 z-50 left-0 top-0 w-full h-full flex justify-center items-center p-4 overflow-y-auto ">
        <div class=" bg-gray-50 p-4 lg:p-8 rounded-lg w-full max-w-4xl space-y-5 overflow-y-auto max-h-[90vh] text-left">
            <div class=" flex justify-between items-center mb-6">
                <h2 class=" text-2xl font-semibold"> Order Details</h2>
                <button @click="showFailedModal = false" class=" text-2xl">
                    <span class="fa fa-close"></span>
                </button>
            </div>

            <div class=" grid grid-cols-2 gap-5">


                <div>
                    <h4 class=" text-gray-500">
                        User Name
                    </h4>

                    <p class=" text-lg font-medium">
                        {{$order->user->name}}
                    </p>
                </div>

                <div>
                    <h4 class=" text-gray-500">
                        User Email
                    </h4>

                    <p class=" text-lg font-medium">
                        {{$order->user->email}}
                    </p>
                </div>

                <div>
                    <h4 class=" text-gray-500">
                        Currency
                    </h4>
                    <p class=" text-lg font-medium">
                        {{$order->currency_name}}
                    </p>
                </div>


                <div>
                    <h4 class=" text-gray-500">
                        Currency code
                    </h4>

                    <p class=" text-lg font-medium">
                        {{$order->currency_code}}
                    </p>
                </div>
                <div>
                    <h4 class=" text-gray-500">
                        Amount
                    </h4>

                    <p class=" text-lg font-medium">
                        {{formatNumber($order->amount)}} {{$order->currency_code}}
                    </p>
                </div>
                <div>
                    <h4 class=" text-gray-500">
                        Price in Naira
                    </h4>

                    <p class=" text-lg font-medium">
                        {{formatNumber($order->naira_amount)}}
                    </p>
                </div>
                <div>
                    <h4 class=" text-gray-500">
                        Status
                    </h4>

                    <p class=" text-lg font-medium {{$order->status}} w-fit">
                        {{$order->status}}
                    </p>
                </div>

                <h3 class=" text-xl font-bold col-span-2">
                    Account Information
                </h3>

                @foreach ($order->data as $label => $dataItem)
                @if (Str::startsWith($dataItem, 'foreign Exchange/'))
                <div>
                    <h4 class=" text-gray-500">
                        {{$label}}
                    </h4>

                    <a href="{{Storage::url($dataItem)}}" target="_blank">
                        <img src="{{asset(Storage::url($dataItem))}}" alt="" class=" object-cover rounded-lg mt-1">
                    </a>
                </div>

                @else
                <div>
                    <h4 class=" text-gray-500">
                        {{$label}}
                    </h4>

                    <p class=" text-lg font-medium">
                        {{$dataItem}}
                    </p>
                </div>

                @endif


                @endforeach

            </div>

            @if ($order->status == 'pending')
            <div x-data='{showRejectForm: false}'>
                <div class=" w-full grid  grid-cols-2 gap-6">
                    <x-secondary-button @click="showRejectForm = !showRejectForm" class=" w-full border-red-600 text-red-700">
                        Reject
                    </x-secondary-button>

                    <x-primary-button wire:click='approveBuyOrder({{$order->id}})' class=" w-full bg-green-700">
                        Approve
                    </x-primary-button>

                </div>

                <form action="" x-show="showRejectForm" x-transition x-cloak class=" space-y-4 mt-3" wire:submit.prevent="rejectBuyOrder({{$order->id}})">
                    <div>
                        <x-input-label for="name" :value="__('Reason:')" />
                        <textarea name="" id="" rows="4" class=" w-full rounded-md bg-white border border-gray-300 mt-2" wire:model='rejectMessage' required></textarea>
                        <x-input-error class="mt-2 " :messages="$errors->get('rejectMessage')" />
                    </div>

                    <x-primary-button class=" w-full ">
                        {{ __('Submit') }}
                    </x-primary-button>
                </form>
            </div>
            @endif
        </div>
    </div>


</div>