{{-- Mobile Admin Menu --}}
<ul class="space-y-2 mt-4">
  <li class="w-full">
    <a href="{{route('admin.dashboard')}}" @class(['font-medium p-3 w-full block hover:bg-indigo-500 rounded-lg', 'text-primary2 border-b-2 border-primary bg-white font-semibold'=> request()->routeIs('admin.dashboard')])>
      <span class="fa fa-tachometer-alt mr-2"></span> Dashboard
    </a>
  </li>

  @can('view_users')
  <li class="w-full">
    <a href="{{route('admin.users.index')}}" @class(['font-medium p-3 w-full block hover:bg-indigo-500 rounded-lg', 'text-primary2 border-b-2 border-primary bg-white font-semibold'=> request()->routeIs('admin.users.*')])>
      <span class="fa fa-users mr-2"></span> Users
    </a>
  </li>
  @endcan

  @can('manage_kyc')
  <li class="w-full">
    <a href="{{route('admin.kyc-verification')}}" @class(['font-medium p-3 w-full block hover:bg-indigo-500 rounded-lg flex items-center justify-between', 'text-primary2 border-b-2 border-primary bg-white font-semibold'=> request()->routeIs('admin.kyc-verification')])>
      <span>
        <span class="fa fa-id-card mr-2"></span> KYC Verification
      </span>
      @if ($this->pendingKycCount >= 1)
      <button class="bg-red-700 p-2 text-white h-6 w-6 rounded-full flex justify-center items-center font-semibold">
        {{$this->pendingKycCount}}
      </button>
      @endif
    </a>
  </li>
  @endcan

  @can('view_user_transactions')
  <li class="w-full">
    <a href="{{route('admin.transactions')}}" @class(['font-medium p-3 w-full block hover:bg-indigo-500 rounded-lg', 'text-primary2 border-b-2 border-primary bg-white font-semibold'=> request()->routeIs('admin.transactions')])>
      <span class="fa fa-credit-card mr-2"></span> User Transactions
    </a>
  </li>
  @endcan

  @can('view_user_payments')
  <li class="w-full">
    <a href="{{route('admin.payment-transactions')}}" @class(['font-medium p-3 w-full block hover:bg-indigo-500 rounded-lg', 'text-primary2 border-b-2 border-primary bg-white font-semibold'=> request()->routeIs('admin.payment-transactions')])>
      <span class="fa fa-wallet mr-2"></span> Payment Transactions
    </a>
  </li>
  @endcan

  <li class="w-full">
    <a href="{{route('admin.settlement-accounts')}}" @class(['font-medium p-3 w-full block hover:bg-indigo-500 rounded-lg', 'text-primary2 border-b-2 border-primary bg-white font-semibold'=> request()->routeIs('admin.settlement-accounts')])>
      <span class="fa fa-university mr-2"></span> Settlement Accounts
    </a>
  </li>

 



  @can('view_users_feedback')
  <li class="w-full">
    <a href="{{route('admin.all-feedbacks')}}" @class(['font-medium p-3 w-full block hover:bg-indigo-500 rounded-lg', 'text-primary2 border-b-2 border-primary bg-white font-semibold'=> request()->routeIs('admin.all-feedbacks')])>
      <span class="fa fa-comments mr-2"></span> Users Feedback
    </a>
  </li>
  @endcan

  @can('manage_live_support')
  <li class="w-full">
    <a href="{{route('admin.live-support')}}" @class(['font-medium p-3 w-full block hover:bg-indigo-500 rounded-lg', 'text-primary2 border-b-2 border-primary bg-white font-semibold'=> request()->routeIs('admin.live-support')])>
      <span class="fa fa-headset mr-2"></span> Live Support
    </a>
  </li>
  @endcan

  @can('manage_error_logs')
  <li class="w-full">
    <a href="{{route('admin.error-logs')}}" @class(['font-medium p-3 w-full block hover:bg-indigo-500 rounded-lg flex items-center justify-between', 'text-primary2 border-b-2 border-primary bg-white font-semibold'=> request()->routeIs('admin.error-logs')])>
      <span>
        <span class="fa fa-bug mr-2"></span> Error Logs
      </span>
      @if ($this->openErrorsCount >= 1)
      <button class="bg-red-700 p-2 text-white h-6 w-6 rounded-full flex justify-center items-center font-semibold">
        {{$this->openErrorsCount}}
      </button>
      @endif
    </a>
  </li>
  @endcan

 

 

  @can('view_roles_and_permissions')
  <li class="w-full">
    <a href="{{route('admin.manage-roles-and-permissions')}}" @class(['font-medium p-3 w-full block hover:bg-indigo-500 rounded-lg', 'text-primary2 border-b-2 border-primary bg-white font-semibold'=> request()->routeIs('admin.manage-roles-and-permissions')])>
      <span class="fa fa-key mr-2"></span> Roles & Permissions
    </a>
  </li>
  @endcan

  @can('manage_website_content')
  <li class="w-full">
    <a href="{{route('admin.website-content')}}" @class(['font-medium p-3 w-full block hover:bg-indigo-500 rounded-lg', 'text-primary2 border-b-2 border-primary bg-white font-semibold'=> request()->routeIs('admin.website-content')])>
      <span class="fa fa-globe mr-2"></span> Website Content
    </a>
  </li>
  @endcan

  @can('view_settings')
  <li class="w-full">
    <a href="{{route('admin.settings')}}" @class(['font-medium p-3 w-full block hover:bg-indigo-500 rounded-lg', 'text-primary2 border-b-2 border-primary bg-white font-semibold'=> request()->routeIs('admin.settings')])>
      <span class="fa fa-gear mr-2"></span> Settings
    </a>
  </li>
  @endcan

{{--
  @can('view_settings')
  <li class="w-full">
    <a href="{{route('admin.api-keys')}}" @class(['font-medium p-3 w-full block hover:bg-indigo-500 rounded-lg', 'text-primary2 border-b-2 border-primary bg-white font-semibold'=> request()->routeIs('admin.api-keys')])>
      <span class="fa fa-key mr-2"></span> API Keys
    </a>
  </li>
  @endcan--}}


</ul>