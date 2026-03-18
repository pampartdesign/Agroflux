@extends('layouts.app')

@section('content')

{{-- Page header --}}
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-slate-900">Account Settings</h1>
    <p class="text-sm text-slate-500 mt-1">Manage your profile information, language, password and account.</p>
</div>

@php
    $planName    = $subscription?->plan?->name ?? null;
    $planKey     = $subscription?->plan?->key ?? $tenant?->plan_key ?? 'core';
    $planPrice   = $subscription?->plan?->price ?? null;
    $planCycle   = $subscription?->plan?->billing_cycle ?? null;
    $planModules = $subscription?->plan?->modules ?? [];

    // For plans whose access is defined in FeatureGate (not solely by DB modules),
    // override $planModules so the "Included Features" section shows the real access.
    if ($planKey === 'agroflux_drone' && count($planModules) < 5) {
        $planModules = [
            'core','farm','livestock','water','traceability',
            'inventory','equipment','iot_sim','iot','logi','drone',
        ];
    } elseif (in_array($planKey, ['plus']) && count($planModules) < 5) {
        $planModules = [
            'core','farm','livestock','water','traceability',
            'inventory','equipment','iot_sim','iot',
        ];
    } elseif ($planKey === 'core' && count($planModules) === 0) {
        $planModules = [
            'core','farm','livestock','water','traceability',
            'inventory','equipment','iot_sim',
        ];
    }

    $endsAt      = $subscription?->ends_at ?? null;
    $trialEndsAt = $tenant?->trial_ends_at ?? null;
    $isTrial     = !$subscription && $trialEndsAt && $trialEndsAt->isFuture();

    $moduleLabels = [
        'core'         => 'Core',
        'farm'         => 'Farm',
        'livestock'    => 'Livestock',
        'water'        => 'Water',
        'traceability' => 'Traceability',
        'inventory'    => 'Inventory',
        'equipment'    => 'Equipment',
        'iot_sim'      => 'IoT Simulator',
        'iot'          => 'IoT Sensors',
        'logi'         => 'Logistics',
        'drone'        => 'Drones & Field Mapping',
    ];

    if ($endsAt) {
        $daysLeft    = (int) now()->diffInDays($endsAt, false);
        $expiryColor = $daysLeft < 0 ? '#dc2626' : ($daysLeft <= 30 ? '#d97706' : '#059669');
    }
@endphp

