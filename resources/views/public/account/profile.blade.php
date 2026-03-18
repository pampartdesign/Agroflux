<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('market.profile_title') }} — AgroFlux</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900">

<header class="sticky top-0 z-30 bg-white border-b border-emerald-100 shadow-sm">
    <div class="max-w-4xl mx-auto px-6 py-4 flex items-center justify-between gap-4">
        <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-3">
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

<div class="max-w-2xl mx-auto px-6 py-8">

    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('customer.dashboard') }}" class="text-slate-400 hover:text-slate-600 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-2xl font-semibold text-slate-900">{{ __('market.profile_title') }}</h1>
    </div>

    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800 mb-5">
            {{ session('success') }}
        </div>
    @endif

    {{-- Profile form --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6 mb-5">
        <div class="font-semibold text-slate-900 mb-4">{{ __('market.personal_details') }}</div>
        <form method="POST" action="{{ route('customer.profile.update') }}" class="space-y-4">
            @csrf @method('PUT')

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('market.first_name') }}</label>
                    <input name="name" type="text" required
                           value="{{ old('name', $customer->name) }}"
                           class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 @error('name') border-red-300 @enderror">
                    @error('name') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('market.surname') }}</label>
                    <input name="surname" type="text" required
                           value="{{ old('surname', $customer->surname) }}"
                           class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 @error('surname') border-red-300 @enderror">
                    @error('surname') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('market.email_address') }}</label>
                <input name="email" type="email" required
                       value="{{ old('email', $customer->email) }}"
                       class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 @error('email') border-red-300 @enderror">
                @error('email') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('market.phone') }}</label>
                <input name="phone" type="tel"
                       value="{{ old('phone', $customer->phone) }}"
                       class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
            </div>

            <div class="border-t border-slate-100 pt-4">
                <div class="text-xs font-medium text-slate-400 mb-3 uppercase tracking-wide">{{ __('market.delivery_address') }}</div>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('market.address') }}</label>
                        <input name="address" type="text"
                               value="{{ old('address', $customer->address) }}"
                               class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('market.city') }}</label>
                            <input name="city" type="text"
                                   value="{{ old('city', $customer->city) }}"
                                   class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('market.zip_code') }}</label>
                            <input name="zip_code" type="text"
                                   value="{{ old('zip_code', $customer->zip_code) }}"
                                   class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('market.country_label') }}</label>
                        @include('public._country_select', ['name' => 'country', 'selected' => old('country', $customer->country ?? 'GR')])
                    </div>
                </div>
            </div>

            <button type="submit"
                    class="w-full h-10 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition">
                {{ __('market.save_profile') }}
            </button>
        </form>
    </div>

    {{-- Password change --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6">
        <div class="font-semibold text-slate-900 mb-4">{{ __('market.change_password') }}</div>
        <form method="POST" action="{{ route('customer.password.update') }}" class="space-y-4">
            @csrf @method('PUT')

            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('market.current_password') }}</label>
                <input name="current_password" type="password"
                       class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 @error('current_password') border-red-300 @enderror">
                @error('current_password') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('market.new_password') }}</label>
                <input name="password" type="password"
                       class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 @error('password') border-red-300 @enderror">
                @error('password') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('market.confirm_password') }}</label>
                <input name="password_confirmation" type="password"
                       class="w-full h-10 rounded-xl border border-slate-200 px-3 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
            </div>

            <button type="submit"
                    class="h-10 px-6 rounded-xl border border-slate-200 bg-white text-sm hover:bg-slate-50 transition">
                {{ __('market.update_password') }}
            </button>
        </form>
    </div>
</div>

<footer class="mt-16 border-t border-slate-200 py-6 text-center text-xs text-slate-400">
    {{ __('market.footer_tagline_short') }}
</footer>

</body>
</html>
