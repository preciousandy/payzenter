<div x-data="{showViewModal: false}">
    <x-secondary-button @click="showViewModal = true" class=" text-sm  rounded-xl">
        <span class=" fa fa-eye mr-2"></span> View Message
    </x-secondary-button>

    <div x-show="showViewModal" x-transition x-cloak class=" fixed bg-black/70 z-50 left-0 top-0 w-full h-full flex justify-center items-center p-4">
        <div class=" bg-gray-50 p-4 lg:p-8 rounded-lg w-full md:w-full lg:w-1/3 2xl:w-1/4  space-y-5">
            <div class=" flex justify-between items-center mb-6">
                <h2 class=" text-2xl font-bold">FeedBack Details</h2>
                <button @click="showViewModal = false" class=" text-2xl p-3">
                    <span class="fa fa-close"></span>
                </button>
            </div>

            <div class=" ">
                <div class=" grid grid-cols-2 gap-4">
                    <div>
                        <h5 class=" font-semibold">Name:</h5>
                        <h2 class=" text-lg mt-1  ">
                            {{$feedback->user_name}}
                        </h2>
                    </div>
                    <div>
                        <h5 class=" font-semibold">Email</h5>
                        <h2 class=" text-lg mt-1  ">
                            {{$feedback->user_email}}
                        </h2>
                    </div>
                    <div>
                        <h5 class=" font-semibold">Phone Number</h5>
                        <h2 class=" text-lg mt-1  ">
                            {{$feedback->phone}}
                        </h2>
                    </div>
                    <div>
                        <h5 class=" font-semibold">Subject</h5>
                        <h2 class=" text-lg mt-1  ">
                            {{$feedback->subject}}
                        </h2>
                    </div>
                    <div>
                        <h5 class=" font-semibold">Category</h5>
                        <h2 class=" text-lg mt-1  ">
                            {{$feedback->category}}
                        </h2>
                    </div>
                </div>

                <div class=" mt-4">
                    <h5 class=" font-semibold">Message:</h5>
                    <p class=" text-lg mt-1  ">
                        {{$feedback->message}}
                    </p>
                </div>

                @if ($feedback->status == 'pending')
                <x-primary-button wire:click="markAsRead" class=" mt-8 mx-auto w-1/3 block rounded-xl">
                    Mark as Read
                </x-primary-button>

                @endif
            </div>
        </div>
    </div>


</div>