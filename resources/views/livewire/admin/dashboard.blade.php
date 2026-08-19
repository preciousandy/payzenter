<div class="p-4 sm:p-6 bg-gray-50 min-h-screen">

    {{-- Dashboard Title --}}
    <h1 class="text-3xl font-extrabold text-gray-900 mb-8 border-b pb-2 tracking-tight">
        📊 Admin Control Dashboard
    </h1>

    <div class="space-y-10">
        @can('view_dashboard')

        {{-- USER STATS SECTION --}}
        <section>
            <div class="flex items-center space-x-3 mb-4 p-3 bg-white border-l-4 border-indigo-600 rounded-lg shadow-sm">
                <i class="fa fa-users text-2xl text-indigo-600"></i>
                <h2 class="font-bold text-xl text-gray-700">User Analytics</h2>
            </div>
            
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                <x-stats-card title="Total Users" :value="$totalUsers" icon="fa-users" color="indigo" />
                <x-stats-card title="Total Wallet Balance" :value="$totalWalletBalance" if_currency="{{true}}" icon="fa-wallet" color="green" />
                <x-stats-card title="KYC Verified Users" :value="$kycVerifiedUsers" icon="fa-check-circle" color="teal" />
                <x-stats-card title="OTP Verified Users" :value="$OtpVerifiedUsers" icon="fa-mobile-alt" color="blue" />
                <x-stats-card title="Users This Month" :value="$usersThisMonth" icon="fa-calendar-alt" color="gray" />
            </div>

            <div class="grid grid-cols-2 gap-4 mt-4 md:grid-cols-4">
                <x-stats-card title="Users This Week" :value="$usersThisWeek" icon="fa-calendar-week" color="gray" />
                <x-stats-card title="Website Users" :value="$usersOnWeb" icon="fa-globe" color="yellow" />
                
            </div>
        </section>

        <hr class="border-gray-300">

        {{-- TRANSACTION & PROFIT SECTION --}}
        <section>
            <div class="flex items-center space-x-3 mb-4 p-3 bg-white border-l-4 border-indigo-600 rounded-lg shadow-sm">
                <i class="fa fa-chart-line text-2xl text-indigo-600"></i>
                <h2 class="font-bold text-xl text-gray-700">Transaction & Revenue Overview</h2>
            </div>

            {{-- Transaction Key Metrics --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <x-stats-card title="Total Transactions" :value="$totalTransactions" icon="fa-list-alt" color="purple" />
                <x-stats-card title="Total Volume" :value="$totalTransactionsVolume" if_currency="{{true}}" icon="fa-dollar-sign" color="green" />
                <x-stats-card title="Users That Made transactions" :value="$totalUsersThatMadeTransaction" icon="fa-user-check" color="blue" />
                <x-stats-card title="All Time Fees" :value="$all_time_fees" if_currency="{{true}}" icon="fa-money-bill-wave" color="red" />
            </div>

            {{-- Profit & Daily Breakdown --}}
            <div class="mt-8">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Transaction Fees Profit</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <x-stats-card title="Today's Fees" :value="$todays_fees" if_currency="{{true}}" icon="fa-sun" color="green" />
                    <x-stats-card title="This Week's Fees" :value="$this_weeks_fees" if_currency="{{true}}" icon="fa-calendar-week" color="green" />
                    <x-stats-card title="This Month's Fees" :value="$this_months_fees" if_currency="{{true}}" icon="fa-calendar" color="green" />
                </div>
            </div>

            {{-- Charts Panel --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8 p-6 bg-white rounded-xl shadow-lg border border-gray-100">
                <x-display-chart :chartLabels="$transactionDates" :chartData="$dailyTransactions" title="Daily Transactions Count" />
                <x-display-chart :chartLabels="$transactionDates" :chartData="$dailyTransactionsVolume" title="Transaction Volume (Value)" />
            </div>
        </section>

        <hr class="border-gray-300">

        {{-- TRANSFER & BILLS SECTION --}}
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- Bank Transfers --}}
            <div class="bg-white p-6 rounded-xl shadow-md">
                <h3 class="font-bold text-lg text-indigo-600 mb-4 flex items-center"><i class="fa fa-university mr-2"></i> Bank Transfers</h3>
                <div class="space-y-3">
                    <x-stats-card title="Total Transfers" :value="$totalBankTransfers" class="border-b pb-2" />
                    <x-stats-card title="Transfers Volume" :value="$totalBankTransfersVolume" if_currency="{{true}}" class="border-b pb-2" />
                    <x-stats-card title="Transfers Users" :value="$totalBankTransfersUsers" />
                </div>
            </div>

            {{-- {{ config('app.name') }} Transfers --}}
            <div class="bg-white p-6 rounded-xl shadow-md">
                <h3 class="font-bold text-lg text-indigo-600 mb-4 flex items-center"><i class="fa fa-bolt mr-2"></i> {{ config('app.name') }} Transfers</h3>
                <div class="space-y-3">
                    <x-stats-card title="Total Transfers" :value="$totalSwiftpayTransfers" class="border-b pb-2" />
                    <x-stats-card title="Transfers Volume" :value="$swiftpayTransfersVolume" if_currency="{{true}}" class="border-b pb-2" />
                    <x-stats-card title="Transfers Users" :value="$swiftpayTransfersUsers" />
                </div>
            </div>

         

          
            
            {{-- Multiple Transfers --}}
            <div class="bg-white p-6 rounded-xl shadow-md">
                <h3 class="font-bold text-lg text-indigo-600 mb-4 flex items-center"><i class="fa fa-clone mr-2"></i> Multiple Transfers</h3>
                <div class="space-y-3">
                    <x-stats-card title="Total Transfers" :value="$totalMultipleTransfers" class="border-b pb-2" />
                    <x-stats-card title="Transfer Volume" :value="$totalMultipleTransfersVolume" if_currency="{{true}}" class="border-b pb-2" />
                    <x-stats-card title="Transfer Users" :value="$totalMultipleTransfersUsers" />
                </div>
            </div>

        </section>

        

        
        <hr class="border-gray-300">

       
        @endcan
    </div>
</div>
