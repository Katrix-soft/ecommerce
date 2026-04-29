@props(['breadcrumbs' => []])

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

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        {{-- iconos --}}
        <script src="https://kit.fontawesome.com/02148a3edd.js" crossorigin="anonymous"></script>

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased" 
            x-data="{ sidebarOpen:  false }"
            :class="{ 'overflow-y-hidden': sidebarOpen }">
  
        <div class="fixed inset-0 bg-gray-900/50 z-20 sm:hidden" 
        style="display: none;"
        x-show="sidebarOpen"
        x-transition.opacity.duration.300ms
        x-on:click="sidebarOpen = false">
        </div>

  @include('layouts.partials.admin.sidebar')
  @include('layouts.partials.admin.navigation')


<div class="p-4 sm:ml-64 transition-transform duration-300" :class="{ 'translate-x-64 sm:translate-x-0': sidebarOpen }">
    <div class=" mt-14">
        
        <div class="flex justify-between items-center">
            @include('layouts.partials.admin.breadcrumb')


            @isset($action)
            
                <div>
                 {{ $action }}
                </div>
            @endisset
        </div>

        <div class="p-4 border-1 border-default border-dashed rounded-base ">
     
     
         {{ $slot }}
     
         
        </div>
    </div>

</div>



        @livewireScripts
    </body>
</html>
