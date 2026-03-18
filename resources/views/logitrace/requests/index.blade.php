@extends('logitrace._layout')

@section('content')
  <div class="flex items-center justify-between mb-4">
    <div>
      <h2 class="text-xl font-semibold">My Delivery Requests</h2>
      <p class="text-sm text-gray-600">Create requests to receive offers, or mark as self-delivered.</p>
    </div>
    <a href="{{ route('logi.requests.create') }}" class="px-4 py-2 rounded-lg bg-emerald-600 text-white">+ Create Request</a>
  </div>

  <div class="rounded-xl border bg-white overflow-hidden">
    <table class="w-full text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="text-left px-4 py-3">#</th>
          <th class="text-left px-4 py-3">Farm</th>
          <th class="text-left px-4 py-3">Pickup</th>
          <th class="text-left px-4 py-3">Status</th>
          <th class="text-left px-4 py-3">Mode</th>
          <th class="text-right px-4 py-3">Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($requests as $r)
          <tr class="border-t">
            <td class="px-4 py-3">{{ $r->id }}</td>
            <td class="px-4 py-3">{{ $r->farm->name ?? ('Farm #'.$r->farm_id) }}</td>
            <td class="px-4 py-3">{{ $r->pickup_address }}</td>
            <td class="px-4 py-3">
              <span class="px-2 py-1 rounded-full bg-gray-100 text-gray-700 text-xs">{{ strtoupper($r->status) }}</span>
            </td>
            <td class="px-4 py-3">
              <span class="px-2 py-1 rounded-full bg-blue-50 text-blue-700 text-xs">{{ strtoupper($r->delivery_mode) }}</span>
            </td>
            <td class="px-4 py-3 text-right">
              <a class="underline" href="{{ route('logi.requests.show', $r) }}">Open</a>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">No delivery requests yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    {{ $requests->links() }}
  </div>
@endsection
