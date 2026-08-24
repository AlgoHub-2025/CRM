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
        
        <style>
            .tech-grid {
                background-size: 40px 40px;
                background-image: linear-gradient(to right, rgba(35, 118, 214, 0.05) 1px, transparent 1px),
                                  linear-gradient(to bottom, rgba(35, 118, 214, 0.05) 1px, transparent 1px);
            }
            .tech-glow {
                box-shadow: 0 0 40px -10px rgba(35, 118, 214, 0.2);
            }
        </style>
    </head>
    <body class="font-sans text-[#2B333E] antialiased bg-[#F4F4F5] tech-grid relative min-h-screen flex flex-col justify-center items-center">
        <!-- Subtle background glow -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-[#2376D6]/5 rounded-full blur-3xl pointer-events-none"></div>
        
        <div class="relative z-10 w-full sm:max-w-md">
            <div class="flex justify-center mb-8">
                <a href="/" wire:navigate class="inline-block transform hover:scale-105 transition duration-300">
                    <x-application-logo class="h-16 w-auto drop-shadow-sm" />
                </a>
            </div>

            <div class="w-full px-8 py-8 bg-white border border-[#E2E4E8] tech-glow rounded-xl">
                {{ $slot }}
            </div>
            
            <div class="mt-8 text-center text-xs text-[#6B7280] uppercase tracking-widest font-medium">
                Secure Access Portal &bull; System Active
            </div>
        </div>
    </body>
</html>
