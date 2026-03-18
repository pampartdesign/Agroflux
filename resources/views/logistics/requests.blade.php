@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-8 px-4">
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold">My Delivery Requests</h1>
    <a class="text-sm underline" href="/dashboard">Back</a>
  </div>

  @if (session('status'))
    <div class="mb-4 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">{{ session('status') }}</div>
  @endif
  @if (session('error'))
    <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
  @endif

  <div class="bg-white border rounded-xl p-6 mb-6">
    <h2 class="font-semibold mb-3">Create Request</h2>
    <form method="POST" action="/logistics/requests" class="grid grid-cols-1 md:grid-cols-3 gap-3">
      @csrf
      <input name="pickup_address" placeholder="Pickup address" class="rounded-lg border-gray-300" required>
      <input name="destination" placeholder="Destination" class="rounded-lg border-gray-300" required>
      <button class="rounded-lg bg-emerald-600 px-4 py-2 text-white text-sm">Create Draft</button>
    </form>
  </div>

  <div class="space-y-4">
    @forelse($requests as $r)
      <div class="bg-white border rounded-xl p-6">
        <div class="flex items-start justify-between gap-4">
          <div>
            <div class="font-semibold">{{ $r->pickup_address }} → {{ $r->destination }}</div>
            <div class="text-sm text-gray-600 mt-1">Status: <span class="font-medium">{{ $r->status }}</span></div>
          </div>
          <div class="flex flex-wrap gap-2 justify-end">
            <form method="POST" action="/logistics/requests/{{ $r->id }}/publish">@csrf
              <button class="rounded-lg bg-gray-900 px-3 py-2 text-white text-xs">Publish</button>
            </form>
            <form method="POST" action="/logistics/requests/{{ $r->id }}/self-deliver">@csrf
              <button class="rounded-lg bg-blue-600 px-3 py-2 text-white text-xs">Self-deliver</button>
            </form>
            <form method="POST" action="/logistics/requests/{{ $r->id }}/complete">@csrf
              <button class="rounded-lg bg-emerald-600 px-3 py-2 text-white text-xs">Complete</button>
            </form>
            <form method="POST" action="/logistics/requests/{{ $r->id }}/cancel">@csrf
              <button class="rounded-lg bg-red-600 px-3 py-2 text-white text-xs">Cancel</button>
            </form>
          </div>
        </div>

        @if($r->offers && count($r->offers))
          <div class="mt-4 border-t pt-4">
            <div class="font-semibold mb-2">Offers</div>
            <div class="space-y-2">
              @foreach($r->offers as $o)
                <div class="flex items-center justify-between bg-gray-50 border rounded-lg p-3">
                  <div class="text-sm">
                    <span class="font-medium">€{{ $o->price }}</span>
                    <span class="text-gray-600">— {{ $o->status }}</span>
                  </div>
                  @if($o->status === 'pending')
                    <form method="POST" action="/logistics/offer/{{ $o->id }}/accept">
                      @csrf
                      <button class="rounded-lg bg-gray-900 px-3 py-2 text-white text-xs">Accept</button>
                    </form>
                  @endif
                </div>
              @endforeach
            </div>
          </div>
        @endif
      </div>
    @empty
      <div class="text-sm text-gray-600">No delivery requests yet.</div>
    @endforelse
  </div>
</div>
@endsection
