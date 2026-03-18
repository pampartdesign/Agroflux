@extends('admin._layout')

@section('page_title', $parentId ? 'New Sub-category' : 'New Category')
@section('page_subtitle', $parentId
    ? 'Adding a sub-category under: ' . ($parents->find($parentId)?->name ?? '—')
    : 'Create a top-level category. You can add sub-categories to it afterwards.')

@section('page_actions')
    <a href="{{ route('admin.categories.index') }}" class="text-sm underline text-gray-600">← Back to Categories</a>
@endsection

@section('page_content')

<form method="POST" action="{{ route('admin.categories.store') }}"
      class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-5 max-w-lg">
    @csrf

    {{-- Name --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">
            Name <span class="text-red-500">*</span>
        </label>
        <input name="name"
               value="{{ old('name') }}"
               type="text"
               required
               autofocus
               placeholder="{{ $parentId ? 'e.g. Extra Virgin, Organic, Cold Pressed…' : 'e.g. Olive Oil, Cereals, Livestock…' }}"
               class="w-full h-10 px-3 rounded-lg border @error('name') border-red-400 @else border-gray-300 @enderror bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
        @error('name')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
        <p class="mt-1 text-xs text-gray-400">The slug is generated automatically from the name.</p>
    </div>

    {{-- Parent category --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Parent category</label>
        <select name="parent_id"
                class="w-full h-10 px-3 rounded-lg border @error('parent_id') border-red-400 @else border-gray-300 @enderror bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
            <option value="">— None (top-level category) —</option>
            @foreach($parents as $parent)
                <option value="{{ $parent->id }}"
                    {{ (string) old('parent_id', $parentId) === (string) $parent->id ? 'selected' : '' }}>
                    {{ $parent->name }}
                </option>
            @endforeach
        </select>
        @error('parent_id')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
        <p class="mt-1 text-xs text-gray-400">
            Leave empty for a top-level category. Select a parent to make this a sub-category.
        </p>
    </div>

    {{-- Actions --}}
    <div class="pt-2 border-t border-gray-100 flex items-center gap-3">
        <button type="submit"
                class="inline-flex items-center h-10 px-5 rounded-lg bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm">
            Create
        </button>
        <a href="{{ route('admin.categories.index') }}"
           class="inline-flex items-center h-10 px-4 rounded-lg border border-gray-200 bg-white text-sm text-gray-600 hover:bg-gray-50 transition">
            Cancel
        </a>
    </div>
</form>

@endsection
