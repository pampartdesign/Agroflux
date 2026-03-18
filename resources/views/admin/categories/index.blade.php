@extends('admin._layout')

@section('page_title', 'Product Categories')
@section('page_subtitle', 'Manage the global category & sub-category tree. All subscribers see these when adding or editing products.')

@section('page_actions')
    <a href="{{ route('admin.categories.create') }}"
       class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-4 py-2 text-white text-sm font-medium hover:bg-emerald-700 transition">
        + New Category
    </a>
@endsection

@section('page_content')

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

    {{-- Legend --}}
    <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-6 text-xs text-gray-500">
        <span class="flex items-center gap-1.5">
            <span class="inline-block px-2 py-0.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 font-medium">Category</span>
            top-level group
        </span>
        <span class="flex items-center gap-1.5">
            <span class="inline-block px-2 py-0.5 rounded-full bg-sky-50 border border-sky-200 text-sky-700 font-medium">Sub-category</span>
            child of a category
        </span>
    </div>

    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Name / Slug</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Type</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Sub-cats</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Products</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $cat)

                {{-- ── Parent category row ── --}}
                <tr class="border-t border-gray-100 bg-slate-50/60 hover:bg-slate-50 transition">
                    <td class="px-5 py-3">
                        <div class="font-semibold text-gray-900">{{ $cat->name }}</div>
                        <div class="text-xs text-gray-400 font-mono mt-0.5">{{ $cat->slug }}</div>
                    </td>
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-50 border border-emerald-200 text-xs text-emerald-700 font-medium">
                            Category
                        </span>
                    </td>
                    <td class="px-5 py-3 text-gray-600 font-medium">{{ $cat->children_count }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ $cat->products_count }}</td>
                    <td class="px-5 py-3 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.categories.create', ['parent_id' => $cat->id]) }}"
                               class="text-xs text-emerald-600 hover:text-emerald-800 hover:underline font-medium transition">
                                + Sub-category
                            </a>
                            <a href="{{ route('admin.categories.edit', $cat) }}"
                               class="text-xs text-gray-600 hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="text-xs text-red-500 hover:underline"
                                        onclick="return confirm('Delete "{{ addslashes($cat->name) }}" and all its sub-categories?')">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>

                {{-- ── Sub-category rows ── --}}
                @foreach($cat->children as $child)
                <tr class="border-t border-gray-100 hover:bg-slate-50/40 transition">
                    <td class="px-5 py-3 pl-10">
                        <div class="flex items-start gap-2">
                            <span class="text-gray-300 text-base leading-none mt-0.5">└</span>
                            <div>
                                <div class="font-medium text-gray-800">{{ $child->name }}</div>
                                <div class="text-xs text-gray-400 font-mono mt-0.5">{{ $child->slug }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-sky-50 border border-sky-200 text-xs text-sky-700 font-medium">
                            Sub-category
                        </span>
                    </td>
                    <td class="px-5 py-3 text-gray-400">—</td>
                    <td class="px-5 py-3 text-gray-600">{{ $child->sub_products_count }}</td>
                    <td class="px-5 py-3 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.categories.edit', $child) }}"
                               class="text-xs text-gray-600 hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.categories.destroy', $child) }}" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="text-xs text-red-500 hover:underline"
                                        onclick="return confirm('Delete sub-category "{{ addslashes($child->name) }}"?')">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach

                {{-- Empty sub-category hint --}}
                @if($cat->children_count === 0)
                <tr class="border-t border-dashed border-gray-100">
                    <td colspan="5" class="px-10 py-2 text-xs text-gray-400 italic">
                        No sub-categories —
                        <a href="{{ route('admin.categories.create', ['parent_id' => $cat->id]) }}"
                           class="text-emerald-600 hover:underline">add one</a>
                    </td>
                </tr>
                @endif

            @empty
                <tr>
                    <td colspan="5" class="px-5 py-14 text-center">
                        <div class="text-gray-400 text-sm">No categories yet.</div>
                        <a href="{{ route('admin.categories.create') }}"
                           class="mt-3 inline-flex items-center rounded-lg bg-emerald-600 px-4 py-2 text-white text-sm hover:bg-emerald-700 transition">
                            Create your first category
                        </a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
