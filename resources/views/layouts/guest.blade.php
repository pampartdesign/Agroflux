<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'AgroFlux') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-slate-900 antialiased bg-slate-50 min-h-screen flex">

    {{-- Left branding panel --}}
    <div class="hidden lg:flex lg:w-1/2 flex-col justify-between p-12 relative overflow-hidden"
         style="background-color:#047857">
        {{-- Background pattern --}}
        <div class="absolute inset-0 opacity-10">
            <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                        <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="1"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#grid)" />
            </svg>
        </div>

        {{-- Logo --}}
        <div class="relative z-10">
            <img src="/agroflux_logo.webp" alt="AgroFlux" class="h-12 w-auto">
        </div>

        {{-- Tagline --}}
        <div class="relative z-10">
            <h2 class="text-white text-3xl font-semibold leading-snug mb-4">
                From Field to<br>Marketplace, Simplified.
            </h2>
            <p class="text-emerald-200 text-sm leading-relaxed max-w-xs">
                Manage your farm, sell fresh produce, track traceability, and grow your business — all in one place.
            </p>

            <div class="mt-8 space-y-3">
                @foreach([
                    ['🌿', 'Farm & crop management'],
                    ['🛒', 'Direct-to-consumer marketplace'],
                    ['📦', 'Full order & traceability tracking'],
                    ['📡', 'IoT sensor monitoring'],
                ] as [$icon, $label])
                <div class="flex items-center gap-3 text-emerald-100 text-sm">
                    <span class="text-base">{{ $icon }}</span>
                    <span>{{ $label }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Footer --}}
        <div class="relative z-10 text-emerald-300 text-xs">
            © {{ date('Y') }} AgroFlux. All rights reserved.
        </div>
    </div>

    {{-- Right form panel --}}
    <div class="flex-1 flex flex-col justify-center items-center px-6 py-12">
        {{-- Mobile logo --}}
        <div class="lg:hidden mb-8">
            <img src="/agroflux_logo.webp" alt="AgroFlux" class="h-10 w-auto mx-auto">
        </div>

        <div class="w-full max-w-sm">
            {{-- Login / Register tabs --}}
            <div class="flex rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden mb-6">
                <a href="{{ route('login') }}"
                   class="flex-1 py-3 text-center text-sm font-medium transition
                       {{ request()->routeIs('login') ? 'bg-emerald-600 text-white' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50' }}">
                    Sign In
                </a>
                <a href="{{ route('register') }}"
                   class="flex-1 py-3 text-center text-sm font-medium transition border-l border-slate-200
                       {{ request()->routeIs('register') ? 'bg-emerald-600 text-white' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50' }}">
                    Create Account
                </a>
            </div>

            {{-- Form card --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6">
                {{ $slot }}
            </div>

            <p class="text-center text-xs text-slate-400 mt-6">
                Are you a customer?
                <a href="{{ route('public.marketplace') }}" class="text-emerald-700 hover:underline font-medium">
                    Browse the Marketplace →
                </a>
            </p>
        </div>
    </div>

</body>
</html>
