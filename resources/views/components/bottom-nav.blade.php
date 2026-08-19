<div class="fixed bottom-0 left-0 w-full bg-white p-3 lg:hidden shadow-[0_-4px_10px_rgba(0,0,0,0.05)] border-t border-gray-100">
    <div class="flex items-center justify-around">

        <a href="{{route('home')}}" 
           @class([
               'flex flex-col items-center p-1 rounded-lg transition-all duration-200',
               'text-gray-500 hover:text-gray-900' => !request()->routeIs('home'),
               'text-[#1400FB] font-semibold transform scale-105' => request()->routeIs('home')
           ])
        >
            <span class="fa fa-house block w-fit text-xl"></span>
            <p class="text-xs mt-1">Home</p>
        </a>

        <a href="{{route('transfer.index')}}" 
           @class([
               'flex flex-col items-center p-1 rounded-lg transition-all duration-200',
               'text-gray-500 hover:text-gray-900' => !request()->routeIs('transfer.*'),
               'text-[#1400FB] font-semibold transform scale-105' => request()->routeIs('transfer.*')
           ]) 
        >
            <span class="fa fa-paper-plane block w-fit text-xl"></span>
            <p class="text-xs mt-1">Transfer</p>
        </a>

        <a href="{{route('savings.index')}}" @class([
               'hidden', // Hide by default if the items are supposed to be commented out/hidden
               'flex flex-col items-center p-1 rounded-lg transition-all duration-200',
               'text-gray-500 hover:text-gray-900' => !request()->routeIs('savings.*'),
               'text-[#1400FB] font-semibold transform scale-105' => request()->routeIs('savings.*')
           ])
        >
             </a>
        
        <a href="{{route('bills.index')}}" @class([
               'hidden',
               'flex flex-col items-center p-1 rounded-lg transition-all duration-200',
               'text-gray-500 hover:text-gray-900' => !request()->routeIs('bills.*'),
               'text-[#1400FB] font-semibold transform scale-105' => request()->routeIs('bills.*')
           ])
        >
            </a>

        <a href="{{route('cards.index')}}" @class([
               'hidden',
               'flex flex-col items-center p-1 rounded-lg transition-all duration-200',
               'text-gray-500 hover:text-gray-900' => !request()->routeIs('cards.*'),
               'text-[#1400FB] font-semibold transform scale-105' => request()->routeIs('cards.*')
           ])
        >
            </a>
        

    </div>
</div>