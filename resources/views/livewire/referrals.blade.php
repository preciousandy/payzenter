<div class="bg-gray-100">
    <div class=" px-4 py-6  min-h-screen max-w-xl">

        <div class=" flex items-center gap-4 justify-between">
            <h2 class=" lg:text-lg">
                Share your referral link with your friends and earn <span class=" text-xl font-semibold">
                    <x-naira-symbol />{{ $referralBonus }}
                </span> for each successful referral
            </h2>

            <img src="{{asset('referral icon.png')}}" alt="" class=" h-24 lg:h-32">
        </div>

        <div class=" mt-4 bg-white p-4 rounded-xl lg:shadow-sm lg:p-6" x-data>
            <h3 class=" font-medium lg:text-lg">SwiftPay Referral Link</h3>
            <x-text-input wire:model='link' disabled class=" w-full bg-white p-3 mt-1 lg:mt-2" />

            <x-primary-button onclick="copyToClipboard('{{$link}}')" class=" mt-4  w-full rounded-lg py-3 lg:mt-6 ">
                <span class=" fa fa-copy"></span> <span id="copy">Copy Link</span>
            </x-primary-button>
        </div>


        {{--Ajo Savings referral --}}
        <div class=" mt-4 bg-white p-4 rounded-lg lg:shadow-sm lg:p-6" x-data>
            <h3 class=" font-medium lg:text-lg">Ajo Savings Referral Code</h3>
            <x-text-input wire:model='referralCode' disabled class=" w-full bg-white p-3 mt-2" />

            <x-secondary-button onclick="copyToClipboard('{{$referralCode}}')" class=" mt-4  w-full rounded-lg   bg-white">
                <span class=" fa fa-clipboard"></span> <span id="copy">Copy Referral Code</span>
            </x-secondary-button>
        </div>



        <div class=" mt-4 bg-white p-4 rounded-lg flex items-center gap-2 lg:shadow-sm lg:gap-4 lg:mt-6 lg:p-6">
            <div class=" h-10 w-10 lg:h-12 lg:w-12 rounded-full bg-blue-200 flex items-center justify-center flex-shrink-0">
                <x-lucide-hand-coins class="h-5 lg:h-7 text-primary" />
            </div>
            <div>
                <h3 class=" font-medium lg:text-lg">Total Earnings</h3>
                <h3 class=" font-semibold text-xl md:text-2xl lg:text-3xl text-primary mt-2">
                    <x-naira-symbol /> {{formatNumber($amounEarned)}}
                </h3>
            </div>
        </div>


        <div class=" mt-6 space-y-4">
            @forelse ($referrals as $user)
            <div class=" flex justify-between items-center p-4 border border-gray-300 rounded-lg bg-white">
                <div class=" flex items-center gap-x-2">
                    <img src="{{asset($user->profile_photo)}}" alt="" class=" h-12 w-12 rounded-full object-cover">

                    <div>
                        <h3 class=" lg:text-lg font-medium">{{$user->name}}</h3>
                        <p class=" text-xs text-gray-500">
                            {{$user->pivot->created_at->diffForHumans()}}
                        </p>
                    </div>
                </div>

                <div class=" text-right">
                    <p class=" lg:text-2xl text-lg font-semibold">
                        <x-naira-symbol /> {{formatNumber($user->pivot->amount)}}
                    </p>
                    <p class=" text-gray-600 text-sm">
                        {{$user->pivot->status}}
                    </p>
                </div>
            </div>
            @empty

            <div>
                <img src="{{asset('no referral.png')}}" alt="" class=" mx-auto mt-6">
                <p class=" text-center text-gray-500 p-6 text-lg font-medium">
                    You have no referrals yet
                </p>
            </div>

            @endforelse


        </div>
    </div>
</div>