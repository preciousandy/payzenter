<div x-data="{ 
            scrollToBottom() {
                const container = document.getElementById('live-messages-container');
                setTimeout(() => {
                    container.scrollTop = container.scrollHeight;
                }, 1000);
            },
            activeTab: 'topics',
            showMobileChat: false
        }" x-on:scroll-to-end="scrollToBottom" class="pb-4">
    <div>
        <h3 class="text-lg font-bold">
            <span class="fa fa-headset mr-2"></span> Admin Live Support
        </h3>
    </div>

    <!-- Mobile Tab Navigation -->
    <div class="lg:hidden mt-4 sticky top-0 bg-white z-10 border-b border-gray-200">
        <div class="flex">
            <button @click="activeTab = 'topics'; showMobileChat = false" :class="activeTab === 'topics' ? 'border-primary2 text-primary2 bg-blue-50' : 'border-transparent text-gray-500'" class="flex-1 py-3 px-4 border-b-2 font-medium text-sm transition-colors">
                <span class="fa fa-bars-progress mr-1"></span>
                Topics
            </button>
            <button @click="activeTab = 'users'; showMobileChat = false" :class="activeTab === 'users' ? 'border-primary2 text-primary2 bg-blue-50' : 'border-transparent text-gray-500'" class="flex-1 py-3 px-4 border-b-2 font-medium text-sm transition-colors">
                <span class="fa fa-users mr-1"></span>
                Users
            </button>
            <button @click="activeTab = 'chat'; showMobileChat = true" :class="activeTab === 'chat' ? 'border-primary2 text-primary2 bg-blue-50' : 'border-transparent text-gray-500'" class="flex-1 py-3 px-4 border-b-2 font-medium text-sm transition-colors">
                <span class="fa fa-comments mr-1"></span>
                Chat
            </button>
        </div>
    </div>

    <div class="mt-6 bg-white shadow rounded-md w-full lg:grid lg:grid-cols-5">
        {{-- Topics page --}}
        <div class="border-r-2 border-gray-300 lg:block" :class="{ 'hidden': activeTab !== 'topics' }" x-show="activeTab === 'topics' || window.innerWidth >= 1024">

            <div class="p-4 border-b-2 flex justify-between items-center">
                <h4 class="font-extrabold text-primary2">
                    <span class="fa fa-bars-progress mr-2"></span>
                    Topics
                </h4>

                <div x-data="{showAddTopicModal: false}">
                    <x-primary-button class="text-xs p-2" @click="showAddTopicModal = true">
                        <span class="fa fa-plus-circle mr-2"></span>
                        <span class="hidden sm:inline">Add topic</span>
                        <span class="sm:hidden">Add</span>
                    </x-primary-button>
                    <div x-show="showAddTopicModal" x-transition x-cloak class="fixed bg-black/70 z-50 left-0 top-0 w-full h-full flex justify-center items-center p-4">
                        <div class="bg-gray-50 p-4 lg:p-8 rounded-lg w-full md:w-full lg:w-1/3 xl:w-1/4 space-y-5">
                            <div class="flex justify-between items-center mb-6">
                                <h2 class="text-xl md:text-2xl font-semibold">Add chat topic</h2>
                                <button @click="showAddTopicModal = false">
                                    <span class="fa fa-close text-2xl"></span>
                                </button>
                            </div>

                            <form action="" class="space-y-4" wire:submit.prevent='addTopic'>
                                <div>
                                    <x-input-label for="email" :value="__('Topic name')" />
                                    <x-text-input id="email" class="block mt-1 w-full" type="text" wire:model="topicName" required autofocus />
                                    <x-input-error :messages="$errors->get('topicName')" class="mt-2" />
                                </div>

                                <x-primary-button class="w-full rounded-xl">
                                    {{ __('Submit topic') }}
                                </x-primary-button>
                            </form>
                        </div>
                    </div>


                </div>
            </div>

            <div class="mt-4 space-y-3 p-2 sm:p-4 max-h-96 lg:max-h-none overflow-y-auto lg:overflow-visible" x-data="{topicId: @entangle('topicId')}">

                @foreach ($topics as $topicItem)
                <div class="border border-gray-300 rounded-md cursor-pointer flex items-center justify-between hover:bg-primary/20 transition-colors" :class="topicId == {{$topicItem->id}} && 'bg-primary/20 border-primary text-primary'" x-data="{showDeleteModal: false}">
                    <div class="flex-1 p-3 sm:p-4 min-w-0" wire:click="selectTopic({{$topicItem->id}})" x-on:click="if (window.innerWidth < 1024) { activeTab = 'users'; }">
                        <h5 class="font-semibold text-sm truncate">
                            {{$topicItem->name}}
                        </h5>

                        <p class="text-sm mt-1 text-gray-600">
                            {{$topicItem->messages->count()}} <span class="text-xs">messages</span>
                        </p>
                    </div>

                    <div class="flex-shrink-0">
                        <button class="p-3 sm:p-4" @click="showDeleteModal = true">
                            <span class="fa fa-trash text-red-500"></span>
                        </button>

                        {{-- delete topic modal --}}
                        <div x-show="showDeleteModal" x-transition x-cloak class="fixed bg-black/70 z-50 left-0 top-0 w-full h-full flex justify-center items-center p-4 text-black">
                            <div class="bg-gray-50 p-4 lg:p-8 rounded-lg w-full md:w-full lg:w-1/3 xl:w-1/4">
                                <div class="flex justify-between items-center mb-6">
                                    <h2 class="text-xl md:text-2xl font-semibold">Delete {{$topicItem->name}} topic</h2>
                                    <button @click="showDeleteModal = false">
                                        <span class="fa fa-close text-2xl"></span>
                                    </button>
                                </div>

                                <h2 class="text-lg md:text-xl text-center font-medium">
                                    Are you sure you want to delete this topic and all its messages?
                                </h2>

                                <div class="grid grid-cols-2 gap-x-4 mt-8">
                                    <button @click="showDeleteModal = false" class="text-center font-semibold">
                                        Cancel
                                    </button>
                                    <x-primary-button class="w-full bg-red-800 rounded-xl" wire:click='deleteTopic({{$topicItem->id}})'>
                                        Yes, Delete
                                    </x-primary-button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

            </div>
        </div>

        {{-- Users Tab --}}
        <div class="border-r-2 border-gray-300 lg:block" :class="{ 'hidden': activeTab !== 'users' }" x-show="activeTab === 'users' || window.innerWidth >= 1024">

            <div class="p-4 border-b-2">
                <h4 class="font-extrabold text-primary2">
                    <span class="fa fa-users mr-2"></span>
                    Users
                </h4>
            </div>

            <div class="p-2 sm:p-4 mt-4 space-y-3 max-h-96 lg:max-h-none overflow-y-auto lg:overflow-visible" x-data="{userId: @entangle('userId')}">

                @forelse ($users as $userItem)
                <div class="p-3 sm:p-4 border border-gray-300 rounded-md cursor-pointer flex items-center gap-x-2 hover:bg-primary/20 transition-colors" :class="userId == {{$userItem->id}} && 'bg-primary/20 border-primary/30 text-primary'" wire:click="selectUser({{$userItem->id}})" x-on:click="activeTab = 'chat'; showMobileChat = true; console.log('Mobile chat opened');">
                    <img src="{{asset($userItem->profile_photo)}}" alt="" class="h-8 w-8 sm:h-10 sm:w-10 rounded-full flex-shrink-0">
                    <div class="min-w-0 flex-1">
                        <h5 class="font-semibold text-sm truncate">
                            {{$userItem->name}}
                        </h5>

                        <p class="text-sm mt-1 text-gray-600">
                            {{$userItem->messages_count}} <span class="text-xs">messages</span>
                        </p>
                    </div>
                </div>

                @empty

                <div class="p-6 text-center">
                    No user messages for this topic
                </div>
                @endforelse

            </div>
        </div>

        <div class="col-span-3 lg:col-span-3 flex flex-col" :class="{ 'hidden': activeTab !== 'chat' && !showMobileChat }" x-show="activeTab === 'chat' || showMobileChat || window.innerWidth >= 1024" style="height: calc(100vh - 200px); min-height: 500px;">

            <div class="p-4 border-b-2 flex items-center justify-between">
                <h4 class="font-extrabold text-primary2 flex items-center">
                    <span class="fa fa-comments mr-2"></span>
                    <span class="hidden sm:inline">Chat</span>
                </h4>
                <!-- Back button for mobile -->
                <button @click="activeTab = 'users'; showMobileChat = false" class="lg:hidden text-gray-600 hover:text-gray-900 px-3 py-1 rounded-md transition-colors">
                    <span class="fa fa-arrow-left mr-2"></span>
                    <span class="text-sm">Back</span>
                </button>
            </div>

            {{-- messages --}}
            <div class="p-4 space-y-4 flex-1 overflow-y-auto" id="live-messages-container">
                @if (count($messages) > 0)

                @foreach ($messages as $message)
                <div @class(['flex ', ' justify-end'=> $message['agent_id'] ])>
                    <div @class(['p-3 rounded-lg text-sm max-w-xs sm:max-w-sm md:max-w-md lg:max-w-lg xl:max-w-xl', 'bg-primary2 text-white'=> $message['agent_id'], 'bg-gray-200 text-gray-800'=> !$message['agent_id'] ])>
                        <p class="break-words">{{$message['message']}}</p>
                        <p class="text-right text-[0.5rem] mt-1">{{$message['readable_created_at']}}</p>
                    </div>
                </div>
                @endforeach
                @else
                <div class="text-center p-8">
                    <p class="text-gray-500 text-sm">
                        No messages yet
                    </p>
                </div>
                @endif
            </div>

            @if ($userId && $topicId)
            <div class="">
                <p wire:loading.block wire:target='sendMessage,loadUserMessages' class="bg-blue-50 text-primary text-xs font-medium text-center py-1">processing...</p>
                <form class="bg-gray-50 p-3 flex items-center gap-2" wire:submit.prevent='sendMessage'>
                    <x-text-input id="name" type="text" class="block w-full" wire:model='message' required />

                    <button class="bg-primary text-white h-10 w-12 inline-flex justify-center items-center rounded-lg flex-shrink-0">
                        <span class="fa fa-paper-plane"></span>
                    </button>
                </form>
            </div>
            @endif

        </div>
    </div>
