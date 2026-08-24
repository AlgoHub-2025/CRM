<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'AlgoHub CRM') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased" x-data="{ sidebarOpen: false }">
        <div class="fixed inset-0 flex overflow-hidden bg-[#F4F4F5]">

            <!-- Sidebar Overlay (mobile) -->
            <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-200"
                 x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-200"
                 x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black/50 z-40 lg:hidden" @click="sidebarOpen = false"></div>

            <!-- Sidebar -->
            <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                   class="fixed inset-y-0 left-0 z-50 w-64 bg-[#2B333E] transform transition-transform duration-200 ease-in-out lg:translate-x-0 lg:static lg:inset-0 flex flex-col">

                <!-- Logo Area -->
                <div class="flex items-center h-16 px-5 bg-white border-b border-[#E2E4E8] border-r">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                        <img src="/images/logo.png" alt="AlgoHub" class="h-8 w-auto">
                        <div class="leading-tight">
                            <span class="text-[#2B333E] font-bold text-sm tracking-tight">ALGO</span><span class="text-[#2376D6] font-bold text-sm tracking-tight">HUB</span>
                            <div class="text-[10px] text-[#6B7280] tracking-widest uppercase mt-0.5">Code Revolution</div>
                        </div>
                    </a>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                    <a href="{{ route('dashboard') }}" wire:navigate
                       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition
                              {{ request()->routeIs('dashboard') ? 'bg-[#2376D6] text-white' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Dashboard
                    </a>

                    @canany(['leads.view.own', 'leads.view.all'])
                    <a href="{{ route('leads.index') }}" wire:navigate
                       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition
                              {{ request()->routeIs('leads.*') ? 'bg-[#2376D6] text-white' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        Leads
                    </a>
                    @endcanany

                    @canany(['opportunities.view.own', 'opportunities.view.all'])
                    <a href="{{ route('opportunities.index') }}" wire:navigate
                       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition
                              {{ request()->routeIs('opportunities.*') ? 'bg-[#2376D6] text-white' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        Opportunities
                    </a>
                    @endcanany

                    @canany(['clients.view.own', 'clients.view.all'])
                    <a href="{{ route('clients.index') }}" wire:navigate
                       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition
                              {{ request()->routeIs('clients.*') ? 'bg-[#2376D6] text-white' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Clients
                    </a>
                    @endcanany

                    @can('companies.view')
                    <a href="{{ route('companies.index') }}" wire:navigate
                       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition
                              {{ request()->routeIs('companies.*') ? 'bg-[#2376D6] text-white' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Companies
                    </a>
                    @endcan
                    
                    @canany(['proposals.view.own', 'proposals.view.all'])
                    <a href="{{ route('proposals.index') }}" wire:navigate
                       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition
                              {{ request()->routeIs('proposals.*') ? 'bg-[#2376D6] text-white' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Proposals
                    </a>
                    @endcanany

                    @canany(['contracts.view.own', 'contracts.view.all'])
                    <a href="{{ route('contracts.index') }}" wire:navigate
                       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition
                              {{ request()->routeIs('contracts.*') ? 'bg-[#2376D6] text-white' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                        Contracts
                    </a>
                    @endcanany

                    @canany(['projects.view.own', 'projects.view.all'])
                    <a href="{{ route('projects.index') }}" wire:navigate
                       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition
                              {{ request()->routeIs('projects.*') ? 'bg-[#2376D6] text-white' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        Projects
                    </a>
                    @endcanany

                    @canany(['invoices.view.own', 'invoices.view.all'])
                    <a href="{{ route('invoices.index') }}" wire:navigate
                       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition
                              {{ request()->routeIs('invoices.*') ? 'bg-[#2376D6] text-white' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Invoices
                    </a>
                    @endcanany

                    @canany(['tickets.view.own', 'tickets.view.all'])
                    <a href="{{ route('tickets.index') }}" wire:navigate
                       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition
                              {{ request()->routeIs('tickets.*') ? 'bg-[#2376D6] text-white' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        Support Tickets
                    </a>
                    @endcanany
                    
                    @hasanyrole('CEO|Managing Director')
                    <a href="{{ route('employees.index') }}" wire:navigate
                       class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition
                              {{ request()->routeIs('employees.*') ? 'bg-[#2376D6] text-white' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        HR & Staff
                    </a>
                    @endhasanyrole
                </nav>

                <!-- User Section (bottom of sidebar) -->
                <div class="px-3 py-4 border-t border-white/10">
                    <div class="flex items-center px-3 py-2">
                        <div class="w-8 h-8 rounded-full bg-[#2376D6] flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="ml-3 min-w-0" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-on:profile-updated.window="name = $event.detail.name">
                            <p class="text-sm font-medium text-white truncate" x-text="name"></p>
                            <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col min-w-0">

                <!-- Top Bar -->
                <header class="h-16 bg-white border-b border-[#E2E4E8] flex items-center justify-between px-4 lg:px-8 sticky top-0 z-30">
                    <div class="flex items-center">
                        <!-- Mobile sidebar toggle -->
                        <button @click="sidebarOpen = true" class="lg:hidden p-2 -ml-2 mr-2 text-gray-500 hover:text-gray-700 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>

                        <!-- Page Title -->
                        @if (isset($header))
                            <div class="text-[#2B333E]">{{ $header }}</div>
                        @endif
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- Profile Dropdown -->
                        <div x-data="{ profileOpen: false }" class="relative">
                            <button @click="profileOpen = !profileOpen" class="flex items-center text-sm text-gray-600 hover:text-gray-900 transition">
                                <span class="hidden sm:inline mr-2">{{ auth()->user()->name }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="profileOpen" @click.away="profileOpen = false"
                                 style="display: none;"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-[#E2E4E8] py-1 z-50">
                                <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-[#F4F4F5]">Profile</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-[#F4F4F5]">Log Out</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 bg-[#F4F4F5] p-4 lg:p-8 overflow-y-auto">
                    @if (session('success'))
                        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4 flex items-center">
                            <svg class="w-5 h-5 text-[#16A34A] mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-sm text-green-800">{{ session('success') }}</p>
                        </div>
                    @endif
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
