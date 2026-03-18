<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ __('app.subscription_required_page_title') }}</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900">
  <div class="max-w-2xl mx-auto py-16 px-4">
    <div class="bg-white shadow rounded-xl p-8">
      <h1 class="text-2xl font-semibold">{{ __('app.subscription_required') }}</h1>
      <p class="text-sm text-gray-600 mt-2">
        {{ __('app.subscription_expired_desc') }}
      </p>

      <div class="mt-6 flex items-center gap-3">
        <a href="{{ route('tenant.select') }}" class="underline text-sm">{{ __('app.switch_organization') }}</a>
        <a href="{{ route('dashboard') }}" class="underline text-sm">{{ __('app.back_to_dashboard') }}</a>
      </div>

      <div class="mt-8 text-xs text-gray-500">
        {{ __('app.payment_integration_note') }}
      </div>
    </div>
  </div>
</body>
</html>
