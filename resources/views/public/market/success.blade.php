<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('market.order_placed') }} — AgroFlux {{ __('market.marketplace') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900">

{{-- Public header --}}
<header class="bg-white border-b border-emerald-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="h-9 w-9 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-sm">A</div>
            <div>
                <div class="font-semibold leading-tight text-slate-900">AgroFlux</div>
                <div class="text-xs text-slate-500">{{ __('market.marketplace') }}</div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @include('public._locale_switcher')
            @include('public._customer_nav')
            <a href="{{ route('public.marketplace') }}"
               class="inline-flex items-center h-9 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 transition text-sm">
                {{ __('market.back_to_marketplace') }}
            </a>
        </div>
    </div>
</header>

<div class="max-w-2xl mx-auto px-6 py-12">

    {{-- Success icon --}}
    <div class="flex flex-col items-center text-center mb-8">
        <div class="h-16 w-16 rounded-full bg-emerald-100 flex items-center justify-center mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h1 class="text-2xl font-semibold text-slate-900">{{ __('market.order_placed') }}</h1>
        <p class="text-slate-500 mt-2 text-sm">
            {{ __('market.thank_you', ['name' => $order->customer_name]) }}
        </p>
    </div>

    {{-- Order summary card --}}
    <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm overflow-hidden mb-5">
        <div class="px-6 py-4 border-b border-emerald-100 flex items-center justify-between">
            <div>
                <div class="text-xs uppercase tracking-wide text-slate-400">{{ __('market.order_reference') }}</div>
                <div class="font-semibold text-slate-900 mt-0.5">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</div>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                {{ __('market.pending_payment') }}
            </span>
        </div>

        <div class="px-6 py-4 space-y-2 text-sm">
            <div class="flex justify-between text-slate-600">
                <span>{{ __('market.label_customer') }}</span>
                <span class="font-medium text-slate-900">{{ $order->customer_name }}</span>
            </div>
            <div class="flex justify-between text-slate-600">
                <span>{{ __('market.label_email') }}</span>
                <span class="font-medium text-slate-900">{{ $order->customer_email }}</span>
            </div>
            <div class="flex justify-between text-slate-600">
                <span>{{ __('market.label_date') }}</span>
                <span class="font-medium text-slate-900">{{ $order->created_at->format('d M Y, H:i') }}</span>
            </div>
        </div>

        @if($order->relationLoaded('items') && $order->items->count())
            <div class="px-6 pb-4">
                <div class="rounded-xl border border-slate-100 bg-slate-50 divide-y divide-slate-100 overflow-hidden">
                    @foreach($order->items as $item)
                        <div class="px-4 py-3 flex justify-between text-sm">
                            <span class="text-slate-700">
                                {{ $item->listing?->product?->default_name ?? 'Item' }}
                                <span class="text-slate-400">×{{ $item->qty }}</span>
                            </span>
                            <span class="font-medium text-slate-900">€{{ number_format((float)$item->price * (float)$item->qty, 2) }}</span>
                        </div>
                    @endforeach
                    <div class="px-4 py-3 flex justify-between text-sm font-semibold bg-white">
                        <span>{{ __('market.total') }}</span>
                        <span class="text-emerald-700">€{{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>
        @else
            <div class="px-6 pb-4 flex justify-between text-sm font-semibold">
                <span>{{ __('market.total') }}</span>
                <span class="text-emerald-700">€{{ number_format($order->total, 2) }}</span>
            </div>
        @endif
    </div>

    {{-- Seller / Farm details --}}
    @if(isset($seller) || $order->tenant)
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden mb-5">
        <div class="px-6 py-4 border-b border-slate-100">
            <div class="font-semibold text-sm text-slate-900">{{ __('market.seller_details') }}</div>
            <div class="text-xs text-slate-400 mt-0.5">{{ __('market.seller_details_desc') }}</div>
        </div>
        <div class="px-6 py-4 space-y-2 text-sm">
            @if($order->tenant?->name)
            <div class="flex justify-between">
                <span class="text-slate-500">{{ __('market.label_farm') }}</span>
                <span class="font-semibold text-slate-900">{{ $order->tenant->name }}</span>
            </div>
            @endif
            @if($order->tenant?->location_name)
            <div class="flex justify-between">
                <span class="text-slate-500">{{ __('market.label_location') }}</span>
                <span class="text-slate-700">{{ $order->tenant->location_name }}</span>
            </div>
            @endif
            @if(isset($seller))
                @if($seller->name || $seller->surname)
                <div class="flex justify-between">
                    <span class="text-slate-500">{{ __('market.label_name') }}</span>
                    <span class="font-medium text-slate-900">{{ trim(($seller->name ?? '').' '.($seller->surname ?? '')) }}</span>
                </div>
                @endif
                @if($seller->phone)
                <div class="flex justify-between">
                    <span class="text-slate-500">{{ __('market.label_phone') }}</span>
                    <span class="font-medium text-slate-900">
                        <a href="tel:{{ $seller->phone }}" class="text-emerald-700 hover:underline">{{ $seller->phone }}</a>
                    </span>
                </div>
                @endif
                @if($seller->email)
                <div class="flex justify-between">
                    <span class="text-slate-500">{{ __('market.label_email') }}</span>
                    <span class="font-medium text-slate-900">
                        <a href="mailto:{{ $seller->email }}" class="text-emerald-700 hover:underline">{{ $seller->email }}</a>
                    </span>
                </div>
                @endif
            @endif
        </div>
    </div>
    @endif

    {{-- Payment instructions --}}
    @if(isset($seller) && ($seller->bank_name || $seller->iban || $seller->iris_number))
    <div class="rounded-2xl overflow-hidden mb-5" style="border:1px solid #bfdbfe;">

        {{-- Bank Transfer --}}
        @if($seller->bank_name || $seller->iban)
        <div class="px-6 py-4" style="background:#eff6ff;border-bottom:1px solid #bfdbfe;">
            <div class="flex items-start gap-3">
                <div class="h-8 w-8 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5" style="background:#dbeafe;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" style="color:#2563eb;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <div class="font-semibold text-sm" style="color:#1d4ed8;">{{ __('market.bank_transfer_title') }}</div>
                    <div class="mt-3 space-y-1.5 text-sm">
                        @if($seller->bank_name)
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500 text-xs">{{ __('market.label_bank') }}</span>
                            <span class="font-medium text-slate-900">{{ $seller->bank_name }}</span>
                        </div>
                        @endif
                        @if($seller->iban)
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500 text-xs">{{ __('market.label_iban') }}</span>
                            <span class="font-mono font-semibold" style="color:#1e40af;font-size:0.85rem;letter-spacing:0.03em;">{{ $seller->iban }}</span>
                        </div>
                        @endif
                        @if($seller->name || $seller->surname)
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500 text-xs">{{ __('market.label_beneficiary') }}</span>
                            <span class="font-medium text-slate-900">{{ trim(($seller->name ?? '').' '.($seller->surname ?? '')) }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500 text-xs">{{ __('market.label_reference') }}</span>
                            <span class="font-medium text-slate-900">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- IRIS --}}
        @if($seller->iris_number)
        <div class="px-6 py-4" style="background:#f0fdf4;border-top:1px solid #bbf7d0;">
            <div class="flex items-start gap-3">
                <div class="h-8 w-8 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5" style="background:#dcfce7;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <div class="font-semibold text-sm text-emerald-800">{{ __('market.iris_title') }}</div>
                    <p class="text-xs text-emerald-700 mt-1 mb-2">{{ __('market.iris_desc') }}</p>
                    <div class="inline-flex items-center gap-3 rounded-xl px-4 py-2.5" style="background:#dcfce7;border:1px solid #86efac;">
                        <div>
                            <div class="text-xs font-semibold text-emerald-700">{{ __('market.iris_number') }}</div>
                            <div class="text-xl font-bold tracking-wider text-emerald-900">{{ $seller->iris_number }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>
    @else
    {{-- No payment info — generic notice --}}
    <div class="rounded-2xl border border-amber-100 bg-amber-50 p-5 mb-5">
        <div class="flex items-start gap-3">
            <div class="h-8 w-8 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="5" width="20" height="14" rx="2"/>
                    <path d="M2 10h20"/>
                </svg>
            </div>
            <div>
                <div class="font-semibold text-amber-900 text-sm">{{ __('market.wire_required_title') }}</div>
                <p class="text-amber-800 text-xs mt-1">
                    {{ __('market.wire_required_desc', ['seller' => $order->tenant?->name ?? __('market.sold_by')]) }}
                </p>
            </div>
        </div>
    </div>
    @endif

    {{-- Guest: create account prompt --}}
    @if(!auth('customer')->check() && !isset($newCustomer))
    <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-5 mb-5">
        <div class="font-semibold text-emerald-900 text-sm mb-1">{{ __('market.create_account_after_order') }}</div>
        <p class="text-xs text-emerald-700 mb-3">{{ __('market.create_account_after_order_desc') }}</p>
        <a href="{{ route('customer.register', ['email' => $order->customer_email]) }}"
           class="inline-flex items-center h-9 px-4 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition">
            {{ __('market.create_account_btn') }}
        </a>
    </div>
    @endif

    {{-- Logged in after order --}}
    @if(isset($newCustomer) || auth('customer')->check())
    <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4 mb-5 flex items-center gap-3">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="text-sm text-emerald-800">
            {{ __('market.account_linked_to_order') }}
            <a href="{{ route('customer.orders') }}" class="font-semibold underline">{{ __('market.my_orders') }}</a>
        </div>
    </div>
    @endif

    {{-- Actions --}}
    <div class="flex items-center gap-3 justify-center">
        <a href="{{ route('public.marketplace') }}"
           class="inline-flex items-center h-10 px-6 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 transition text-sm font-medium">
            {{ __('market.continue_shopping') }}
        </a>
        <a href="{{ route('order.print', $order) }}"
           target="_blank"
           class="inline-flex items-center h-10 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 transition text-sm">
            {{ __('market.print_order') }}
        </a>
    </div>
</div>

<footer class="mt-16 border-t border-slate-200 py-6 text-center text-xs text-slate-400">
    {{ __('market.footer_tagline_short') }}
</footer>

</body>
</html>
