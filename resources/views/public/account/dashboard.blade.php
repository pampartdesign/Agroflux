<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('market.account_dashboard_title') }} — AgroFlux</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900">

<header class="sticky top-0 z-30 bg-white border-b border-emerald-100 shadow-sm">
    <div class="max-w-4xl mx-auto px-6 py-4 flex items-center justify-between gap-4">
        <a href="{{ route('public.marketplace') }}" class="flex items-center gap-3">
            <div class="h-9 w-9 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-sm">A</div>
            <div>
                <div class="font-semibold leading-tight text-slate-900">AgroFlux</div>
                <div class="text-xs text-slate-500">{{ __('market.marketplace') }}</div>
            </div>
        </a>
        <div class="flex items-center gap-2">
            @include('public._locale_switcher')
            @include('public._customer_nav')
        </div>
    </div>
</header>

<div class="max-w-4xl mx-auto px-6 py-8">

    {{-- Welcome banner --}}
    <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm p-6 mb-6 flex items-center gap-4">
        <div class="h-14 w-14 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xl flex-shrink-0">
            {{ $customer->initials() }}
        </div>
        <div>
            <div class="font-semibold text-slate-900 text-lg">{{ $customer->fullName() }}</div>
            <div class="text-sm text-slate-500">{{ $customer->email }}</div>
        </div>
    </div>

    {{-- Quick nav --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <a href="{{ route('customer.orders') }}"
           class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5 hover:border-emerald-200 hover:bg-emerald-50 transition group">
            <div class="h-10 w-10 rounded-xl bg-emerald-100 flex items-center justify-center mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div class="font-semibold text-slate-900">{{ __('market.my_orders') }}</div>
            <div class="text-xs text-slate-500 mt-0.5">{{ __('market.my_orders_desc') }}</div>
        </a>
        <a href="{{ route('customer.profile') }}"
           class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5 hover:border-emerald-200 hover:bg-emerald-50 transition">
            <div class="h-10 w-10 rounded-xl bg-slate-100 flex items-center justify-center mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div class="font-semibold text-slate-900">{{ __('market.my_profile') }}</div>
            <div class="text-xs text-slate-500 mt-0.5">{{ __('market.my_profile_desc') }}</div>
        </a>
        <a href="{{ route('public.marketplace') }}"
           class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5 hover:border-emerald-200 hover:bg-emerald-50 transition">
            <div class="h-10 w-10 rounded-xl bg-slate-100 flex items-center justify-center mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.2 6H19M9 21a1 1 0 100-2 1 1 0 000 2zm10 0a1 1 0 100-2 1 1 0 000 2z"/>
                </svg>
            </div>
            <div class="font-semibold text-slate-900">{{ __('market.marketplace') }}</div>
            <div class="text-xs text-slate-500 mt-0.5">{{ __('market.browse_marketplace') }}</div>
        </a>
    </div>

    {{-- Recent orders --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="font-semibold text-slate-900">{{ __('market.recent_orders') }}</div>
            <a href="{{ route('customer.orders') }}" class="text-xs text-emerald-700 hover:underline">{{ __('market.view_all') }}</a>
        </div>

        @if($recentOrders->isEmpty())
            <div class="px-6 py-10 text-center text-sm text-slate-400">
                {{ __('market.order_history_empty') }}
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach($recentOrders as $order)
                    <a href="{{ route('customer.order', $order) }}"
                       class="flex items-center justify-between px-6 py-4 hover:bg-slate-50 transition">
                        <div>
                            <div class="font-medium text-slate-900 text-sm">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</div>
                            <div class="text-xs text-slate-400 mt-0.5">{{ $order->created_at->format('d M Y') }}</div>
                        </div>
                        <div class="text-right">
                            <div class="font-semibold text-slate-900 text-sm">€{{ number_format($order->total, 2) }}</div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $order->status === 'pending_wire' ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700' }}">
                                {{ $order->status }}
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>

<footer class="mt-16 border-t border-slate-200 py-6 text-center text-xs text-slate-400">
    {{ __('market.footer_tagline_short') }}
</footer>

</body>
</html>
