@extends('logitrace._layout')

@section('content')
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="rounded-xl border bg-white p-6 md:col-span-2">
      <h2 class="text-xl font-semibold">Request #{{ $requestModel->id }}</h2>
      <p class="text-sm text-gray-600 mt-1">Pickup: {{ $requestModel->pickup_address }}</p>

      <div class="text-sm text-gray-700 mt-4 space-y-2">
        <div><span class="text-gray-500">Delivery:</span> {{ $requestModel->delivery_address ?: '-' }}</div>
        <div><span class="text-gray-500">Cargo:</span> {{ $requestModel->cargo_description ?: '-' }}</div>
        <div><span class="text-gray-500">Weight:</span> {{ $requestModel->cargo_weight_kg ? ($requestModel->cargo_weight_kg.' kg') : '-' }}</div>
        <div><span class="text-gray-500">Requested date:</span> {{ $requestModel->requested_date?->format('Y-m-d') ?? '-' }}</div>
      </div>
    </div>

    <div class="rounded-xl border bg-white p-6">
      <h3 class="font-semibold">Send an Offer</h3>
      <p class="text-sm text-gray-600 mt-1">Offer a price. Farmer may accept or self-deliver.</p>

      @if(!$profile)
        <div class="mt-3 rounded-lg border bg-yellow-50 text-yellow-800 px-3 py-2 text-sm">
          Create your trucker profile first.
          <a class="underline ml-1" href="{{ route('logi.trucker.profile.edit') }}">Go</a>
        </div>
      @endif

      @if($myOffer)
        <div class="mt-4 rounded-lg border bg-gray-50 p-3 text-sm">
          <div class="font-medium">You already offered: €{{ number_format((float)$myOffer->price, 2) }}</div>
          <div class="text-gray-600 mt-1">Status: {{ strtoupper($myOffer->status) }}</div>
          @if($myOffer->message)
            <div class="text-gray-700 mt-2">{{ $myOffer->message }}</div>
          @endif
        </div>
      @else
        <form method="POST" action="{{ route('logi.offers.store', $requestModel) }}" class="mt-4 space-y-3">
          @csrf

          <div>
            <label class="block text-sm font-medium mb-1">Price (EUR)</label>
            <input name="price" value="{{ old('price') }}" class="w-full rounded-lg border-gray-300" />
            @error('price')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
          </div>

          <div>
            <label class="block text-sm font-medium mb-1">Message (optional)</label>
            <textarea name="message" class="w-full rounded-lg border-gray-300" rows="3">{{ old('message') }}</textarea>
            @error('message')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
          </div>

          <button class="px-4 py-2 rounded-lg bg-emerald-600 text-white w-full" @disabled(!$profile)>Send Offer</button>
        </form>
      @endif
    </div>
  </div>
@endsection
