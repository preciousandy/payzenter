<div x-data="{showNotificationModal : false}">
    <x-loading-spinner wire:target='loadMore' />
    <div class=" relative">
        <button class=" text-2xl lg:text-3xl" @click="showNotificationModal = true">
            <span class="fa fa-bell text-[#1400FB]"></span>
        </button>
        @if(auth()->user()->unreadNotifications->isNotEmpty())
        <span class=" block w-3 h-3 bg-red-700 rounded-full absolute top-0 right-0"></span>
        @endif
    </div>

    <div x-show="showNotificationModal" x-cloak x-transition class="fixed bg-gray-50 z-30 left-0 top-0 h-full w-full p-4 overflow-y-auto">
        <div class="flex items-center justify-between ">
            <h3 class="text-xl font-bold">Notifications</h3>
            <button class=" text-3xl" @click="showNotificationModal = false">
                <span class="fa fa-close"></span>
            </button>
        </div>

        <div class=" mt-3 mb-4 flex justify-end items-center">

            <button class=" sec-btn  rounded shadow-sm bg-white text-xs" wire:click='markAllAsRead'>
                <span class=" fa fa-broom mr-2"></span> Mark All as Read
            </button>
        </div>

        <div class=" space-y-3 mt-6">
            @forelse ($notifications as $notification)
            <div wire:click="markAsRead('{{$notification->id}}')" @class(['block rounded p-3 cursor-pointer text-sm flex items-center gap-2', 'bg-blue-50 text-blue-700 border-l-2 border-blue-600'=> !$notification->read_at, 'bg-white'=> $notification->read_at])>
                <div class=" h-10 w-10 rounded-full bg-blue-200 flex items-center justify-center flex-shrink-0">
                    @if (isset($notification->data['topic']))
                    <x-render-notification-icon :type="$notification->data['topic']" />
                    @else
                    <x-lucide-bell class=" h-5 text-primary" />
                    @endif
                </div>
                <div>
                    <p class=" font-medium ">{{ ucfirst(str_replace('_', ' ', $notification->data['topic'] ?? 'Notification')) }}</p>
                    <p class="  text-gray-700 mt-1">{{$notification->data['message'] ?? ''}}</p>
                </div>
            </div>

            @empty

            <div class=" p-4 flex justify-center items-center">
                You have no pending Notifications
            </div>

            @endforelse
        </div>

        @if ($notifications->hasMorePages())
        <div class=" p-4 lg:p-8 w-full flex justify-center items-center">
            <x-secondary-button wire:click='loadMore' class=" rounded-md">
                Load More ...
            </x-secondary-button>
        </div>
        @endif

    </div>
</div>