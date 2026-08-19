<div class="  bg-white ">
    <div class="px-4 py-6 max-w-7xl   min-h-screen lg:grid lg:grid-cols-5 lg:gap-6">
        <div class="  lg:col-span-3">
            <div class=" bg-primary text-white p-6 lg:p-8  rounded-xl flex items-center justify-between">
                <div>
                    <h3 class=" font-medium lg:font-semibold lg:text-lg">My SwiftPay Balance</h3>

                    <p class=" text-2xl lg:text-4xl font-bold mt-4">
                        <x-naira-symbol /> {{ formatNumber(auth()->user()->wallet_balance) }}
                    </p>
                </div>

                <button>
                    <span class=" fa fa-chevron-right text-2xl"></span>
                </button>
            </div>



            <div class=" mt-8 lg:mt-10 space-y-6 lg:space-y-8">
                <a href="{{ route('savings.interest-savings.welcome')}}" class=" bg-gray-100 p-4 md:p-6 rounded-xl flex items-center justify-between  gap-2 shadow">

                    <div class=" space-y-3 lg:space-y-5">
                        <h2 class=" text-base md:text-xl lg:text-2xl font-semibold">
                            <span class=" fa fa-user mr-2 inline-flex bg-primary text-white h-8 w-8 lg:h-12 lg:w-12 items-center justify-center rounded-full"></span> Save with Interests
                        </h2>

                        <p class=" text-sm md:text-base lg:text-lg text-gray-600">
                            Save daily, weekly & monthly and get interest on your savings, lock your savings and create multiple savings account/wallet.
                        </p>

                    </div>
                    <img src="{{asset('save-with-interest.png')}}" alt="" class=" h-16 w-20  lg:h-36 ">
                </a>

                <a href="{{route('savings.group-savings.welcome')}}" class=" bg-gray-100 p-4 md:p-6 rounded-xl flex items-center justify-between  gap-2 shadow">

                    <div class=" space-y-3 lg:space-y-5">
                        <h2 class=" text-base md:text-xl lg:text-2xl font-semibold">
                            <span class=" fa fa-users mr-2 inline-flex bg-primary text-white h-8 w-8 lg:h-12 lg:w-12 items-center justify-center rounded-full"></span>
                            Group Savings
                        </h2>

                        <p class=" text-sm md:text-base lg:text-lg text-gray-600">
                            Create your joint wallet & save with your friends, family or business partners. Save for a project or business, create a family savings online with your spouse. Monitor your members & the money saved by each member.
                        </p>


                    </div>

                    <img src="{{asset('group-savings.png')}}" alt="" class="h-16 w-20  lg:h-36">
                </a>
            </div>
        </div>

        <div class="lg:col-span-2 hidden lg:block">
            <div class=" border border-gray-200 p-4 rounded-xl shadow-sm">
                <h3 class=" text-xl font-semibold">
                    Recent Interest Savings
                </h3>
                <div class=" mt-6 space-y-4">
                    @forelse ($interestSavings as $item)
                    <x-savings-item :item="$item" :lockedSavingsInterestRate="$lockedSavingsInterestRate" :unlockedSavingsInterestRate="$unlockedSavingsInterestRate" />
                    @empty
                    <div class="  p-6 ">
                        <p class=" text-center">
                            You have no savings yet
                        </p>
                    </div>
                    @endforelse
                </div>
            </div>

            <div class=" border border-gray-200 p-4 rounded-xl shadow-sm mt-8">
                <h3 class=" text-xl font-semibold">
                    Group Savings
                </h3>
                <div class=" mt-6 space-y-4">
                    @forelse ($groupSavings as $group_item)
                    <x-group-savings-item :item="$group_item" />
                    @empty
                    <div class="  p-6 ">
                        <div class="  p-6 ">
                            <p class=" text-center">You are not a member of any group savings</p>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>