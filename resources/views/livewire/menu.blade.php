<div class="px-4 pt-6 pb-20 bg-gray-50 min-h-screen space-y-6 lg:max-w-xl lg:mx-auto">

    <!-- Gateway/Main Menu -->
    <section>
        <h3 class="text-xl font-extrabold text-gray-900 tracking-tight mb-3">Main Menu</h3>

        <ul class="bg-white rounded-xl shadow-lg border border-gray-100 divide-y divide-gray-100 text-base">
            
            <li>
                <a href="{{route('home')}}" class="flex items-center justify-between p-4 font-semibold text-gray-800 hover:bg-gray-50 rounded-t-xl transition duration-150">
                    <div class="flex items-center gap-3">
                        <span class="fa fa-home text-xl w-5 text-center text-[#1400FB]"></span>
                        <span>Home</span>
                    </div>
                    <span class="fa fa-chevron-right text-xs text-gray-400"></span>
                </a>
            </li>
            
            <li>
                <a href="{{route('gateway.transactions')}}" class="flex items-center justify-between p-4 font-semibold text-gray-800 hover:bg-gray-50 transition duration-150">
                    <div class="flex items-center gap-3">
                        <span class="fa fa-receipt text-xl w-5 text-center text-[#1400FB]"></span>
                        <span>Transactions</span>
                    </div>
                    <span class="fa fa-chevron-right text-xs text-gray-400"></span>
                </a>
            </li>
            
            <li>
                <a href="{{route('gateway.payment-links')}}" class="flex items-center justify-between p-4 font-semibold text-gray-800 hover:bg-gray-50 transition duration-150">
                    <div class="flex items-center gap-3">
                        <span class="fa fa-link text-xl w-5 text-center text-[#1400FB]"></span>
                        <span>Payment Links</span>
                    </div>
                    <span class="fa fa-chevron-right text-xs text-gray-400"></span>
                </a>
            </li>
            
            <li>
                <a href="{{route('gateway.virtual-accounts')}}" class="flex items-center justify-between p-4 font-semibold text-gray-800 hover:bg-gray-50 transition duration-150">
                    <div class="flex items-center gap-3">
                        <span class="fa fa-building-columns text-xl w-5 text-center text-[#1400FB]"></span>
                        <span>Virtual Accounts</span>
                    </div>
                    <span class="fa fa-chevron-right text-xs text-gray-400"></span>
                </a>
            </li>
            
            <li>
                <a href="{{route('transfer.index')}}" class="flex items-center justify-between p-4 font-semibold text-gray-800 hover:bg-gray-50 transition duration-150">
                    <div class="flex items-center gap-3">
                        <span class="fa fa-paper-plane text-xl w-5 text-center text-[#1400FB]"></span>
                        <span>Transfers</span>
                    </div>
                    <span class="fa fa-chevron-right text-xs text-gray-400"></span>
                </a>
            </li>
            
            <li>
                <a href="{{route('gateway.webhooks')}}" class="flex items-center justify-between p-4 font-semibold text-gray-800 hover:bg-gray-50 transition duration-150">
                    <div class="flex items-center gap-3">
                        <span class="fa fa-plug text-xl w-5 text-center text-[#1400FB]"></span>
                        <span>Webhook Events</span>
                    </div>
                    <span class="fa fa-chevron-right text-xs text-gray-400"></span>
                </a>
            </li>


              <li>
                <a href="{{route('gateway.preferences')}}" class="flex items-center justify-between p-4 font-semibold text-gray-800 hover:bg-gray-50 transition duration-150">
                    <div class="flex items-center gap-3">
                        <span class="fa fa-sliders  text-xl w-5 text-center text-[#1400FB]"></span>
                        <span>Preferences</span>
                    </div>
                    <span class="fa fa-chevron-right text-xs text-gray-400"></span>
                </a>
            </li>
            
            <li>
                <a href="{{route('gateway.api-keys')}}" class="flex items-center justify-between p-4 font-semibold text-gray-800 hover:bg-gray-50 transition duration-150">
                    <div class="flex items-center gap-3">
                        <span class="fa fa-key text-xl w-5 text-center text-[#1400FB]"></span>
                        <span>API Keys</span>
                    </div>
                    <span class="fa fa-chevron-right text-xs text-gray-400"></span>
                </a>
            </li>
            
            <li>
                <a href="{{route('gateway.docs')}}" class="flex items-center justify-between p-4 font-semibold text-gray-800 hover:bg-gray-50 rounded-b-xl transition duration-150">
                    <div class="flex items-center gap-3">
                        <span class="fa fa-book text-xl w-5 text-center text-[#1400FB]"></span>
                        <span>Documentation</span>
                    </div>
                    <span class="fa fa-chevron-right text-xs text-gray-400"></span>
                </a>
            </li>
        </ul>
    </section>

    <!-- Account Section -->
    <section>
        <h3 class="text-xl font-extrabold text-gray-900 tracking-tight mb-3">Account</h3>

        <ul class="bg-white rounded-xl shadow-lg border border-gray-100 divide-y divide-gray-100 text-base">
            
            <li>
                <a href="{{route('profile')}}" class="flex items-center justify-between p-4 font-semibold text-gray-800 hover:bg-gray-50 rounded-t-xl transition duration-150">
                    <div class="flex items-center gap-3">
                        <span class="fa fa-user-circle text-xl w-5 text-center text-[#1400FB]"></span>
                        <span>Profile Settings</span>
                    </div>
                    <span class="fa fa-chevron-right text-xs text-gray-400"></span>
                </a>
            </li> 
            
            <li>
                <a href="{{route('contact-support')}}" class="flex items-center justify-between p-4 font-semibold text-gray-800 hover:bg-gray-50 transition duration-150">
                    <div class="flex items-center gap-3">
                        <span class="fa fa-user-edit text-xl w-5 text-center text-[#1400FB]"></span>
                        <span>Edit Company Details</span>
                    </div>
                    <span class="fa fa-chevron-right text-xs text-gray-400"></span>
                </a>
            </li>
            
            <li>
                <a href="{{route('kyc.verify')}}" class="flex items-center justify-between p-4 font-semibold text-gray-800 hover:bg-gray-50 transition duration-150">
                    <div class="flex items-center gap-3">
                        <span class="fa fa-id-card text-xl w-5 text-center text-[#1400FB]"></span>
                        <span>Account Verification</span>
                    </div>
                    <span 
                        @class([
                            'inline-block px-3 py-1 text-xs font-bold rounded-full', 
                            'bg-green-100 text-green-700' => auth()->user()->kyc_status == 'Verified',
                            'bg-yellow-100 text-yellow-700' => auth()->user()->kyc_status == 'Pending',
                            'bg-red-100 text-red-700' => auth()->user()->kyc_status == 'Unverified',
                        ])
                    >
                        {{ucfirst(auth()->user()->kyc_status)}}
                    </span>
                </a>
            </li>
            
            <li>
                <a href="{{route('transaction-history')}}" class="flex items-center justify-between p-4 font-semibold text-gray-800 hover:bg-gray-50 rounded-b-xl transition duration-150">
                    <div class="flex items-center gap-3">
                        <span class="fa fa-history text-xl w-5 text-center text-[#1400FB]"></span>
                        <span>Transaction History</span>
                    </div>
                    <span class="fa fa-chevron-right text-xs text-gray-400"></span>
                </a>
            </li>
        </ul>
    </section>
    
    <!-- Security Section -->
    <section>
        <h3 class="text-xl font-extrabold text-gray-900 tracking-tight mb-3">Security</h3>

        <ul class="bg-white rounded-xl shadow-lg border border-gray-100 divide-y divide-gray-100 text-base">
            <li>
                <a href="{{route('profile.change-password')}}" class="flex items-center justify-between p-4 font-semibold text-gray-800 hover:bg-gray-50 rounded-t-xl transition duration-150">
                    <div class="flex items-center gap-3">
                        <span class="fa fa-key text-xl w-5 text-center text-[#1400FB]"></span>
                        <span>Change Password</span>
                    </div>
                    <span class="fa fa-chevron-right text-xs text-gray-400"></span>
                </a>
            </li>
            <li>
                <a href="{{route('profile.change-transfer-pin')}}" class="flex items-center justify-between p-4 font-semibold text-gray-800 hover:bg-gray-50 rounded-b-xl transition duration-150">
                    <div class="flex items-center gap-3">
                        <span class="fa fa-lock text-xl w-5 text-center text-[#1400FB]"></span>
                        <span>Change Transaction PIN</span>
                    </div>
                    <span class="fa fa-chevron-right text-xs text-gray-400"></span>
                </a>
            </li>
        </ul>
    </section>

    <!-- Resources & Support Section -->
    <section>
        <h3 class="text-xl font-extrabold text-gray-900 tracking-tight mb-3">Resources & Support</h3>

        <ul class="bg-white rounded-xl shadow-lg border border-gray-100 divide-y divide-gray-100 text-base">
            <li>
                <a href="{{route('terms-and-conditions')}}" target="_blank" class="flex items-center justify-between p-4 font-semibold text-gray-800 hover:bg-gray-50 rounded-t-xl transition duration-150">
                    <div class="flex items-center gap-3">
                        <span class="fa fa-file text-xl w-5 text-center text-[#1400FB]"></span>
                        <span>Terms & Conditions</span>
                    </div>
                    <span class="fa fa-external-link text-xs text-gray-400"></span>
                </a>
            </li>
            
            <li class="border-t border-gray-100">
                <a href="{{route('live-chat-welcome')}}" class="flex items-center justify-between p-4 font-semibold text-gray-800 hover:bg-gray-50 transition duration-150">
                    <div class="flex items-center gap-3">
                        <span class="fa fa-comments text-xl w-5 text-center text-[#1400FB]"></span>
                        <span>Live Chat Support</span>
                    </div>
                    <span class="fa fa-chevron-right text-xs text-gray-400"></span>
                </a>
            </li>
            <li>
                <a href="{{route('feedback.create')}}" class="flex items-center justify-between p-4 font-semibold text-gray-800 hover:bg-gray-50 rounded-b-xl transition duration-150">
                    <div class="flex items-center gap-3">
                        <span class="fa fa-envelope text-xl w-5 text-center text-[#1400FB]"></span>
                        <span>Send Report / Feedback</span>
                    </div>
                    <span class="fa fa-chevron-right text-xs text-gray-400"></span>
                </a>
            </li>
        </ul>
    </section>

    <!-- Logout -->
    <div class="pt-2">
        <button 
            class="block w-full p-4 font-bold text-red-700 bg-red-50 hover:bg-red-100 text-left rounded-xl transition duration-150 shadow-sm" 
            wire:click='logout'
        >
            <span class="fa fa-sign-out-alt mr-3"></span>
            Log Out
        </button>
    </div>
</div>
