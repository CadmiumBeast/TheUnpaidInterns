
<!DOCTYPE html>
<<<<<<< HEAD
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <x-app-logo />
            </a>

            <flux:navlist variant="outline">
                <flux:navlist.group :heading="__('Platform')" class="grid">
                    <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
                    <flux:navlist.item icon="chat-bubble-left-right" :href="route('complaints.index')" :current="request()->routeIs('complaints.*')" wire:navigate>{{ __('Complaints') }}</flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist>

            <flux:spacer />

            <flux:navlist variant="outline">
                <flux:navlist.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                {{ __('Repository') }}
                </flux:navlist.item>

                <flux:navlist.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                {{ __('Documentation') }}
                </flux:navlist.item>
            </flux:navlist>

            <!-- Desktop User Menu -->
            <flux:dropdown class="hidden lg:block" position="bottom" align="start">
                <flux:profile
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    icon:trailing="chevrons-up-down"
                />

                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>
=======
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GovCare - Dashboard</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'teal': {
                            500: '#14b8a6',
                            600: '#0d9488',
                            700: '#0f766e'
                        },
                        'green': {
                            500: '#22c55e',
                            600: '#16a34a'
                        },
                        'blue': {
                            400: '#60a5fa',
                            500: '#3b82f6'
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-900 font-sans">
    <!-- Header Section -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="container mx-auto px-6 py-4">
            <div class="flex justify-between items-center mb-4">
                <!-- Logo and Branding -->
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/GovCare_Logo_Small.png') }}" alt="GovCare Logo" width="200px" height="auto">
                </div>
                <!-- Logout Button -->
                <form method="POST" action="{{ route('logout') }}" class="flex items-center">
                    @csrf
                    <button type="submit" class="flex items-center px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-lg shadow transition-colors">
                        <svg class="w-4 h-4 mr-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
            <!-- Navigation Tabs -->
            <nav class="flex justify-center mt-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 flex space-x-2 px-2 py-1">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-5 py-2 rounded-lg text-gray-600 hover:text-teal-700 hover:bg-teal-50 font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-teal-100 text-teal-700 font-semibold shadow' : '' }}">
                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6m-6 0H7m6 0v6m0 0H7m6 0h6"></path></svg>
                        Dashboard
                    </a>
                    @if(auth()->check() && auth()->user()->type === 'admin')
                        <a href="{{ route('admin.appointments.index') }}" class="flex items-center px-5 py-2 rounded-lg text-gray-600 hover:text-teal-700 hover:bg-teal-50 font-medium transition-colors {{ request()->routeIs('admin.appointments.*') ? 'bg-teal-100 text-teal-700 font-semibold shadow' : '' }}">
                            <svg class="w-4 h-4 mr-2 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 018 0v2M9 7a4 4 0 100-8 4 4 0 000 8zm6 8v-2a6 6 0 00-12 0v2"></path></svg>
                            Appointments
                        </a>
                        <a href="{{ route('admin.doctors.index') }}" class="flex items-center px-5 py-2 rounded-lg text-gray-600 hover:text-teal-700 hover:bg-teal-50 font-medium transition-colors {{ request()->routeIs('admin.doctors.*') ? 'bg-teal-100 text-teal-700 font-semibold shadow' : '' }}">
                            <svg class="w-4 h-4 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Doctors
                        </a>
                        <a href="{{ route('admin.medicine') }}" class="flex items-center px-5 py-2 rounded-lg text-gray-600 hover:text-teal-700 hover:bg-teal-50 font-medium transition-colors {{ request()->routeIs('medicine') ? 'bg-teal-100 text-teal-700 font-semibold shadow' : '' }}">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 018 0v2M9 7a4 4 0 100-8 4 4 0 000 8zm6 8v-2a6 6 0 00-12 0v2"></path></svg>
                            Medicine
                        </a>
                    @elseif(auth()->check() && auth()->user()->type === 'doctor')
                        <a href="{{ route('doctor.dashboard') }}" class="flex items-center px-5 py-2 rounded-lg text-gray-600 hover:text-teal-700 hover:bg-teal-50 font-medium transition-colors {{ request()->routeIs('doctor.dashboard') ? 'bg-teal-100 text-teal-700 font-semibold shadow' : '' }}">
                            <svg class="w-4 h-4 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Doctor Dashboard
                        </a>
                        <a href="{{ route('doctor.schedules.index') }}" class="flex items-center px-5 py-2 rounded-lg text-gray-600 hover:text-teal-700 hover:bg-teal-50 font-medium transition-colors {{ request()->routeIs('doctor.schedules.*') ? 'bg-teal-100 text-teal-700 font-semibold shadow' : '' }}">
                            <svg class="w-4 h-4 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Schedules
                        </a>
                    @elseif(auth()->check() && auth()->user()->type === 'patient')
                        <a href="{{ route('patient.appointments.index') }}" class="flex items-center px-5 py-2 rounded-lg text-gray-600 hover:text-teal-700 hover:bg-teal-50 font-medium transition-colors {{ request()->routeIs('patient.appointments.*') ? 'bg-teal-100 text-teal-700 font-semibold shadow' : '' }}">
                            <svg class="w-4 h-4 mr-2 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 018 0v2M9 7a4 4 0 100-8 4 4 0 000 8zm6 8v-2a6 6 0 00-12 0v2"></path></svg>
                            Appointments
                        </a>
                        <a href="{{ route('medicine') }}" class="flex items-center px-5 py-2 rounded-lg text-gray-600 hover:text-teal-700 hover:bg-teal-50 font-medium transition-colors {{ request()->routeIs('medicine') ? 'bg-teal-100 text-teal-700 font-semibold shadow' : '' }}">
                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 018 0v2M9 7a4 4 0 100-8 4 4 0 000 8zm6 8v-2a6 6 0 00-12 0v2"></path></svg>
                        Medicine
                        </a>
                    @endif
                    
                </div>
            </nav>
        </div>
    </header>
>>>>>>> 621b53d6aa9e6236f83b9054b591e051bb64dfa4

    <main class="container mx-auto px-6 py-8">
        {{ $slot }}
    </main>
    </body>
</html>
