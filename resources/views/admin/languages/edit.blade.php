@extends('admin._layout')

@section('page_title', 'Edit Language')

@section('page_actions')
  <a href="{{ route('admin.languages.index') }}" class="text-sm underline text-gray-700">Back</a>
@endsection

@section('page_content')
  <form method="POST" action="{{ route('admin.languages.update', $language) }}" class="bg-white rounded-xl shadow-sm border p-6 space-y-4 max-w-xl">
    @csrf @method('PUT')

    <div>
      <label class="block text-sm font-medium mb-1">Code</label>
      <input value="{{ $language->code }}" class="w-full rounded-lg border-gray-200 bg-gray-50" disabled>
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Name</label>
      <input name="name" value="{{ old('name', $language->name) }}" class="w-full rounded-lg border-gray-300" required>
      @error('name')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
    </div>

    <label class="inline-flex items-center gap-2">
      <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300" @checked(old('is_active', $language->is_active))>
      <span class="text-sm">Active</span>
    </label>

    <label class="inline-flex items-center gap-2">
      <input type="checkbox" name="is_default" value="1" class="rounded border-gray-300" @checked(old('is_default', $language->is_default))>
      <span class="text-sm">Set as default</span>
    </label>

    <button class="rounded-lg bg-emerald-600 px-4 py-2 text-white text-sm">Save</button>
  </form>
@endsection
