<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('app.app_name') }} - {{ __('app.create_org_title') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900">
<div class="max-w-xl mx-auto py-10 px-4">
    <div class="bg-white shadow rounded-xl p-6">
        <h1 class="text-2xl font-semibold mb-2">{{ __('app.create_org_title') }}</h1>
        <p class="text-sm text-gray-600 mb-6">
            {{ __('app.create_org_subtitle', ['days' => config('agroflux.trial_days', 14)]) }}
        </p>

        <form method="POST" action="{{ route('onboarding.tenant.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1" for="name">{{ __('app.org_name_label') }}</label>
                <input id="name" name="name" value="{{ old('name') }}"
                       class="w-full rounded-lg border-gray-300" placeholder="{{ __('app.org_name_placeholder') }}"/>
                @error('name')
                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-4">
                <button class="inline-flex items-center justify-center rounded-lg bg-black text-white px-4 py-2">
                    {{ __('app.create_and_continue') }}
                </button>
                <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 hover:underline">{{ __('app.skip_for_now') }}</a>
            </div>
        </form>

        <div class="mt-6 text-sm text-gray-600">
            <a href="{{ route('locale.switch', ['locale' => 'en']) }}" class="underline">English</a>
            <span class="mx-2">•</span>
            <a href="{{ route('locale.switch', ['locale' => 'el']) }}" class="underline">Ελληνικά</a>
        </div>
    </div>
</div>
</body>
</html>
