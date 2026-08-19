<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

  <head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{config('services.ga4.measurementId')}}"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
              function gtag(){dataLayer.push(arguments);}
              gtag('js', new Date());
            
              gtag('config', "{{config('services.ga4.measurementId')}}");
    </script>


    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Welcome' }} | {{ config('app.name') }}</title>

    <!-- Favicon and App Icons -->
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('logo/pzfav.jpg') }}">
        <link rel="icon" type="image/jpeg" sizes="32x32" href="{{ asset('logo/pzfav.jpg') }}">
        <link rel="icon" type="image/jpeg" sizes="16x16" href="{{ asset('logo/pzfav.jpg') }}">
        <link rel="manifest" href="{{ asset('favicons/site.webmanifest') }}">
        <link rel="mask-icon" href="{{ asset('logo/pzfav.jpg') }}" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#2d89ef">
    <meta name="theme-color" content="#ffffff">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    {{-- <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700,800,900&display=swap" rel="stylesheet" /> --}}
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@300,301,400,401,500,501,700,701,900,901&display=swap" rel="stylesheet">

    {{-- font awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- select2 cdn --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    <style>
      html {
        scroll-behavior: smooth;
      }

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
  </head>

  <body class=" text-black">

    @if (session('success'))
    <div x-data="modalTimeout" x-init="show()">
      <!-- Modal -->
      <div x-show="showModal" x-transition:enter="transition ease-out duration-300" x-transition:leave="transition ease-in duration-300" x-on:click.away="showModal = false" class=" fixed top-10 z-50 left-10 right-10 bg-green-50 p-4  border-l-4 border-green-700 rounded-md shadow-lg text-green-800 font-medium max-w-2xl mx-auto">
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

    {{ $slot ?? '' }}
    @yield('content')

    @stack('scripts')

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <x-livewire-alert::scripts />

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
    </script>
  </body>

</html>
