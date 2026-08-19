<div class="  min-h-screen flex flex-col " style="background-image: url('{{asset('transfer-card.png')}}')">

    <div class=" text-white py-6 space-y-6">
        <h2 class=" font-medium text-center">
            Wallet Balance oo
        </h2>

        <h1 class=" text-center text-4xl font-extrabold">
            <x-naira-symbol /> {{ session('showBalance', true) ? formatNumber(auth()->user()->wallet_balance) : '****' }}
        </h1>
    </div>


    <div class=" bg-white flex-1 rounded-t-[2.5rem] px-4  ">
        {{$slot}}
    </div>

</div>
