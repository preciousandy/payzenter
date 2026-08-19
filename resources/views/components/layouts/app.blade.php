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

        <title>{{ $title ?? 'Home' }} | {{ config('app.name') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://api.fontshare.com/v2/css?f[]=satoshi@300,301,400,401,500,501,700,701,900,901&display=swap" rel="stylesheet">

        <!-- Favicon and App Icons -->
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('logo/pzfav.jpg') }}">
        <link rel="icon" type="image/jpeg" sizes="32x32" href="{{ asset('logo/pzfav.jpg') }}">
        <link rel="icon" type="image/jpeg" sizes="16x16" href="{{ asset('logo/pzfav.jpg') }}">
        <link rel="manifest" href="{{ asset('favicons/site.webmanifest') }}">
        <link rel="mask-icon" href="{{ asset('logo/pzfav.jpg') }}" color="#0033cc">
        <meta name="msapplication-TileColor" content="#0033cc">
        <meta name="theme-color" content="#0033cc">

        {{-- font awesome --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

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

            /* html {
                scroll-behavior: smooth;
            } */

            meta[name="theme-color"] {
                content: "#ffffff" !important;
            }
        </style>

        @stack('styles')

    </head>

    <body class=" text-black bg-gray-50">

        <x-side-bar />

        <div class=" lg:ml-72">
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

            <x-header :headerTitle="$headerTitle ?? ''" />

            {{-- BVN verification --}}
            {{-- <x-bvn-verification /> --}}

            <main class=" pb-28">

                {{ $slot ?? '' }}
                @yield('content')
            </main>

            <x-bottom-nav />
        </div>


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


                    function copyToClipboard(text) {
                    // Create a temporary textarea element
                    const textarea = document.createElement("textarea");
                    textarea.value = text;
                    
                    // Prevent the textarea from being visible and disrupting the page
                    textarea.style.position = "fixed";
                    textarea.style.opacity = "0";
                    
                    // Append the textarea to the document
                    document.body.appendChild(textarea);
                    
                    // Select the text in the textarea
                    textarea.select();
                    
                    try {
                    // Execute the copy command
                    document.execCommand("copy");
                    
                    const el = document.getElementById('copy');
                    
                    // Use querySelectorAll to get all elements with the class 'copy'
                    const elClasses = document.querySelectorAll('.copy');
                    
                    if (el) {
                    el.innerText = "Copied!";
                    }
                    
                    // Loop through all elements with the 'copy' class and change their text
                    elClasses.forEach(element => {
                    element.innerText = "Copied!";
                    });
                    } catch (err) {
                    console.error("Failed to copy text to clipboard", err);
                    }
                    
                    // Remove the textarea element from the document
                    document.body.removeChild(textarea);
                    }
        </script>
        @stack('scripts')
    </body>

</html>
