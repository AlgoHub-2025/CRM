<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false }" class="bg-brand-charcoal border-b border-brand-charcoal">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center space-x-2">
                        <img src="/images/logo.svg" alt="AlgoHub" class="h-8 w-auto">
                        <span class="text-white font-bold text-lg tracking-tight">AlgoHub <span class="text-brand-blue font-normal">CRM</span></span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-1 sm:-my-px sm:ms-8 sm:flex">
                    <a href="{{ route('dashboard') }}" wire:navigate
                       class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md transition
                              {{ request()->routeIs('dashboard') ? 'text-white bg-brand-charcoal' : 'text-brand-charcoal hover:text-white hover:bg-brand-charcoal' }}">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Dashboard
                    </a>

                    @can('leads.view.own')
                    <a href="{{ route('leads.index') }}" wire:navigate
                       class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md transition
                              {{ request()->routeIs('leads.*') ? 'text-white bg-brand-charcoal' : 'text-brand-charcoal hover:text-white hover:bg-brand-charcoal' }}">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Leads
                    </a>
                    @endcan

                    @can('opportunities.view.own')
                    <a href="{{ route('opportunities.index') }}" wire:navigate
                       class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md transition
                              {{ request()->routeIs('opportunities.*') ? 'text-white bg-brand-charcoal' : 'text-brand-charcoal hover:text-white hover:bg-brand-charcoal' }}">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        Pipeline
                    </a>
                    @endcan

                    @can('companies.view')
                    <a href="{{ route('companies.index') }}" wire:navigate
                       class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md transition
                              {{ request()->routeIs('companies.*') ? 'text-white bg-brand-charcoal' : 'text-brand-charcoal hover:text-white hover:bg-brand-charcoal' }}">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Companies
                    </a>
                    @endcan

                    @can('tickets.view.own')
                    <a href="{{ route('tickets.index') }}" wire:navigate
                       class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md transition
                              {{ request()->routeIs('tickets.*') ? 'text-white bg-brand-charcoal' : 'text-brand-charcoal hover:text-white hover:bg-brand-charcoal' }}">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        Tickets
                    </a>
                    @endcan
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md text-brand-charcoal hover:text-white hover:bg-brand-charcoal transition">
                            <div class="w-8 h-8 rounded-full bg-brand-blue flex items-center justify-center text-white text-sm font-bold mr-2">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <div x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                            <svg class="ms-1 fill-current h-4 w-4 text-brand-charcoal" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile')" wire:navigate>
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <button wire:click="logout" class="w-full text-start">
                            <x-dropdown-link>
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-brand-charcoal hover:text-white hover:bg-brand-charcoal transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-brand-charcoal">
        <div class="pt-2 pb-3 space-y-1 px-2">
            <a href="{{ route('dashboard') }}" wire:navigate class="block px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('dashboard') ? 'text-white bg-brand-charcoal' : 'text-brand-charcoal hover:text-white hover:bg-brand-charcoal' }}">Dashboard</a>
            <a href="{{ route('leads.index') }}" wire:navigate class="block px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('leads.*') ? 'text-white bg-brand-charcoal' : 'text-brand-charcoal hover:text-white hover:bg-brand-charcoal' }}">Leads</a>
            <a href="{{ route('opportunities.index') }}" wire:navigate class="block px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('opportunities.*') ? 'text-white bg-brand-charcoal' : 'text-brand-charcoal hover:text-white hover:bg-brand-charcoal' }}">Pipeline</a>
            <a href="{{ route('companies.index') }}" wire:navigate class="block px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('companies.*') ? 'text-white bg-brand-charcoal' : 'text-brand-charcoal hover:text-white hover:bg-brand-charcoal' }}">Companies</a>
            <a href="{{ route('tickets.index') }}" wire:navigate class="block px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('tickets.*') ? 'text-white bg-brand-charcoal' : 'text-brand-charcoal hover:text-white hover:bg-brand-charcoal' }}">Tickets</a>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-brand-charcoal">
            <div class="px-4">
                <div class="font-medium text-base text-white" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                <div class="font-medium text-sm text-brand-charcoal">{{ auth()->user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1 px-2">
                <a href="{{ route('profile') }}" wire:navigate class="block px-3 py-2 text-sm font-medium rounded-md text-brand-charcoal hover:text-white hover:bg-brand-charcoal">Profile</a>
                <button wire:click="logout" class="w-full text-start block px-3 py-2 text-sm font-medium rounded-md text-brand-charcoal hover:text-white hover:bg-brand-charcoal">Log Out</button>
            </div>
        </div>
    </div>
</nav>

