@extends('admin._layout')

@section('page_title', 'Assign Plan — ' . $tenant->name)
@section('page_subtitle', 'Set or change the subscription plan for this tenant.')

@section('page_actions')
    <a href="{{ route('admin.subscriptions.index') }}"
       class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-4 py-2 text-gray-600 text-sm hover:bg-gray-50 transition">
        ← Back
    </a>
@endsection

@section('page_content')

@php $current = $tenant->activeSubscription; @endphp

{{-- Current subscription info --}}
@if($current && $current->plan)
    <div class="mb-5 rounded-xl border border-emerald-100 bg-emerald-50 px-5 py-4 text-sm max-w-xl">
        <div class="text-xs font-semibold text-emerald-600 uppercase tracking-wide mb-1">Current Active Subscription</div>
        <div class="font-semibold text-slate-800">{{ $current->plan->name }}</div>
        <div class="text-xs text-slate-500 mt-0.5">
            Status: <strong>{{ ucfirst($current->status) }}</strong>
            @if($current->ends_at)
                · Expires {{ $current->ends_at->format('d M Y') }}
            @endif
        </div>
    </div>
@endif

<form method="POST" action="{{ route('admin.subscriptions.update', $tenant) }}" class="max-w-xl space-y-5">
    @csrf
    @method('PUT')

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 space-y-4">
        <div class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">New Subscription</div>

        {{-- Plan select --}}
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">
                Plan <span class="text-red-500">*</span>
            </label>
            <select name="plan_id" required
                    class="w-full h-10 px-3 rounded-lg border @error('plan_id') border-red-400 @else border-gray-200 @enderror bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                <option value="">— Select a plan —</option>
                @foreach($plans as $plan)
                    <option value="{{ $plan->id }}"
                            @selected(old('plan_id', $current?->plan_id) == $plan->id)>
                        {{ $plan->name }}
                        @if($plan->price !== null)
                            — €{{ number_format((float)$plan->price, 2) }}{{ $plan->billing_cycle ? '/'.substr($plan->billing_cycle, 0, 2) : '' }}
                        @endif
                    </option>
                @endforeach
            </select>
            @error('plan_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- Status --}}
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1.5">Status</label>
            <select name="status"
                    class="w-full h-10 px-3 rounded-lg border border-gray-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                <option value="active"    @selected(old('status', 'active') === 'active')>Active</option>
                <option value="past_due"  @selected(old('status') === 'past_due')>Past Due</option>
                <option value="canceled"  @selected(old('status') === 'canceled')>Canceled</option>
            </select>
        </div>

        {{-- Dates --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Starts At</label>
                <input name="starts_at" type="date"
                       value="{{ old('starts_at', now()->format('Y-m-d')) }}"
                       class="w-full h-9 px-3 rounded-lg border @error('starts_at') border-red-400 @else border-gray-200 @enderror bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                @error('starts_at')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">
                    Expires At
                    <span class="font-normal text-gray-400">(leave blank = no expiry)</span>
                </label>
                <input name="ends_at" type="date"
                       value="{{ old('ends_at', $current?->ends_at?->format('Y-m-d')) }}"
                       class="w-full h-9 px-3 rounded-lg border @error('ends_at') border-red-400 @else border-gray-200 @enderror bg-white text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200">
                @error('ends_at')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="rounded-lg bg-amber-50 border border-amber-100 px-3 py-2 text-xs text-amber-700">
            Saving will cancel all existing active subscriptions for this tenant and create a new one.
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit"
                class="inline-flex items-center gap-2 h-10 px-6 rounded-lg bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition shadow-sm">
            Assign Plan
        </button>
        <a href="{{ route('admin.subscriptions.index') }}"
           class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
    </div>
</form>

@endsection
