@extends('logitrace._layout')

@section('content')
  <div class="rounded-xl border bg-white p-6 max-w-3xl">
    <div class="mb-4">
      <h2 class="text-xl font-semibold">Trucker Profile</h2>
      <p class="text-sm text-gray-600">Required to send offers. Vehicle type helps farmers pick the right transport.</p>
    </div>

    <form method="POST" action="{{ route('logi.trucker.profile.update') }}" class="space-y-4">
      @csrf

      <div>
        <label class="block text-sm font-medium mb-1">Company name</label>
        <input name="company_name" value="{{ old('company_name', $profile->company_name ?? '') }}" class="w-full rounded-lg border-gray-300" />
        @error('company_name')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Phone</label>
        <input name="phone" value="{{ old('phone', $profile->phone ?? '') }}" class="w-full rounded-lg border-gray-300" />
        @error('phone')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Vehicle type</label>
        <select name="vehicle_type" class="w-full rounded-lg border-gray-300">
          @php($v = old('vehicle_type', $profile->vehicle_type ?? 'van'))
          <option value="van" @selected($v==='van')>Van</option>
          <option value="pickup" @selected($v==='pickup')>Small Pickup</option>
          <option value="refrigerated" @selected($v==='refrigerated')>Refrigerated Truck</option>
          <option value="truck" @selected($v==='truck')>Truck</option>
        </select>
        @error('vehicle_type')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Notes</label>
        <textarea name="notes" class="w-full rounded-lg border-gray-300" rows="4">{{ old('notes', $profile->notes ?? '') }}</textarea>
        @error('notes')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
      </div>

      <div class="flex items-center gap-2">
        <button class="px-4 py-2 rounded-lg bg-emerald-600 text-white">Save</button>
        <a class="px-4 py-2 rounded-lg border bg-white" href="{{ route('logi.available.index') }}">Browse requests</a>
      </div>
    </form>
  </div>
@endsection
