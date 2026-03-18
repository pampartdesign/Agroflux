@extends('admin._layout')

@section('page_title', 'Edit ' . ($category->parent_id ? 'Sub-category' : 'Category'))
@section('page_subtitle', '"' . $category->name . '" — slug: ' . $category->slug)

@section('page_actions')
    <a href="{{ route('admin.categories.index') }}" class="text-sm underline text-gray-600">← Back to Categories</a>
@endsection

@section('page_content')

<form method="POST" action="{{ route('admin.categories.update', $category) }}"
      class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-5 max-w-lg">
    @csrf
    @method('PUT')

    {{-- Name --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">
            Name <span class="text-red-500">*</span>
        </label>
        <input name="name"
               value="{{ old('name', $category->name) }}"
               type="text"
               required
               autofocus
               class="w-full h-10 px-3 rounded-lg border @error('name') border-red-400 @else border-gray-300 @enderror bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
        @error('name')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
        <p class="mt-1 text-xs text-gray-400">
            Slug will be regenerated if the name changes.
            Current: <span class="font-mono">{{ $category->slug }}</span>
        </p>
    </div>

    {{-- Parent category --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Parent category</label>

        @if($category->children()->exists())
            {{-- This IS a parent already — warn and lock --}}
            <div class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-gray-50 text-sm text-gray-400 flex items-center">
                — None (top-level) — cannot change while it has sub-categories
            </div>
            <input type="hidden" name="parent_id" value="">
            <p class="mt-1 text-xs text-gray-400">
                To move this category under another, first re-assign or delete its
                <strong>{{ $category->children()->count() }}</strong> sub-categor{{ $category->children()->count() === 1 ? 'y' : 'ies' }}.
            </p>
        @else
            <select name="parent_id"
                    class="w-full h-10 px-3 rounded-lg border @error('parent_id') border-red-400 @else border-gray-300 @enderror bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                <option value="">— None (top-level category) —</option>
                @foreach($parents as $parent)
                    <option value="{{ $parent->id }}"
                        {{ (string) old('parent_id', $category->parent_id) === (string) $parent->id ? 'selected' : '' }}>
                        {{ $parent->name }}
                    </option>
                @endforeach
            </select>
            @error('parent_id')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-xs text-gray-400">
                Leave empty to keep as top-level. Select a parent to convert to a sub-category.
            </p>
        @endif
    </div>

    {{-- Info chips --}}
    <div class="flex flex-wrap gap-3 py-3 border-t border-b border-gray-100">
        @php
            $productsUsing = \App\Models\Product::where('category_id', $category->id)
                ->orWhere('subcategory_id', $category->id)
                ->count();
        @endphp
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-200 text-xs text-slate-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M20 7l-8-4-8 4 8 4 8-4z"/><path d="M4 7v10l8 4 8-4V7"/><path d="M12 11v10"/>
            </svg>
            {{ $productsUsing }} product{{ $productsUsing !== 1 ? 's' : '' }} using this
        </span>
        @if(!$category->parent_id)
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-200 text-xs text-slate-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/>
            </svg>
            {{ $category->children()->count() }} sub-categor{{ $category->children()->count() === 1 ? 'y' : 'ies' }}
        </span>
        @endif
    </div>

    {{-- Actions --}}
    <div class="flex items-center gap-3">
        <button type="submit"
                class="inline-flex items-center h-10 px-5 rounded-lg bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm">
            Save Changes
        </button>
        <a href="{{ route('admin.categories.index') }}"
           class="inline-flex items-center h-10 px-4 rounded-lg border border-gray-200 bg-white text-sm text-gray-600 hover:bg-gray-50 transition">
            Cancel
        </a>
        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="ml-auto">
            @csrf @method('DELETE')
            <button type="submit"
                    class="inline-flex items-center h-10 px-4 rounded-lg border border-red-200 text-red-600 text-sm hover:bg-red-50 transition"
                    onclick="return confirm('Delete "{{ addslashes($category->name) }}"? This cannot be undone.')">
                Delete
            </button>
        </form>
    </div>
</form>

@endsection
