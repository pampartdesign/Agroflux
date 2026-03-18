<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('market.login') }} — AgroFlux {{ __('market.marketplace') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col">

<header class="bg-white border-b border-emerald-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between gap-4">
        <a href="{{ route('public.marketplace') }}" class="flex items-center gap-3">
            <div class="h-9 w-9 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-sm">A</div>
            <div>
                <div class="font-semibold leading-tight text-slate-900">AgroFlux</div>
                <div class="text-xs text-slate-500">{{ __('market.marketplace') }}</div>
            </div>
        </a>
        @include('public._locale_switcher')
    </div>
</header>

<div class="flex-1 flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-sm">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-semibold text-slate-900">{{ __('market.login') }}</h1>
            <p class="text-sm text-slate-500 mt-1">{{ __('market.login_subtitle') }}</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6">
            <form method="POST" action="{{ route('customer.login') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('market.email_address') }}</label>
                    <input name="email" type="email" required
                           value="{{ old('email') }}"
                           class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 @error('email') border-red-300 @enderror"
                           placeholder="you@example.com" autofocus>
                    @error('email')
                        <div class="text-xs text-red-600 mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('market.password') }}</label>
                    <input name="password" type="password" required
                           class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200"
                           placeholder="••••••••">
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-200">
                        {{ __('market.remember_me') }}
                    </label>
                </div>

                <button type="submit"
                        class="w-full h-10 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition">
                    {{ __('market.login_btn') }}
                </button>
            </form>
        </div>

        <p class="text-center text-sm text-slate-500 mt-5">
            {{ __('market.no_account') }}
            <a href="{{ route('customer.register') }}" class="text-emerald-700 font-medium hover:underline">
                {{ __('market.register') }}
            </a>
        </p>

        <p class="text-center mt-3">
            <a href="{{ route('public.marketplace') }}" class="text-xs text-slate-400 hover:text-slate-600 transition">
                ← {{ __('market.back_to_marketplace') }}
            </a>
        </p>
    </div>
</div>

</body>
</html>
