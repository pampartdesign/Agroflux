@extends('admin._layout')

@section('page_title', 'Add Region')

@section('page_actions')
  <a href="{{ route('admin.regions.index') }}" class="text-sm underline text-gray-700">Back</a>
@endsection

@section('page_content')
  <form method="POST" action="{{ route('admin.regions.store') }}" class="bg-white rounded-xl shadow-sm border p-6 space-y-4 max-w-xl">
    @csrf

    <div>
      <label class="block text-sm font-medium mb-1">Name</label>
      <input name="name" value="{{ old('name') }}" class="w-full rounded-lg border-gray-300" required>
      @error('name')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Code (optional)</label>
      <input name="code" value="{{ old('code') }}" class="w-full rounded-lg border-gray-300" placeholder="e.g., GR-TH">
      @error('code')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
    </div>

    <label class="inline-flex items-center gap-2">
      <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300" checked>
      <span class="text-sm">Active</span>
    </label>

    <button class="rounded-lg bg-emerald-600 px-4 py-2 text-white text-sm">Create</button>
  </form>
@endsection
