@extends('layouts.app')

@section('content')

{{-- Page header --}}
<div class="flex items-center justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
            <a href="{{ route('core.products.index') }}" class="hover:text-emerald-700 transition">{{ __('core.my_products') }}</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-900 font-medium">{{ __('core.new_product_breadcrumb') }}</span>
        </div>
        <h1 class="text-2xl font-semibold text-slate-900">{{ __('core.new_product') }}</h1>
        <p class="text-sm text-slate-500 mt-1">{{ __('core.create_product_subtitle') }}</p>
    </div>
    <a href="{{ route('core.products.index') }}"
       class="inline-flex items-center gap-2 h-10 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 transition text-sm text-slate-600 flex-shrink-0">
        {{ __('core.back_to_products') }}
    </a>
</div>

<form method="POST"
      action="{{ route('core.products.store') }}"
      enctype="multipart/form-data"
      class="max-w-2xl space-y-5">
    @csrf
    @include('core.products._form', ['product' => null])
    <div class="flex items-center gap-3 pt-1">
        <button type="submit"
                class="inline-flex items-center gap-2 h-10 px-5 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            {{ __('core.save_product') }}
        </button>
        <a href="{{ route('core.products.index') }}"
           class="inline-flex items-center h-10 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 transition text-sm text-slate-600">
            {{ __('app.cancel') }}
        </a>
    </div>
</form>

@endsection
