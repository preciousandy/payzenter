<div>
    <div class=" lg:hidden">
        <x-transfer-layout>
            <x-transfer-links />
        </x-transfer-layout>
    </div>

    <div class=" hidden lg:block p-8 max-w-6xl">
        <div class=" grid lg:grid-cols-2 lg:gap-6">
            <div class=" bg-white border border-gray-200  p-4 rounded-xl space-y-4 lg:space-y-8">
                <p class=" text-sm lg:text-base bg-gray-100 p-3 w-fit rounded-lg">
                    🇳🇬 Nigeria
                </p>


                <div class=" flex justify-between items-center">
                    <h1 class=" text-center text-3xl lg:text-4xl font-extrabold">
                        <x-naira-symbol /> {{ session('showBalance', true) ? formatNumber(auth()->user()->wallet_balance) : '****' }}
                    </h1>

                </div>
            </div>

            <div class="p-5 hidden lg:block bg-white border border-gray-200 rounded-xl">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 lg:text-xl">
                            Settlement Withdrawals
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Move funds from your merchant balance to your saved settlement account.
                        </p>
                    </div>
                    <div class="h-12 w-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                        <span class="fa fa-building-columns text-lg"></span>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-2 gap-4">
                    <div class="rounded-xl bg-gray-50 border border-gray-100 p-4">
                        <p class="text-xs uppercase tracking-wide text-gray-500">Merchant</p>
                        <p class="mt-2 font-semibold text-gray-900 lg:text-lg">{{ auth()->user()->name }}</p>
                    </div>
                    <div class="rounded-xl bg-gray-50 border border-gray-100 p-4">
                        <p class="text-xs uppercase tracking-wide text-gray-500">Status</p>
                        <p class="mt-2 font-semibold text-green-600 lg:text-lg">Ready for payout</p>
                    </div>
                </div>

                <div class="mt-4 rounded-2xl bg-gradient-to-r from-[#1400FB] to-[#3B82F6] p-5 text-white">
                    <p class="text-sm text-indigo-100">Available Balance</p>
                    <p class="mt-2 text-3xl font-extrabold">
                        <x-naira-symbol /> {{ session('showBalance', true) ? formatNumber(auth()->user()->wallet_balance) : '****' }}
                    </p>
                    <p class="mt-2 text-sm text-indigo-100">
                        Withdrawals go only to your configured settlement account.
                    </p>
                </div>

                <div class="mt-4 space-y-3">
                    <div class="flex items-center gap-3 rounded-xl border border-gray-100 p-3">
                        <span class="fa fa-check-circle text-green-600"></span>
                        <p class="text-sm text-gray-600">Secure merchant payout flow</p>
                    </div>
                    <div class="flex items-center gap-3 rounded-xl border border-gray-100 p-3">
                        <span class="fa fa-clock text-indigo-600"></span>
                        <p class="text-sm text-gray-600">Track every withdrawal from transaction history</p>
                    </div>
                    <div class="flex items-center gap-3 rounded-xl border border-gray-100 p-3">
                        <span class="fa fa-shield text-sky-600"></span>
                        <p class="text-sm text-gray-600">Settlement account details stay fixed for safer payouts</p>
                    </div>
                </div>
            </div>
        </div>


        <div class=" max-w-lg mx-auto">
            <x-transfer-links />
        </div>
    </div>
</div>
