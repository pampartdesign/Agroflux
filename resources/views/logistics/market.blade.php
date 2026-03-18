@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-8 px-4">
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold">Available Deliveries</h1>
    <a class="text-sm underline" href="/dashboard">Back</a>
  </div>

  @if (session('status'))
    <div class="mb-4 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">{{ session('status') }}</div>
  @endif
  @if (session('error'))
    <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
  @endif

  <div class="space-y-4">
    @forelse($requests as $r)
      <div class="bg-white border rounded-xl p-6">
        <div class="flex items-start justify-between gap-4">
          <div>
            <div class="font-semibold">{{ $r->pickup_address }} → {{ $r->destination }}</div>
            <div class="text-sm text-gray-600 mt-1">Status: <span class="font-medium">{{ $r->status }}</span></div>
          </div>
          <div class="w-full max-w-xs">
            <form method="POST" action="/logistics/request/{{ $r->id }}/offer" class="flex gap-2 items-center justify-end">
              @csrf
              <input name="price" placeholder="Offer €" class="rounded-lg border-gray-300 w-28" required>
              <button class="rounded-lg bg-gray-900 px-3 py-2 text-white text-xs">Send Offer</button>
            </form>
          </div>
        </div>
      </div>
    @empty
      <div class="text-sm text-gray-600">No open delivery requests right now.</div>
    @endforelse
  </div>
</div>
@endsection
