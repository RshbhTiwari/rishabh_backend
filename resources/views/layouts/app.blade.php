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

        <!-- Styles -->
        @livewireStyles
    </head>
    <body
    x-data="{ page: 'ecommerce', 'loaded': true, 'darkMode': true, 'stickyMenu': false, 'sidebarToggle': false, 'scrollTop': false }"
    x-init="
         darkMode = JSON.parse(localStorage.getItem('darkMode'));
         $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))"
    :class="{'dark tex  t-bodydark bg-boxdark-2': darkMode === true}"
    >
        <x-banner />
        <!-- ===== Page Wrapper Start ===== -->
        <div class="flex h-screen overflow-hidden">
            <!-- ===== Sidebar Start ===== -->
            @livewire('navigation-menu')
            <!-- ===== Sidebar End ===== -->

            <!-- ===== Content Area Start ===== -->
            <div class="relative flex flex-1 flex-col overflow-y-auto overflow-x-hidden">
                <!-- ===== Header Start ===== -->
                @if(isset($header))
                <!-- Desktop Header -->
                <header class="w-full items-center bg-gray-100 py-2 px-6 hidden sm:flex">
                    <div class="w-1/2">{{ $header }}</div>
                    <div x-data="{ isOpen: false }" x-cloak class="relative w-1/2 flex justify-end">

                        <button @click="isOpen = !isOpen" class="realtive z-10 w-12 h-12">
                             admin
                        </button>
                        <button x-show="isOpen" @click="isOpen = false" class="h-full w-full fixed inset-0 cursor-default"></button>
                        <div x-show="isOpen" class="z-20 absolute w-32 bg-white rounded-lg shadow-lg py-2 mt-16">
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ __('Manage Account') }}
                            </div>

                            <x-dropdown-link  class="block px-4 py-2 account-link hover:text-white" href="{{ route('profile.show') }}">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                <x-dropdown-link class="block px-4 py-2 account-link hover:text-white" href="{{ route('api-tokens.index') }}">
                                    {{ __('API Tokens') }}
                                </x-dropdown-link>
                            @endif

                            <div class="border-t border-gray-200"></div>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf
                                <x-dropdown-link class="block px-4 py-2 account-link hover:text-white" href="{{ route('logout') }}"
                                         @click.prevent="$root.submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </div>
                    </div>
                </header>

                <!-- Mobile Header & Nav -->
                <header x-data="{ isOpen: false }" class="w-full bg-sidebar py-5 px-6 sm:hidden">
                    <div class="flex items-center justify-between">
                        <a href="{{ route('dashboard') }}" class="text-white text-3xl font-semibold uppercase hover:text-gray-300">Admin</a>
                        <button @click="isOpen = !isOpen" class="text-white text-3xl focus:outline-none">
                            <i x-show="!isOpen" class="fas fa-bars"></i>
                            <i x-show="isOpen" class="fas fa-times"></i>
                        </button>
                    </div>

                    <!-- Dropdown Nav -->
                    <nav :class="isOpen ? 'flex': 'hidden'" class="flex flex-col pt-4">

                        <a href="" class="flex items-center text-white opacity-75 hover:opacity-100 py-4 pl-6 nav-item">
                            <i class="fa-brands fa-product-hunt mr-3"></i>
                            {{ __('Products') }}
                        </a>
                    </nav>
                    <!-- <button class="w-full bg-white cta-btn font-semibold py-2 mt-5 rounded-br-lg rounded-bl-lg rounded-tr-lg shadow-lg hover:shadow-xl hover:bg-gray-300 flex items-center justify-center">
                        <i class="fas fa-plus mr-3"></i> New Report
                    </button> -->
                </header>
                @endif
                <!-- ===== Header End ===== -->
                <!-- ===== Main Content Start ===== -->
                <main>
                    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
                        <x-status-alert />
                        {{ $slot }}
                        <!-- ====== Table One Start -->
                        <div class="col-span-12 xl:col-span-8">
                        </div>
                    </div>
                </main>
            <!-- ===== Main Content End ===== -->
            </div>
          <!-- ===== Content Area End ===== -->
        </div>
        <!-- ===== Page Wrapper End ===== -->

        @stack('modals')

        @livewireScripts
    </body>
</html>
