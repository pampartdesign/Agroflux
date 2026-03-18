<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('market.register') }} — AgroFlux {{ __('market.marketplace') }}</title>
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
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-semibold text-slate-900">{{ __('market.create_account') }}</h1>
            <p class="text-sm text-slate-500 mt-1">{{ __('market.register_subtitle') }}</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6">
            <form method="POST" action="{{ route('customer.register') }}" class="space-y-4">
                @csrf

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('market.first_name') }}</label>
                        <input name="name" type="text" required
                               value="{{ old('name') }}"
                               class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 @error('name') border-red-300 @enderror">
                        @error('name') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('market.surname') }}</label>
                        <input name="surname" type="text" required
                               value="{{ old('surname') }}"
                               class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 @error('surname') border-red-300 @enderror">
                        @error('surname') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('market.email_address') }}</label>
                    <input name="email" type="email" required
                           value="{{ old('email', request('email')) }}"
                           class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 @error('email') border-red-300 @enderror"
                           placeholder="you@example.com">
                    @error('email') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('market.phone') }}</label>
                    <input name="phone" type="tel"
                           value="{{ old('phone') }}"
                           class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200"
                           placeholder="+30 210 000 0000">
                </div>

                <div class="border-t border-slate-100 pt-4">
                    <div class="text-xs font-medium text-slate-400 mb-3 uppercase tracking-wide">{{ __('market.delivery_address') }}</div>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('market.address') }}</label>
                            <input name="address" type="text"
                                   value="{{ old('address') }}"
                                   class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('market.city') }}</label>
                                <input name="city" type="text"
                                       value="{{ old('city') }}"
                                       class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('market.zip_code') }}</label>
                                <input name="zip_code" type="text"
                                       value="{{ old('zip_code') }}"
                                       class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('market.country_label') }}</label>
                            @include('public._country_select', ['name' => 'country', 'selected' => old('country', 'GR')])
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-4">
                    <div class="text-xs font-medium text-slate-400 mb-3 uppercase tracking-wide">{{ __('market.security') }}</div>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('market.password') }}</label>
                            <input name="password" type="password" required
                                   class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 @error('password') border-red-300 @enderror"
                                   placeholder="••••••••">
                            @error('password') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('market.confirm_password') }}</label>
                            <input name="password_confirmation" type="password" required
                                   class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200"
                                   placeholder="••••••••">
                        </div>
                    </div>
                </div>

                <button type="submit"
                        class="w-full h-10 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition">
                    {{ __('market.create_account_btn') }}
                </button>
            </form>
        </div>

        <p class="text-center text-sm text-slate-500 mt-5">
            {{ __('market.have_account') }}
            <a href="{{ route('customer.login') }}" class="text-emerald-700 font-medium hover:underline">
                {{ __('market.login') }}
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
