@extends('logitrace._layout')

@section('content')
  <div class="flex items-center justify-between mb-4">
    <div>
      <h2 class="text-xl font-semibold">Request #{{ $requestModel->id }}</h2>
      <p class="text-sm text-gray-600">{{ $requestModel->pickup_address }}</p>
    </div>
    <div class="flex items-center gap-2">
      <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-xs">{{ strtoupper($requestModel->status) }}</span>
      <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs">{{ strtoupper($requestModel->delivery_mode) }}</span>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="rounded-xl border bg-white p-5 md:col-span-2">
      <h3 class="font-semibold">Details</h3>
      <div class="text-sm text-gray-700 mt-3 space-y-2">
        <div><span class="text-gray-500">Farm:</span> {{ $requestModel->farm->name ?? ('Farm #'.$requestModel->farm_id) }}</div>
        <div><span class="text-gray-500">Pickup:</span> {{ $requestModel->pickup_address }}</div>
        <div><span class="text-gray-500">Delivery:</span> {{ $requestModel->delivery_address ?: '-' }}</div>
        <div><span class="text-gray-500">Cargo:</span> {{ $requestModel->cargo_description ?: '-' }}</div>
        <div><span class="text-gray-500">Weight:</span> {{ $requestModel->cargo_weight_kg ? ($requestModel->cargo_weight_kg.' kg') : '-' }}</div>
        <div><span class="text-gray-500">Requested date:</span> {{ $requestModel->requested_date?->format('Y-m-d') ?? '-' }}</div>
      </div>

      <div class="mt-5 flex flex-wrap gap-2">
        @if(in_array($requestModel->status, ['draft'], true))
          <form method="POST" action="{{ route('logi.requests.publish', $requestModel) }}">
            @csrf
            <button class="px-4 py-2 rounded-lg bg-emerald-600 text-white">Publish to marketplace</button>
          </form>
        @endif

        @if(in_array($requestModel->status, ['draft','open','offered'], true))
          <form method="POST" action="{{ route('logi.requests.self_delivered', $requestModel) }}">
            @csrf
            <button class="px-4 py-2 rounded-lg border bg-white">Mark self-delivered</button>
          </form>
        @endif

        @if(!in_array($requestModel->status, ['cancelled','completed'], true))
          <form method="POST" action="{{ route('logi.requests.cancel', $requestModel) }}">
            @csrf
            <button class="px-4 py-2 rounded-lg border bg-white text-red-700">Cancel</button>
          </form>
        @endif
      </div>
    </div>

    <div class="rounded-xl border bg-white p-5">
      <h3 class="font-semibold">Offers</h3>
      <p class="text-sm text-gray-600 mt-1">Offers appear after publishing. Farmer may accept one OR self-deliver.</p>

      <div class="mt-4 space-y-3">
        @forelse($offers as $o)
          <div class="rounded-lg border p-3">
            <div class="flex items-center justify-between">
              <div class="font-medium">€{{ number_format((float)$o->price, 2) }}</div>
              <div class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-700">{{ strtoupper($o->status) }}</div>
            </div>
            <div class="text-sm text-gray-600 mt-1">
              Trucker: {{ $o->trucker->name ?? ('User #'.$o->trucker_user_id) }}
            </div>
            @if($o->message)
              <div class="text-sm text-gray-700 mt-2">{{ $o->message }}</div>
            @endif

            @if(in_array($requestModel->status, ['open','offered'], true) && $o->status === 'sent')
              <form method="POST" action="{{ route('logi.requests.accept_offer', [$requestModel, $o]) }}" class="mt-3">
                @csrf
                <button class="px-3 py-2 rounded-lg bg-emerald-600 text-white text-sm">Accept Offer</button>
              </form>
            @endif
          </div>
        @empty
          <div class="text-sm text-gray-500">No offers yet.</div>
        @endforelse
      </div>
    </div>
  </div>
@endsection
