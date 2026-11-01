<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Favicon and App Icons -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('logo/pzfav.jpg') }}">
    <link rel="icon" type="image/jpeg" sizes="32x32" href="{{ asset('logo/pzfav.jpg') }}">
    <link rel="icon" type="image/jpeg" sizes="16x16" href="{{ asset('logo/pzfav.jpg') }}">
    <link rel="manifest" href="{{ asset('favicons/site.webmanifest') }}">
    <link rel="mask-icon" href="{{ asset('logo/pzfav.jpg') }}" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#2d89ef">
    <meta name="theme-color" content="#ffffff">

    <!-- Fonts -->
    {{-- <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=raleway:400,500,600,700,800&display=swap" rel="stylesheet" /> --}}

    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@300,301,400,401,500,501,700,701,900,901&display=swap" rel="stylesheet">

    {{-- font awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    <style>
      /* html {
        scroll-behavior: smooth;
      } */

      * {
        font-family: 'Satoshi', sans-serif;
      }

      [x-cloak] {
        display: none;
      }

      meta[name="theme-color"] {
        content: "#ffffff" !important;
      }
    </style>

    @stack('styles')
  </head>

  <body class="bg-slate-100 text-black" x-data="{ mobileSidebarOpen: false }" @toggle-mobile-sidebar.window="mobileSidebarOpen = !mobileSidebarOpen">

    @if (session('success'))
    <div x-data="modalTimeout" x-init="show()">
      <!-- Modal -->
      <div x-show="showModal" x-transition:enter="transition ease-out duration-300" x-transition:leave="transition ease-in duration-300" x-on:click.away="showModal = false" class=" fixed top-10 z-50 left-10 right-10 bg-green-50 p-4  border-l-4 border-green-700 rounded-md shadow-2xl text-green-800 font-medium max-w-2xl mx-auto">
        <div class="modal-content">
          <p>{{session('success') }}</p>
        </div>
      </div>
    </div>
    @endif

    @if (session('error'))
    <div x-data="modalTimeout" x-init="show()">
      <!-- Modal -->
      <div x-show="showModal" x-transition:enter="transition ease-out duration-300" x-transition:leave="transition ease-in duration-300" x-on:click.away="showModal = false" class=" fixed top-10 z-50 left-10 right-10 bg-red-50 p-4  border-l-4 border-red-700 rounded-md shadow-lg text-red-800 font-medium max-w-2xl mx-auto">
        <div class="modal-content">
          <p>{{session('error') }}</p>
        </div>
      </div>
    </div>
    @endif

    <livewire:admin.admin-side-bar>

      <!-- Mobile Sidebar Overlay -->
      <div x-show="mobileSidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-30 bg-gray-600 bg-opacity-75 lg:hidden" @click="mobileSidebarOpen = false" style="display: none;">
      </div>

      <!-- Mobile Sidebar -->
      <div x-show="mobileSidebarOpen" x-transition:enter="transition ease-in-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="fixed inset-y-0 left-0 z-40 w-72 bg-primary2 text-white lg:hidden overflow-y-auto" style="display: none;">

        <div class="flex items-center justify-between p-4 border-b border-indigo-500">
          <a href="{{ route('admin.dashboard') }}" class="flex items-center text-xl font-bold">
            <img src="{{asset('logo/pzwhlogo.png')}}" alt="Logo" class="h-10 mr-3">
            {{ config('app.name') }} <span class="text-sm self-end">Admin</span>
          </a>
          <button @click="mobileSidebarOpen = false" class="text-white hover:text-gray-300 focus:outline-none">
            <i class="fa fa-times text-xl"></i>
          </button>
        </div>

        <div class="p-4">
          <livewire:admin.admin-mobile-menu />
        </div>
      </div>

      <div class="flex-1 lg:ml-72 min-w-0">

        <x-adminheader />

        <main class="p-2 sm:p-4 md:p-6">
          {{ $slot ?? '' }}
          @yield('content')
        </main>
      </div>

      <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

      <x-livewire-alert::scripts />
      @stack('scripts')

      <script>
        document.addEventListener('alpine:init', () => {
          Alpine.data('modalTimeout', () => ({
            showModal: false,
            show() {
              this.showModal = true;
              setTimeout(() => {
                this.showModal = false;
              }, 4000); 
            },
          }));
        });

        // Redirect to admin login if session expires (Livewire or fetch)
        document.addEventListener('livewire:init', () => {
          if (typeof Livewire !== 'undefined' && Livewire.hook) {
            Livewire.hook('request', ({ uri, options, payload, respond, succeed, fail }) => {
              fail(({ status, preventDefault }) => {
                if (status === 419 || status === 401) {
                  preventDefault();
                  window.location.href = "{{ route('admin.login') }}";
                }
              });
            });
          }
        });

        if (window.fetch) {
          const originalFetch = window.fetch;
          window.fetch = async (...args) => {
            const response = await originalFetch(...args);
            if (response.status === 419 || response.status === 401) {
              window.location.href = "{{ route('admin.login') }}";
            }
            return response;
          };
        }
      </script>

  </body>

</html>
