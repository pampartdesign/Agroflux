@extends('layouts.app')

@section('content')

{{-- Page header --}}
<div class="flex items-center justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
            <a href="{{ route('core.products.index') }}" class="hover:text-emerald-700 transition">{{ __('core.my_products') }}</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-900 font-medium truncate">{{ $product->default_name }}</span>
        </div>
        <h1 class="text-2xl font-semibold text-slate-900">{{ __('core.edit_product_title_page') }}</h1>
        <p class="text-sm text-slate-500 mt-1">{{ __('core.edit_product_subtitle') }}</p>
    </div>
    <a href="{{ route('core.products.index') }}"
       class="inline-flex items-center gap-2 h-10 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 transition text-sm text-slate-600 flex-shrink-0">
        {{ __('core.back_to_products') }}
    </a>
</div>

{{-- Stats strip --}}
<div class="max-w-2xl grid grid-cols-3 gap-3 mb-5">
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm px-4 py-3">
        <div class="text-xs text-slate-400 uppercase tracking-wide">{{ __('core.col_listings') }}</div>
        <div class="text-xl font-bold text-slate-900 mt-0.5">{{ $product->listings_count ?? $product->listings()->count() }}</div>
    </div>
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm px-4 py-3">
        <div class="text-xs text-slate-400 uppercase tracking-wide">{{ __('core.col_batches') }}</div>
        <div class="text-xl font-bold text-slate-900 mt-0.5">{{ $product->batches_count ?? $product->batches()->count() }}</div>
    </div>
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm px-4 py-3">
        <div class="text-xs text-slate-400 uppercase tracking-wide">{{ __('core.col_created') }}</div>
        <div class="text-sm font-semibold text-slate-700 mt-0.5">{{ $product->created_at->format('d M Y') }}</div>
    </div>
</div>

<form method="POST"
      action="{{ route('core.products.update', $product) }}"
      enctype="multipart/form-data"
      class="max-w-2xl space-y-5">
    @csrf
    @method('PUT')
    @include('core.products._form', ['product' => $product])
    <div class="flex items-center gap-3 pt-1">
        <button type="submit"
                class="inline-flex items-center gap-2 h-10 px-5 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            {{ __('app.save_changes') }}
        </button>
        <a href="{{ route('core.products.index') }}"
           class="inline-flex items-center h-10 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 transition text-sm text-slate-600">
            {{ __('app.cancel') }}
        </a>
    </div>
</form>

@endsection
