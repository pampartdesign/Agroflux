@extends('layouts.app')
@section('content')

<div class="flex items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-semibold text-slate-900">Authorized Sellers</h1>
        <p class="text-sm text-slate-500 mt-1">Manage companies listed on the Authorized Sellers page.</p>
    </div>
    <a href="{{ route('admin.authorized-sellers.create') }}"
       class="inline-flex items-center gap-2 h-10 px-4 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm">
        + Add Seller
    </a>
</div>

@if(session('status'))
<div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
    {{ session('status') }}
</div>
@endif

@if($sellers->isEmpty())
<div class="rounded-2xl border border-slate-100 bg-white shadow-sm p-16 text-center">
    <div class="text-4xl mb-3">🏢</div>
    <p class="text-slate-500 text-sm">No authorized sellers yet. Add your first company.</p>
</div>
@else
<div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-slate-100">
                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide w-12">Order</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide">Company</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide hidden sm:table-cell">Category</th>
                <th class="px-5 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wide w-20">Products</th>
                <th class="px-5 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wide w-24">Status</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-slate-400 uppercase tracking-wide w-32">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @foreach($sellers as $seller)
            <tr class="hover:bg-slate-50/60 transition">
                <td class="px-5 py-3 text-slate-500">
                    {{ $seller->sort_order ?: '—' }}
                </td>
                <td class="px-5 py-3">
                    <div class="flex items-center gap-3">
                        @if($seller->featuredImageUrl())
                        <img src="{{ $seller->featuredImageUrl() }}" alt=""
                             class="h-10 w-10 rounded-xl object-cover flex-shrink-0 border border-slate-100">
                        @else
                        <div class="h-10 w-10 rounded-xl bg-slate-100 flex items-center justify-center flex-shrink-0 text-slate-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/>
                            </svg>
                        </div>
                        @endif
                        <div>
                            <div class="font-medium text-slate-900">{{ $seller->company_name }}</div>
                            @if($seller->short_description)
                            <div class="text-xs text-slate-400 truncate max-w-xs">{{ Str::limit($seller->short_description, 60) }}</div>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="px-5 py-3 hidden sm:table-cell text-slate-500">{{ $seller->category ?: '—' }}</td>
                <td class="px-5 py-3 text-center text-slate-600">{{ $seller->products_count }}</td>
                <td class="px-5 py-3 text-center">
                    <form method="POST" action="{{ route('admin.authorized-sellers.toggle', $seller) }}">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-1 text-xs font-medium px-2.5 py-1 rounded-full transition {{ $seller->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-100 hover:bg-red-50 hover:text-red-700 hover:border-red-100' : 'bg-slate-100 text-slate-500 border border-slate-200 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-100' }}">
                            {{ $seller->is_active ? 'Published' : 'Hidden' }}
                        </button>
                    </form>
                </td>
                <td class="px-5 py-3 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('admin.authorized-sellers.edit', $seller) }}"
                           class="text-xs px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-600 transition">
                            Edit
                        </a>
                        <form method="POST" action="{{ route('admin.authorized-sellers.destroy', $seller) }}"
                              onsubmit="return confirm('Delete {{ addslashes($seller->company_name) }}?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="text-xs px-3 py-1.5 rounded-lg border border-red-100 hover:bg-red-50 text-red-600 transition">
                                Delete
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<p class="mt-3 text-xs text-slate-400">
    Sort order: lower numbers appear first. Sellers with order 0 are sorted alphabetically at the end.
</p>
@endif

@endsection
