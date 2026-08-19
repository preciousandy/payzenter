<div class=" bg-white h-screen flex flex-col  " x-data="{ 
         scrollToBottom() {
             const container = document.getElementById('message-container');
             setTimeout(() => {
                container.scrollTop = container.scrollHeight;
             }, 1000);
         }
     }" x-init="scrollToBottom" x-on:scroll-to-bottom="scrollToBottom()">


    <div class=" bg-primary text-white p-3 flex items-center justify-between" x-data>
        <button @click="history.back()" class=" p-2">
            <span class="fa fa-chevron-left">

            </span>
        </button>

        <h2 class=" text-lg font-semibold">
            <span class="fa fa-headphones mr-2"></span>

            Live Support
        </h2>

        <div>

        </div>
    </div>



    {{-- messages --}}
    <div class=" p-4 flex-1 space-y-4 overflow-y-auto " id="message-container">
        @forelse ($messages as $message)
        <div @class(['flex ', ' justify-end'=> !$message['agent_id'] ])>
            <div @class(['px-3 py-2 rounded-lg text-sm', 'bg-primary text-white'=> !$message['agent_id'] == 'user', 'bg-blue-100 text-blue-800'=> $message['agent_id'] ]) style="max-width: 80%">
                <p>{{$message['message']}}</p>
                <p class=" text-right text-[0.5rem]">{{$message['readable_created_at']}}</p>
            </div>
        </div>
        @empty
        <div class=" text-center p-8">
            <p class=" text-gray text-sm">
                No messages yet
            </p>
        </div>
        @endforelse
    </div>






    <div class="">
        <p wire:loading.block wire:target='sendMessage,loadUserMessages' class=" bg-blue-50 text-primary text-xs font-medium text-center py-1">processing...</p>
        <form class=" bg-gray-50  p-3 flex items-center gap-2" wire:submit.prevent='sendMessage'>
            <x-text-input id="name" type="text" class=" block w-full" wire:model='message' required />

            <button class=" bg-primary text-white h-10 w-12 inline-flex justify-center items-center rounded-lg">
                <span class=" fa fa-paper-plane"></span>
            </button>
        </form>
    </div>




</div>

@push('scripts')
<script src="https://js.pusher.com/8.2.0/pusher.min.js">
</script>

<script>
    document.addEventListener('livewire:initialized', () => {
   
    
    var pusher = new Pusher('{{config('broadcasting.connections.pusher.key')}}', {
        cluster: 'eu',
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        }
    });

    const channelName= 'private-live-support.user.{{auth()->id()}}';

    console.log(channelName);


    // Subscribe to the user's private support channel
    var userChannel = pusher.subscribe('private-live-support.user.{{auth()->id()}}');
    userChannel.bind('live-support-message', function(data) {
        console.log(data);
       @this.handleNewMessage(data.data);
    });

    
    // You can call this function from other JavaScript code
    // For example:
    // document.querySelector('#someButton').addEventListener('click', () => startChat(1));
    });
</script>

@endpush