{{-- 2-column grid: forms on left, My Plan on right --}}
<div style="display:grid; grid-template-columns:minmax(0,1fr) minmax(0,360px); gap:1.5rem; align-items:start;" class="block lg:grid">

    {{-- ══ LEFT COLUMN — forms ══ --}}
    <div class="space-y-5">

        {{-- ── Profile information ── --}}
        <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-emerald-100 flex items-center gap-3">
                <div class="h-8 w-8 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div class="font-semibold text-sm text-slate-900">Profile Information</div>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" class="px-6 py-5 space-y-4">
                @csrf
                @method('PATCH')

                {{-- First name + Last name --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">
                            First name <span class="text-red-500">*</span>
                        </label>
                        <input name="name"
                               value="{{ old('name', $user->name) }}"
                               type="text"
                               required
                               class="w-full h-10 px-3 rounded-xl border @error('name') border-red-400 @else border-slate-200 @enderror bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200"
                               placeholder="e.g. Georgios">
                        @error('name')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">Last name</label>
                        <input name="surname"
                               value="{{ old('surname', $user->surname) }}"
                               type="text"
                               class="w-full h-10 px-3 rounded-xl border @error('surname') border-red-400 @else border-slate-200 @enderror bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200"
                               placeholder="e.g. Papadopoulos">
                        @error('surname')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Company / Legal name --}}
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Company / Legal name</label>
                    <input name="company_name"
                           value="{{ old('company_name', $user->company_name) }}"
                           type="text"
                           class="w-full h-10 px-3 rounded-xl border @error('company_name') border-red-400 @else border-slate-200 @enderror bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200"
                           placeholder="e.g. Papadopoulos Agri S.A.">
                    @error('company_name')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Phone + Email --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">Phone</label>
                        <input name="phone"
                               value="{{ old('phone', $user->phone) }}"
                               type="tel"
                               class="w-full h-10 px-3 rounded-xl border @error('phone') border-red-400 @else border-slate-200 @enderror bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200"
                               placeholder="+30 210 000 0000">
                        @error('phone')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">
                            Email address <span class="text-red-500">*</span>
                        </label>
                        <input name="email"
                               value="{{ old('email', $user->email) }}"
                               type="email"
                               required
                               class="w-full h-10 px-3 rounded-xl border @error('email') border-red-400 @else border-slate-200 @enderror bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200"
                               placeholder="you@example.com">
                        @error('email')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Address --}}
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ __('profile.address') }}</label>
                    <input name="address"
                           value="{{ old('address', $user->address) }}"
                           type="text"
                           class="w-full h-10 px-3 rounded-xl border @error('address') border-red-400 @else border-slate-200 @enderror bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200"
                           placeholder="{{ __('profile.address_placeholder') }}">
                    @error('address')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Zip code + Country --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">
                            {{ __('profile.zip_code') }} <span class="text-red-500">*</span>
                        </label>
                        <input name="zip_code"
                               value="{{ old('zip_code', $user->zip_code) }}"
                               type="text"
                               required
                               class="w-full h-10 px-3 rounded-xl border @error('zip_code') border-red-400 @else border-slate-200 @enderror bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200"
                               placeholder="{{ __('profile.zip_code_placeholder') }}">
                        @error('zip_code')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">
                            {{ __('profile.country') }} <span class="text-red-500">*</span>
                            <span class="font-normal text-slate-400 normal-case">— {{ __('profile.country_hint') }}</span>
                        </label>
                        @php
                        $euCountries = [
                            'Greece','Germany','France','Italy','Spain','Portugal','Netherlands',
                            'Belgium','Austria','Switzerland','Poland','Czech Republic','Slovakia',
                            'Hungary','Romania','Bulgaria','Croatia','Slovenia','Serbia','Albania',
                            'North Macedonia','Cyprus','Malta','Luxembourg','Ireland','Denmark',
                            'Sweden','Finland','Norway','United Kingdom','Turkey',
                        ];
                        @endphp
                        <select name="country"
                                required
                                class="w-full h-10 px-3 rounded-xl border @error('country') border-red-400 @else border-slate-200 @enderror bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                            <option value="">— {{ __('profile.select_country') }} —</option>
                            @foreach($euCountries as $c)
                                <option value="{{ $c }}" {{ old('country', $user->country) === $c ? 'selected' : '' }}>
                                    {{ $c }}
                                </option>
                            @endforeach
                        </select>
                        @error('country')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
                    <button type="submit"
                            class="inline-flex items-center gap-2 h-10 px-5 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Profile
                    </button>
                    @if (session('status') === 'profile-updated')
                        <span class="text-sm text-emerald-700 font-medium">✓ Saved</span>
                    @endif
                </div>
            </form>
        </div>

        {{-- ── Payment Information ── --}}
        <div class="rounded-2xl border border-blue-100 bg-white shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-blue-100 flex items-center gap-3">
                <div class="h-8 w-8 rounded-xl flex items-center justify-center flex-shrink-0" style="background:#eff6ff;border:1px solid #bfdbfe;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" style="color:#2563eb;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <div>
                    <div class="font-semibold text-sm text-slate-900">Payment Information</div>
                    <div class="text-xs text-slate-400 mt-0.5">Required for marketplace sellers — shown to buyers on order confirmation.</div>
                </div>
            </div>

            @php
                $hasPaymentInfo = !empty($user->bank_name) && !empty($user->iban);
            @endphp

            @if(!$hasPaymentInfo)
            <div class="mx-6 mt-4 flex items-start gap-3 rounded-xl px-4 py-3"
                 style="background:#fefce8;border:1px solid #fde047;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0 mt-0.5" style="color:#ca8a04;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
                <p class="text-xs" style="color:#854d0e;">
                    <strong>Payment info incomplete.</strong> Buyers will not see bank details on their order confirmation until you fill in your Bank Name and IBAN.
                </p>
            </div>
            @endif

            <form method="POST" action="{{ route('profile.payment.update') }}" class="px-6 py-5 space-y-4">
                @csrf
                @method('PATCH')

                {{-- Section 1: Bank Details --}}
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide mb-3" style="color:#2563eb;">
                        🏦 Bank Transfer (SEPA / Wire)
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1.5">Bank Name</label>
                            <input name="bank_name"
                                   value="{{ old('bank_name', $user->bank_name) }}"
                                   type="text"
                                   class="w-full h-10 px-3 rounded-xl border @error('bank_name') border-red-400 @else border-slate-200 @enderror bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-200"
                                   placeholder="e.g. Alpha Bank, Piraeus Bank">
                            @error('bank_name')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1.5">IBAN</label>
                            <input name="iban"
                                   value="{{ old('iban', $user->iban) }}"
                                   type="text"
                                   class="w-full h-10 px-3 rounded-xl border @error('iban') border-red-400 @else border-slate-200 @enderror bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 font-mono"
                                   placeholder="e.g. GR16 0110 1250 0000 0001 2300 695">
                            @error('iban')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- Section 2: IRIS --}}
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide mb-3" style="color:#2563eb;">
                        ⚡ IRIS Instant Payment (Optional)
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">
                            IRIS Number
                            <span class="font-normal text-slate-400 ml-1">— your phone number registered with IRIS</span>
                        </label>
                        <input name="iris_number"
                               value="{{ old('iris_number', $user->iris_number) }}"
                               type="text"
                               class="w-full h-10 px-3 rounded-xl border @error('iris_number') border-red-400 @else border-slate-200 @enderror bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-200"
                               placeholder="e.g. 6912345678">
                        @error('iris_number')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                        <p class="mt-1 text-xs text-slate-400">IRIS allows buyers to pay instantly via mobile banking. Leave blank if not applicable.</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
                    <button type="submit"
                            class="inline-flex items-center gap-2 h-10 px-5 rounded-xl text-white text-sm font-medium hover:opacity-90 transition shadow-sm"
                            style="background:#2563eb;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Payment Info
                    </button>
                    @if (session('status') === 'payment-updated')
                        <span class="text-sm font-medium" style="color:#2563eb;">✓ Payment info saved</span>
                    @endif
                </div>
            </form>
        </div>

        {{-- ── Language ── --}}
        <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-emerald-100 flex items-center gap-3">
                <div class="h-8 w-8 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                    </svg>
                </div>
                <div class="font-semibold text-sm text-slate-900">Language</div>
            </div>

            <form method="POST" action="{{ route('profile.locale.update') }}" class="px-6 py-5 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Default language</label>
                    <select name="locale"
                            class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                        @foreach(config('agroflux.locales') as $code => $meta)
                            <option value="{{ $code }}" @selected(old('locale', $user->locale ?? 'en') === $code)>
                                {{ $meta['flag'] }} {{ $meta['label'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('locale')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
                    <button type="submit"
                            class="inline-flex items-center gap-2 h-10 px-5 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Language
                    </button>
                    @if(session('status') === 'Language preference saved.')
                        <span class="text-sm text-emerald-700 font-medium">✓ Saved</span>
                    @endif
                </div>
            </form>
        </div>

        {{-- ── Update password ── --}}
        <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-emerald-100 flex items-center gap-3">
                <div class="h-8 w-8 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div class="font-semibold text-sm text-slate-900">Update Password</div>
            </div>

            <form method="POST" action="{{ route('password.update') }}" class="px-6 py-5 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">Current password</label>
                    <input name="current_password"
                           type="password"
                           autocomplete="current-password"
                           class="w-full h-10 px-3 rounded-xl border @if($errors->updatePassword->has('current_password')) border-red-400 @else border-slate-200 @endif bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                    @if($errors->updatePassword->has('current_password'))
                        <p class="mt-1.5 text-xs text-red-600">{{ $errors->updatePassword->first('current_password') }}</p>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">New password</label>
                        <input name="password"
                               type="password"
                               autocomplete="new-password"
                               class="w-full h-10 px-3 rounded-xl border @if($errors->updatePassword->has('password')) border-red-400 @else border-slate-200 @endif bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                        @if($errors->updatePassword->has('password'))
                            <p class="mt-1.5 text-xs text-red-600">{{ $errors->updatePassword->first('password') }}</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">Confirm new password</label>
                        <input name="password_confirmation"
                               type="password"
                               autocomplete="new-password"
                               class="w-full h-10 px-3 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
                    <button type="submit"
                            class="inline-flex items-center gap-2 h-10 px-5 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        Update Password
                    </button>
                    @if (session('status') === 'password-updated')
                        <span class="text-sm text-emerald-700 font-medium">✓ Password updated</span>
                    @endif
                </div>
            </form>
        </div>

        {{-- ── Delete account ── --}}
        <div class="rounded-2xl border border-red-100 bg-white shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-red-100 flex items-center gap-3">
                <div class="h-8 w-8 rounded-xl bg-red-50 border border-red-100 flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <div class="font-semibold text-sm text-red-700">Delete Account</div>
            </div>

            <div class="px-6 py-5">
                <p class="text-sm text-slate-500 mb-4">
                    Once your account is deleted, all of its data will be permanently removed. This action cannot be undone.
                </p>

                <div x-data="{ open: false }">
                    <button type="button"
                            x-on:click="open = !open"
                            class="inline-flex items-center gap-2 h-10 px-5 rounded-xl border border-red-200 bg-red-50 hover:bg-red-100 transition text-sm text-red-700 font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete my account
                    </button>

                    <div x-show="open" x-transition class="mt-4 rounded-xl border border-red-200 bg-red-50 p-4">
                        <p class="text-sm font-medium text-red-800 mb-3">Enter your password to confirm:</p>
                        <form method="POST" action="{{ route('profile.destroy') }}" class="space-y-3">
                            @csrf
                            @method('DELETE')

                            <input name="password"
                                   type="password"
                                   placeholder="Your current password"
                                   class="w-full h-10 px-3 rounded-xl border @if($errors->userDeletion->has('password')) border-red-400 @else border-red-200 @endif bg-white text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
                            @if($errors->userDeletion->has('password'))
                                <p class="text-xs text-red-600">{{ $errors->userDeletion->first('password') }}</p>
                            @endif

                            <div class="flex gap-2">
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 h-9 px-4 rounded-xl bg-red-600 text-white text-sm font-medium hover:bg-red-700 transition">
                                    Confirm deletion
                                </button>
                                <button type="button"
                                        x-on:click="open = false"
                                        class="inline-flex items-center h-9 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 transition text-sm text-slate-600">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- /left column --}}

    {{-- ══ RIGHT COLUMN — My Plan (sticky) ══ --}}
    <div style="position:sticky; top:1.5rem;" class="space-y-4">

        {{-- ── My Plan card ── --}}
        <div class="rounded-2xl border border-emerald-100 bg-white shadow-sm overflow-hidden">

            {{-- Header --}}
            <div class="px-5 py-4 border-b border-emerald-100 flex items-center gap-3"
                 style="background:linear-gradient(135deg,#f0fdf4 0%,#dcfce7 100%);">
                <div class="h-9 w-9 rounded-xl bg-white border border-emerald-200 flex items-center justify-center flex-shrink-0 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div class="font-semibold text-sm text-slate-900">My Plan</div>

                @if($isTrial)
                    <span class="ml-auto inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold"
                          style="background:#fef3c7; color:#92400e; border:1px solid #fde68a;">
                        Trial
                    </span>
                @elseif($planName)
                    <span class="ml-auto inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold tracking-wide"
                          style="{{ $planKey === 'plus' ? 'background:#d1fae5; color:#065f46; border:1px solid #a7f3d0;' : 'background:#f1f5f9; color:#475569; border:1px solid #cbd5e1;' }}">
                        {{ strtoupper($planKey) }}
                    </span>
                @else
                    <span class="ml-auto inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold"
                          style="background:#fef2f2; color:#991b1b; border:1px solid #fecaca;">
                        No Plan
                    </span>
                @endif
            </div>

            <div class="px-5 py-5">

                @if($isTrial)
                    {{-- ── Trial state ── --}}
                    <div class="mb-5">
                        <div class="text-base font-bold text-slate-900 mb-0.5">AgroFlux Trial</div>
                        <div class="text-xs text-slate-500 mb-3">Full platform access during your trial.</div>
                        <div class="flex items-center gap-2 text-xs font-semibold rounded-xl px-3 py-2"
                             style="background:#fef3c7; color:#92400e;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Trial ends {{ $trialEndsAt->format('d M Y') }}
                            &nbsp;·&nbsp; {{ $trialEndsAt->diffForHumans() }}
                        </div>
                    </div>

                @elseif($planName)
                    {{-- ── Active subscription ── --}}
                    <div class="mb-4">
                        <div class="text-base font-bold text-slate-900 leading-tight">{{ $planName }}</div>
                        @if($planPrice !== null)
                            <div class="text-xs text-slate-500 mt-0.5">
                                €{{ number_format((float)$planPrice, 2) }}
                                @if($planCycle) / {{ $planCycle }} @endif
                            </div>
                        @endif
                    </div>

                    {{-- Expiry badge --}}
                    @if($endsAt)
                        <div class="flex items-center justify-between rounded-xl px-3 py-2.5 mb-4 text-xs font-medium"
                             style="background:{{ $daysLeft < 0 ? '#fef2f2' : ($daysLeft <= 30 ? '#fefce8' : '#f0fdf4') }}; color:{{ $expiryColor }}; border:1px solid {{ $daysLeft < 0 ? '#fecaca' : ($daysLeft <= 30 ? '#fef08a' : '#bbf7d0') }};">
                            <span class="flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Expires {{ $endsAt->format('d M Y') }}
                            </span>
                            <span class="font-bold">
                                {{ $daysLeft >= 0 ? $daysLeft . 'd left' : 'Expired' }}
                            </span>
                        </div>
                    @endif

                    {{-- Module chips --}}
                    @if(count($planModules) > 0)
                        <div class="mb-4">
                            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-2">Included Features</div>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($planModules as $mod)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-medium"
                                          style="background:#f0fdf4; color:#166534; border:1px solid #bbf7d0;">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        {{ $moduleLabels[$mod] ?? $mod }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                @else
                    {{-- ── No subscription ── --}}
                    <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-4 text-center mb-4">
                        <div class="text-sm font-medium text-slate-600 mb-1">No active plan</div>
                        <div class="text-xs text-slate-400">Contact your administrator to activate a subscription.</div>
                    </div>
                @endif

                {{-- ── Action buttons ── --}}
                <div class="space-y-2 pt-3 border-t border-slate-100">
                    @if($subscription)
                        <button type="button"
                                onclick="document.getElementById('renewModal').classList.remove('hidden')"
                                class="w-full inline-flex items-center justify-center gap-2 h-10 px-4 rounded-xl text-white text-sm font-medium hover:opacity-90 transition"
                                style="background:#059669;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Renew Subscription
                        </button>
                    @endif

                    @if(isset($upgradePlans) && $upgradePlans->count() > 0)
                        <button type="button"
                                onclick="document.getElementById('upgradeModal').classList.remove('hidden')"
                                class="w-full inline-flex items-center justify-center gap-2 h-10 px-4 rounded-xl text-white text-sm font-medium hover:opacity-90 transition"
                                style="background:#0284c7;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                            Upgrade Plan
                        </button>
                    @endif

                    @if(auth()->user()->is_super_admin && $tenant)
                        <a href="{{ route('admin.subscriptions.edit', $tenant) }}"
                           class="w-full inline-flex items-center justify-center gap-2 h-9 px-4 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-xs font-medium text-slate-500 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Admin: Manage Subscription
                        </a>
                    @endif
                </div>

            </div>
        </div>{{-- /My Plan card --}}

    </div>{{-- /right column --}}

</div>{{-- /2-column grid --}}

{{-- ══ MODALS (fixed overlay — position in DOM irrelevant) ══ --}}

{{-- Renew modal --}}
<div id="renewModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
     style="background:rgba(0,0,0,0.5);"
     onclick="if(event.target===this)document.getElementById('renewModal').classList.add('hidden')">
    <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="h-10 w-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background:#f0fdf4;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </div>
            <div>
                <div class="font-semibold text-slate-900">Renew Subscription</div>
                @if($planName)<div class="text-xs text-slate-500 mt-0.5">{{ $planName }}</div>@endif
            </div>
            <button onclick="document.getElementById('renewModal').classList.add('hidden')"
                    class="ml-auto text-slate-400 hover:text-slate-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <p class="text-sm text-slate-600 mb-5">
            To renew your plan, please contact your AgroFlux administrator or reach us at
            <a href="mailto:billing@agroflux.io" class="text-emerald-600 hover:underline font-medium">billing@agroflux.io</a>.
        </p>
        <button onclick="document.getElementById('renewModal').classList.add('hidden')"
                class="w-full h-10 rounded-xl text-sm font-medium text-white hover:opacity-90 transition"
                style="background:#059669;">Close</button>
    </div>
</div>

{{-- Upgrade modal --}}
@if(isset($upgradePlans) && $upgradePlans->count() > 0)
<div id="upgradeModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
     style="background:rgba(0,0,0,0.5);"
     onclick="if(event.target===this)document.getElementById('upgradeModal').classList.add('hidden')">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="h-10 w-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background:#eff6ff;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            <div class="font-semibold text-slate-900">Upgrade Your Plan</div>
            <button onclick="document.getElementById('upgradeModal').classList.add('hidden')"
                    class="ml-auto text-slate-400 hover:text-slate-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="space-y-3 mb-5">
            @foreach($upgradePlans as $up)
                <div class="rounded-xl border border-slate-200 p-4 flex items-center justify-between gap-3 hover:border-blue-200 hover:bg-blue-50 transition">
                    <div>
                        <div class="font-semibold text-slate-900 text-sm">{{ $up->name }}</div>
                        @if($up->description)
                            <div class="text-xs text-slate-500 mt-0.5">{{ $up->description }}</div>
                        @endif
                    </div>
                    <div class="text-right shrink-0">
                        @if($up->price !== null)
                            <div class="font-bold text-slate-900">€{{ number_format((float)$up->price, 2) }}</div>
                            @if($up->billing_cycle)
                                <div class="text-xs text-slate-400">/ {{ $up->billing_cycle }}</div>
                            @endif
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <p class="text-xs text-slate-400 mb-4 text-center">
            Contact <a href="mailto:billing@agroflux.io" class="text-blue-600 hover:underline font-medium">billing@agroflux.io</a> to upgrade your plan.
        </p>
        <button onclick="document.getElementById('upgradeModal').classList.add('hidden')"
                class="w-full h-10 rounded-xl text-sm font-medium text-white hover:opacity-90 transition"
                style="background:#0284c7;">Close</button>
    </div>
</div>
@endif

@endsection
