<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" prefix="og: https://ogp.me/ns#">

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

        <!-- Primary Meta Tags -->
        <title>{{ $title ?? 'Welcome' }} | {{ config('app.name') }} - Seamless Transactions</title>
        <meta name="title" content="{{ $title ?? 'Welcome' }} | {{ config('app.name') }} - Seamless Financial Transactions">
        <meta name="description" content="{{get_website_content('description')}}">

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ config('app.url') }}">
        <meta property="og:title" content="{{ $title ?? 'Welcome' }} | {{ config('app.name') }} - Seamless Financial Transactions">
        <meta property="og:description" content="{{get_website_content('description')}}">
        <meta property="og:image" content="{{ asset('logo-2.png') }}">

        <!-- Twitter -->
        <meta property="twitter:card" content="summary_large_image">
        <meta property="twitter:url" content="{{ config('app.url') }}">
        <meta property="twitter:title" content="{{ $title ?? 'Welcome' }} | {{ config('app.name') }} - Seamless Financial Transactions">
        <meta property="twitter:description" content="{{get_website_content('description')}}">
        <meta property="twitter:image" content="{{ asset('logo-2.png')}}">

        <!-- Favicon and App Icons -->
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('logo/pzfav.jpg') }}">
        <link rel="icon" type="image/jpeg" sizes="32x32" href="{{ asset('logo/pzfav.jpg') }}">
        <link rel="icon" type="image/jpeg" sizes="16x16" href="{{ asset('logo/pzfav.jpg') }}">
        <link rel="manifest" href="{{ asset('favicons/site.webmanifest') }}">
        <link rel="mask-icon" href="{{ asset('logo/pzfav.jpg') }}" color="#0033cc">
        <meta name="msapplication-TileColor" content="#0033cc">
        <meta name="theme-color" content="#0033cc">

        <!-- Canonical URL -->
        <link rel="canonical" href="{{ url()->current() }}">

        <!-- Robots Meta Tag -->
        <meta name="robots" content="index, follow">

        <!-- Geo Tags (Optional) -->
        <meta name="geo.region" content="{{ config('app.geo_region', 'US') }}">
        <meta name="geo.position" content="{{ config('app.geo_position', '') }}">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Fonts -->
        {{-- <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700,800,900&display=swap" rel="stylesheet" /> --}}
        <!-- Fonts -->
        <link href="https://api.fontshare.com/v2/css?f[]=satoshi@300,301,400,401,500,501,700,701,900,901&display=swap" rel="stylesheet">

        {{-- font awesome --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />


        <link href="https://api.fontshare.com/v2/css?f[]=satoshi@300,301,400,401,500,501,700,701,900,901&display=swap" rel="stylesheet">

        {{-- select2 cdn --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        <style>
            * {
                font-family: 'Satoshi', sans-serif;
            }

            [x-cloak] {
                display: none;
            }

            html {
                scroll-behavior: smooth;
            }

            meta[name="theme-color"] {
                content: "#ffffff" !important;
            }
        </style>

        <script>
            document.documentElement.classList.add('js')
        </script>

        @stack('styles')
    </head>

    <body class=" text-black ">



        <x-guest-navbar />

        <main class="">

            {{ $slot ?? '' }}
            @yield('content')
        </main>

        @livewire('footer')

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
        </script>

        <script src="https://unpkg.com/taos@1.0.5/dist/taos.js"></script>
    </body>

</html>
