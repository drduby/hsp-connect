<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="h-full bg-white">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin – @yield('title', 'HSPConnect')</title>
    @vite(['resources/css/admin.css'])
    @livewireStyles

</head>
<body class="h-full" x-data="{ sidebarOpen: false, profileOpen: false }">

    {{-- Mobile sidebar --}}
    <div x-show="sidebarOpen" class="relative z-50 lg:hidden" x-cloak>
        <div x-show="sidebarOpen"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-900/80"
             @click="sidebarOpen = false">
        </div>
        <div class="fixed inset-0 flex">
            <div x-show="sidebarOpen"
                 x-transition:enter="transition ease-in-out duration-300 transform"
                 x-transition:enter-start="-translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in-out duration-300 transform"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="-translate-x-full"
                 class="relative mr-16 flex w-full max-w-xs flex-1">
                <div class="absolute left-full top-0 flex w-16 justify-center pt-5">
                    <button type="button" @click="sidebarOpen = false" class="-m-2.5 p-2.5">
                        <span class="sr-only">Close sidebar</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6 text-white">
                            <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>
                <div class="relative flex grow flex-col gap-y-5 overflow-y-auto bg-white px-6 pb-4">
                    @include('layouts.admin-nav')
                </div>
            </div>
        </div>
    </div>

    {{-- Static sidebar for desktop --}}
    <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
        <div class="flex grow flex-col gap-y-5 overflow-y-auto border-r border-gray-200 bg-white px-6 pb-4">
            @include('layouts.admin-nav')
        </div>
    </div>

    <div class="lg:pl-72">
        {{-- Top navbar --}}
        <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
            <button type="button" @click="sidebarOpen = true" class="-m-2.5 p-2.5 text-gray-700 lg:hidden">
                <span class="sr-only">Open sidebar</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6">
                    <path d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>

            <div aria-hidden="true" class="h-6 w-px bg-gray-200 lg:hidden"></div>

            <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                <div class="flex flex-1 items-center">
                    <span class="text-sm font-semibold text-gray-700 lg:hidden">@yield('title', 'Dashboard')</span>
                </div>
                <div class="flex items-center gap-x-4 lg:gap-x-6">
                    <a href="{{ route('home') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back to site</a>

                    <div aria-hidden="true" class="hidden lg:block lg:h-6 lg:w-px lg:bg-gray-200"></div>

                    {{-- Profile dropdown --}}
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open" class="flex items-center gap-x-3 text-sm">
                            <span class="flex size-8 items-center justify-center rounded-full bg-indigo-600 text-xs font-bold text-white">
                                {{ strtoupper(substr(auth()->user()->nickname ?? 'A', 0, 1)) }}
                            </span>
                            <span class="hidden lg:block font-semibold text-gray-900">{{ auth()->user()->nickname }}</span>
                            <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="hidden lg:block size-5 text-gray-400">
                                <path d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" fill-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 z-10 mt-2 w-44 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-gray-900/5"
                             x-cloak>
                            <a href="{{ route('admin.password.index') }}" class="block px-3 py-1 text-sm/6 text-gray-900 hover:bg-gray-50">Change password</a>
                            <div class="my-1 border-t border-gray-100"></div>
                            <form method="POST" action="{{ route('logout') ?? '#' }}">
                                @csrf
                                <button type="submit" class="block w-full px-3 py-1 text-left text-sm/6 text-red-600 hover:bg-gray-50">Sign out</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <main class="py-10">
            <div class="px-4 sm:px-6 lg:px-8">
                @yield('content')
            </div>
        </main>
    </div>

    @livewireScripts
</body>
</html>
