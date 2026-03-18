<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AgroFlux - LogiTrace</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900">
  <div class="max-w-6xl mx-auto py-8 px-4">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-semibold">LogiTrace</h1>
        <p class="text-sm text-gray-600">Optional logistics marketplace: requests, offers, and routing.</p>
      </div>
      <div class="flex items-center gap-2">
        <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-lg border bg-white text-sm">Back to Dashboard</a>
      </div>
    </div>

    @if(session('status'))
      <div class="mb-6 rounded-lg border bg-green-50 text-green-800 px-4 py-3 text-sm">
        {{ session('status') }}
      </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <a class="rounded-xl border bg-white p-4 hover:shadow-sm" href="{{ route('logi.dashboard') }}">
        <div class="font-semibold">Overview</div>
        <div class="text-sm text-gray-600 mt-1">Summary</div>
      </a>
      <a class="rounded-xl border bg-white p-4 hover:shadow-sm" href="{{ route('logi.requests.index') }}">
        <div class="font-semibold">Farmer Requests</div>
        <div class="text-sm text-gray-600 mt-1">Create and manage</div>
      </a>
      <a class="rounded-xl border bg-white p-4 hover:shadow-sm" href="{{ route('logi.available.index') }}">
        <div class="font-semibold">Available Requests</div>
        <div class="text-sm text-gray-600 mt-1">Trucker marketplace</div>
      </a>
      <a class="rounded-xl border bg-white p-4 hover:shadow-sm" href="{{ route('logi.offers.mine') }}">
        <div class="font-semibold">My Offers</div>
        <div class="text-sm text-gray-600 mt-1">Track bids</div>
      </a>
    </div>

    @yield('content')
  </div>
</body>
</html>
