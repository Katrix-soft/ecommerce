<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @stack('css')

        <script src="https://kit.fontawesome.com/02148a3edd.js" crossorigin="anonymous"></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- PWA -->
        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#3b226e">
        <link rel="apple-touch-icon" href="/icon-512.png">


    </head>
    <body class="font-sans antialiased">
        <x-banner />

        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            

            @livewire('navigation')

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
           <div class="mt-16"> @include('layouts.partials.app.footer')</div>
        </div>

        @stack('modals')

        @stack('js')

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        @if (session('swal'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({!! json_encode(session('swal')) !!});
                });
            </script>
        @endif

        <script>
            document.addEventListener('livewire:init', function() {
                Livewire.on('swal', (data) => {
                    Swal.fire(data[0]);
                });

                Livewire.on('alert', (message) => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Bien hecho!',
                        text: message,
                        confirmButtonColor: '#3085d6',
                    });
                });
            });
        </script>

        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js')
                        .then(reg => console.log('Service Worker: Registered'))
                        .catch(err => console.log('Service Worker: Error: ', err));
                });
            }
        </script>

        @if(class_exists('\App\Models\TenantModule') && \App\Models\TenantModule::where('module', 'chatbot')->where('is_enabled', true)->exists())
            @include('layouts.partials.chatbot')
        @endif
    </body>
</html>

