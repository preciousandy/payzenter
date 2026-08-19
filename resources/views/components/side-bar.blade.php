<div class="h-screen fixed z-30 w-72 bg-gray-800 p-6 hidden lg:flex flex-col border-r border-gray-700 shadow-2xl">

    <a href="{{ route('home') }}" class="flex items-center text-xl font-bold mt-2 mb-8">
        <img src="{{asset('logo/pzwhlogo.png')}}" alt="Logo" class="h-16 mx-auto">
    </a>


    @auth
        <div class="mb-8 p-3 bg-gray-700/50 rounded-lg border border-gray-700 shadow-inner" 
             x-data="{ merchantId: '{{ auth()->user()->merchant_id ?? 'N/A' }}', showCopied: false }"
        >
            <p class="text-xs font-medium text-gray-400">Merchant ID</p>
            <div class="flex items-center justify-between mt-1">
                <span class="text-lg font-bold text-white tracking-wider truncate">{{ auth()->user()->merchant_id ?? 'N/A' }}</span>
                
                <button 
                    @click="navigator.clipboard.writeText(merchantId); showCopied = true; setTimeout(() => showCopied = false, 2000)" 
                    class="ml-2 p-1.5 rounded-full text-gray-300 hover:text-[#1400FB] hover:bg-gray-700 transition duration-150 relative" 
                    title="Copy Merchant ID"
                >
                    <span class="fa fa-copy"></span>
                    <span 
                        x-show="showCopied" 
                        x-transition.opacity.duration.300ms 
                        class="absolute right-0 bottom-full mb-1 px-2 py-0.5 text-xs font-semibold bg-green-500 text-white rounded shadow-md whitespace-nowrap"
                    >
                        Copied!
                    </span>
                </button>
            </div>
        </div>
    @endauth

    <ul class="flex-1 flex flex-col justify-between text-gray-300">
        <div class="space-y-3">
            
            <li class="w-full">
                <a href="{{route('home')}}" 
                   @class([
                       'font-semibold p-3 w-full flex items-center transition-all duration-200 rounded-xl', 
                       'text-gray-300 hover:bg-gray-700 hover:text-white' => !request()->routeIs('home'),
                       'text-white bg-[#1400FB] shadow-lg shadow-[#1400FB]/40' => request()->routeIs('home')
                   ])
                >
                    <span class="fa fa-home text-lg w-6 text-center mr-3"></span> 
                    <span>Home</span>
                </a>
            </li>

            <li class="w-full">
                <a href="{{route('gateway.transactions')}}" 
                   @class([
                       'font-semibold p-3 w-full flex items-center transition-all duration-200 rounded-xl', 
                       'text-gray-300 hover:bg-gray-700 hover:text-white' => !request()->routeIs('gateway.transactions'),
                       'text-white bg-[#1400FB] shadow-lg shadow-[#1400FB]/40' => request()->routeIs('gateway.transactions')
                   ])
                >
                    <span class="fa fa-receipt text-lg w-6 text-center mr-3"></span> 
                    <span>Transactions</span>
                </a>
            </li>

            <li class="w-full">
                <a href="{{route('gateway.payment-links')}}" 
                   @class([
                       'font-semibold p-3 w-full flex items-center transition-all duration-200 rounded-xl', 
                       'text-gray-300 hover:bg-gray-700 hover:text-white' => !request()->routeIs('gateway.payment-links'),
                       'text-white bg-[#1400FB] shadow-lg shadow-[#1400FB]/40' => request()->routeIs('gateway.payment-links')
                   ])
                >
                    <span class="fa fa-link text-lg w-6 text-center mr-3"></span> 
                    <span>Payment Links</span>
                </a>
            </li>

            <li class="w-full">
                <a href="{{route('gateway.virtual-accounts')}}" 
                   @class([
                       'font-semibold p-3 w-full flex items-center transition-all duration-200 rounded-xl', 
                       'text-gray-300 hover:bg-gray-700 hover:text-white' => !request()->routeIs('gateway.virtual-accounts'),
                       'text-white bg-[#1400FB] shadow-lg shadow-[#1400FB]/40' => request()->routeIs('gateway.virtual-accounts')
                   ])
                >
                    <span class="fa fa-building-columns text-lg w-6 text-center mr-3"></span> 
                    <span>Virtual Account</span>
                </a>
            </li>
            
            <li class="w-full">
                <a href="{{route('transfer.index')}}" 
                   @class([
                       'font-semibold p-3 w-full flex items-center transition-all duration-200 rounded-xl', 
                       'text-gray-300 hover:bg-gray-700 hover:text-white' => !request()->routeIs('transfer.*'),
                       'text-white bg-[#1400FB] shadow-lg shadow-[#1400FB]/40' => request()->routeIs('transfer.*')
                   ])
                >
                    <span class="fa fa-paper-plane text-lg w-6 text-center mr-3"></span> 
                    <span>Transfer</span>
                </a>
            </li>
            
        </div>

        <div class="space-y-3 mb-4">

            <li class="w-full">
                <a href="{{route('gateway.webhooks')}}" 
                   @class([
                       'font-semibold p-3 w-full flex items-center transition-all duration-200 rounded-xl', 
                       'text-gray-300 hover:bg-gray-700 hover:text-white' => !request()->routeIs('gateway.webhooks'),
                       'text-white bg-[#1400FB] shadow-lg shadow-[#1400FB]/40' => request()->routeIs('gateway.webhooks')
                   ])
                >
                    <span class="fa fa-plug text-lg w-6 text-center mr-3"></span> 
                    <span>Webhook Events</span>
                </a>
            </li>

            <li class="w-full">
                <a href="{{route('gateway.api-keys')}}" 
                   @class([
                       'font-semibold p-3 w-full flex items-center transition-all duration-200 rounded-xl', 
                       'text-gray-300 hover:bg-gray-700 hover:text-white' => !request()->routeIs('gateway.api-keys'),
                       'text-white bg-[#1400FB] shadow-lg shadow-[#1400FB]/40' => request()->routeIs('gateway.api-keys')
                   ])
                >
                    <span class="fa fa-key text-lg w-6 text-center mr-3"></span> 
                    <span>API Keys</span>
                </a>
            </li>

            <li class="w-full">
                <a href="{{route('gateway.preferences')}}" 
                   @class([
                       'font-semibold p-3 w-full flex items-center transition-all duration-200 rounded-xl', 
                       'text-gray-300 hover:bg-gray-700 hover:text-white' => !request()->routeIs('gateway.preferences'),
                       'text-white bg-[#1400FB] shadow-lg shadow-[#1400FB]/40' => request()->routeIs('gateway.preferences')
                   ])
                >
                    <span class="fa fa-sliders text-lg w-6 text-center mr-3"></span> 
                    <span>Preferences</span>
                </a>
            </li>

            <li class="w-full">
                <a href="{{route('gateway.docs')}}" 
                   @class([
                       'font-semibold p-3 w-full flex items-center transition-all duration-200 rounded-xl', 
                       'text-gray-300 hover:bg-gray-700 hover:text-white' => !request()->routeIs('gateway.docs'),
                       'text-white bg-[#1400FB] shadow-lg shadow-[#1400FB]/40' => request()->routeIs('gateway.docs')
                   ])
                >
                    <span class="fa fa-book text-lg w-6 text-center mr-3"></span> 
                    <span>Documentation</span>
                </a>
            </li>

            <li class="w-full">
                <a href="{{route('live-chat-welcome')}}" 
                   @class([
                       'font-semibold p-3 w-full flex items-center transition-all duration-200 rounded-xl', 
                       'text-gray-300 hover:bg-gray-700 hover:text-white' => !request()->routeIs('live-chat-welcome'),
                       'text-white bg-[#1400FB] shadow-lg shadow-[#1400FB]/40' => request()->routeIs('live-chat-welcome')
                   ])
                >
                    <span class="fa fa-headset text-lg w-6 text-center mr-3"></span> 
                    <span>Live Chat</span>
                </a>
            </li>

            <li class="w-full">
                <form method="POST" action="{{ route('logout') }}" class="inline-block w-full">
                    @csrf
                    <button 
                        class="font-semibold p-3 w-full block text-left rounded-xl transition-all duration-200 
                               text-red-400 hover:bg-red-900/40 hover:text-red-300" 
                        onclick="event.preventDefault(); this.closest('form').submit();"
                    >
                        <span class="fa fa-right-from-bracket text-lg w-6 text-center mr-3"></span> 
                        <span>Logout</span>
                    </button>
                </form>
            </li>


        </div>


    </ul>


</div>
