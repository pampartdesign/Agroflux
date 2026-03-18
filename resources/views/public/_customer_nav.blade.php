@php $cust = auth('customer')->user(); @endphp

@if($cust)
    {{-- Logged-in customer dropdown --}}
    <div x-data="{ open: false }" class="relative shrink-0">
        <button @click="open = !open" @click.away="open = false"
                class="inline-flex items-center gap-2 h-8 px-3 rounded-xl border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 transition text-sm text-emerald-800 font-medium">
            <span class="h-5 w-5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs font-bold flex-shrink-0">
                {{ $cust->initials() }}
            </span>
            <span class="hidden sm:inline text-xs">{{ $cust->name }}</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div x-show="open" x-cloak
             class="absolute right-0 mt-1 w-44 bg-white border border-slate-200 rounded-xl shadow-lg py-1 z-50 text-sm">
            <a href="{{ route('customer.dashboard') }}"
               class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-emerald-50 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                {{ __('market.account_dashboard_title') }}
            </a>
            <a href="{{ route('customer.orders') }}"
               class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-emerald-50 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                {{ __('market.my_orders') }}
            </a>
            <a href="{{ route('customer.profile') }}"
               class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-emerald-50 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                {{ __('market.my_profile') }}
            </a>
            <div class="border-t border-slate-100 mt-1 pt-1">
                <form method="POST" action="{{ route('customer.logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center gap-2 px-3 py-2 text-red-600 hover:bg-red-50 transition text-left">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        {{ __('market.logout') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@else
    {{-- Guest: login + register links --}}
    <div class="flex items-center gap-1">
        <a href="{{ route('customer.login') }}"
           class="inline-flex items-center h-8 px-3 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 transition text-xs text-slate-600 font-medium">
            {{ __('market.login') }}
        </a>
        <a href="{{ route('customer.register') }}"
           class="inline-flex items-center h-8 px-3 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 transition text-xs font-medium">
            {{ __('market.register') }}
        </a>
    </div>
@endif