</div>

@push('scripts')
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

<script>
    document.addEventListener('livewire:initialized', () => {
        // Handle window resize for responsive behavior
        function handleResize() {
            if (window.innerWidth >= 1024) {
                // Desktop view - show all sections
                Alpine.evaluate(document.querySelector('[x-data]'), 'activeTab = "topics"');
                Alpine.evaluate(document.querySelector('[x-data]'), 'showMobileChat = false');
            }
        }
        
        window.addEventListener('resize', handleResize);
        
        // Pusher.logToConsole = true;
        
        var pusher = new Pusher('{{config('broadcasting.connections.pusher.key')}}', {
            cluster: 'eu',
            authEndpoint: '/broadcasting/auth',
            auth: {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            }
        });
        
        // Subscribe to admin channel for the current topic
        let adminChannel = null;
        
        // Listen for topic changes
        Livewire.on('topicSelected', (topicId) => {
            // Unsubscribe from previous channel
            if (adminChannel) {
                pusher.unsubscribe(adminChannel.name);
            }
            
            // Subscribe to new topic channel
            if (topicId) {
                adminChannel = pusher.subscribe('private-live-support.admin.' + topicId);
                adminChannel.bind('live-support-message', function(data) {
                    @this.handleNewMessage(data.data);
                });
            }
        });
    });
</script>

@endpush