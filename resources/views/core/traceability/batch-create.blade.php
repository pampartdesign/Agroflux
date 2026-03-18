<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>New Batch - AgroFlux</title>@vite(['resources/css/app.css','resources/js/app.js'])</head>
<body class="bg-gray-50 text-gray-900">
<div class="max-w-2xl mx-auto py-10 px-4">
  <div class="bg-white shadow rounded-xl p-6">
    <h1 class="text-2xl font-semibold mb-6">Create Batch</h1>
    <form method="POST" action="{{ route('core.traceability.batch.store') }}" class="space-y-4">
      @csrf
      <div>
        <label class="block text-sm font-medium mb-1">Product</label>
        <select name="product_id" class="w-full rounded-lg border-gray-300">
          @foreach($products as $p)
            <option value="{{ $p->id }}">{{ $p->default_name }}</option>
          @endforeach
        </select>
        @error('product_id')<p class="text-sm text-red-600 mt-2">{{ $message }}</p>@enderror
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Batch Code</label>
        <input name="code" value="{{ old('code') }}" class="w-full rounded-lg border-gray-300" placeholder="e.g., LOT-2026-0001">
        @error('code')<p class="text-sm text-red-600 mt-2">{{ $message }}</p>@enderror
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">Status</label>
        <select name="status" class="w-full rounded-lg border-gray-300">
          <option value="draft">draft</option>
          <option value="published">published</option>
          <option value="archived">archived</option>
        </select>
      </div>

      <div class="flex items-center gap-3">
        <button class="rounded-lg bg-black text-white px-4 py-2 text-sm">Save</button>
        <a href="{{ route('core.traceability.index') }}" class="text-sm underline">Cancel</a>
      </div>
    </form>
  </div>
</div>
</body></html>
