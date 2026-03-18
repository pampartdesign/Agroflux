@extends('layouts.app')

@section('content')
<div class="flex items-start justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-semibold text-slate-900">{{ __('core.listings_title') }}</h1>
        <p class="text-sm text-slate-600 mt-1">{{ __('core.listings_subtitle') }}</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('public.marketplace') }}" target="_blank"
           class="inline-flex items-center gap-1.5 h-10 px-4 rounded-xl border border-emerald-200 bg-white hover:bg-emerald-50 transition text-sm text-emerald-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
            </svg>
            {{ __('core.view_marketplace') }}
        </a>
        <a href="{{ route('core.listings.create') }}"
           class="inline-flex items-center gap-2 h-10 px-4 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 transition shadow-sm text-sm font-medium">
            <span class="text-lg leading-none">+</span>
            {{ __('core.new_listing') }}
        </a>
    </div>
</div>

{{-- Payment info notice --}}
@if(!$hasPaymentInfo)
<div class="mb-4 flex items-start gap-3 rounded-xl px-4 py-3" style="background:#fefce8;border:1px solid #fde047;">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0 mt-0.5" style="color:#ca8a04;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
    </svg>
    <div class="flex-1">
        <span class="text-sm font-semibold" style="color:#854d0e;">{{ __('core.payment_info_missing') }}</span>
        <p class="text-xs mt-0.5" style="color:#78350f;">
            {{ __('core.payment_info_missing_desc') }}
            <a href="{{ route('profile.edit') }}" class="underline font-medium" style="color:#92400e;">{{ __('core.add_payment_info_link') }}</a>
        </p>
    </div>
</div>
@endif

{{-- Filter tabs --}}
<div class="flex items-center gap-2 mb-4">
    <a href="{{ route('core.listings.index') }}"
       class="h-8 px-4 rounded-xl text-xs font-medium border transition
              {{ !request('type') ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white border-slate-200 text-slate-600 hover:bg-emerald-50' }}">
        {{ __('core.filter_all') }}
    </a>
    <a href="{{ route('core.listings.index', ['type' => 'instock']) }}"
       class="h-8 px-4 rounded-xl text-xs font-medium border transition
              {{ request('type') === 'instock' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white border-slate-200 text-slate-600 hover:bg-emerald-50' }}">
        {{ __('core.filter_instock') }}
    </a>
    <a href="{{ route('core.listings.index', ['type' => 'preorder']) }}"
       class="h-8 px-4 rounded-xl text-xs font-medium border transition
              {{ request('type') === 'preorder' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white border-slate-200 text-slate-600 hover:bg-emerald-50' }}">
        {{ __('core.filter_preorder') }}
    </a>
</div>

<div class="rounded-2xl bg-white border border-emerald-100 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-emerald-100 flex items-center justify-between">
        <div class="font-semibold text-sm text-slate-900">{{ __('core.marketplace_listings') }}</div>
        <div class="text-xs text-slate-500">
            {{ $listings->total() }} listing{{ $listings->total() !== 1 ? 's' : '' }}
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-500">
                <tr>
                    <th class="text-left font-medium px-5 py-3">{{ __('core.col_product') }}</th>
                    <th class="text-left font-medium px-5 py-3">{{ __('core.col_type') }}</th>
                    <th class="text-left font-medium px-5 py-3">{{ __('core.col_price') }}</th>
                    <th class="text-left font-medium px-5 py-3">{{ __('core.col_qty_harvest') }}</th>
                    <th class="text-center font-medium px-5 py-3">{{ __('core.col_marketplace') }}</th>
                    <th class="text-right font-medium px-5 py-3">{{ __('core.col_actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-emerald-50">
            @forelse($listings as $listing)
                <tr class="hover:bg-emerald-50/30 transition">
                    <td class="px-5 py-3 font-medium text-slate-900">
                        {{ $listing->product?->default_name ?? ('#'.$listing->id) }}
                        @if($listing->product?->category)
                            <div class="text-xs text-slate-400 font-normal">{{ $listing->product->category->name }}</div>
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $listing->type === 'instock' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                            {{ $listing->type === 'instock' ? __('core.type_instock') : __('core.type_preorder') }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-slate-700 font-medium">
                        €{{ $listing->price ? number_format($listing->price, 2) : '—' }}
                    </td>
                    <td class="px-5 py-3 text-slate-600 text-xs">
                        @if($listing->type === 'instock')
                            {{ $listing->available_qty ?? '—' }} {{ $listing->product?->unit ?? '' }}
                        @else
                            {{ optional($listing->expected_harvest_at)->format('d M Y') ?? '—' }}
                        @endif
                    </td>

                    {{-- Active/Inactive toggle --}}
                    <td class="px-5 py-3 text-center">
                        <form method="POST" action="{{ route('core.listings.toggle', $listing) }}">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 h-7 px-3 rounded-full text-xs font-medium border transition
                                           {{ $listing->is_active
                                               ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-red-50 hover:text-red-600 hover:border-red-200'
                                               : 'bg-slate-50 text-slate-500 border-slate-200 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200' }}"
                                    title="{{ $listing->is_active ? __('core.listing_click_to_hide') : __('core.listing_click_to_publish') }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $listing->is_active ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                                {{ $listing->is_active ? __('core.listing_live') : __('core.listing_hidden') }}
                            </button>
                        </form>
                    </td>

                    <td class="px-5 py-3 text-right">
                        <div class="flex items-center justify-end gap-1.5">
                            @if($listing->is_active)
                                <a href="{{ route('public.marketplace.show', $listing) }}" target="_blank"
                                   class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-slate-200 bg-white hover:bg-emerald-50 text-slate-400 hover:text-emerald-700 transition"
                                   title="{{ __('core.view_on_marketplace') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </a>
                            @endif
                            <a href="{{ route('core.listings.edit', $listing) }}"
                               class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-slate-200 bg-white hover:bg-emerald-50 text-slate-400 hover:text-emerald-700 transition"
                               title="{{ __('core.edit_listing') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </a>
                            <form method="POST" action="{{ route('core.listings.destroy', $listing) }}"
                                  onsubmit="return confirm('{{ __('core.confirm_delete_listing', ['name' => addslashes($listing->product?->default_name ?? 'this product')]) }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center justify-center h-8 w-8 rounded-lg border border-slate-200 bg-white hover:bg-red-50 hover:border-red-200 text-slate-400 hover:text-red-600 transition"
                                        title="{{ __('core.delete_listing') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-5 py-10 text-center text-slate-400">
                        <div class="text-sm font-medium text-slate-500 mb-1">{{ __('core.no_listings_title') }}</div>
                        <div class="text-xs">{{ __('core.no_listings_desc') }}</div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if($listings->hasPages())
        <div class="px-5 py-4 border-t border-emerald-50">
            {{ $listings->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
