@extends('admin._layout')

@section('page_title', 'Tenant Subscriptions')
@section('page_subtitle', 'View and assign subscription plans to tenants.')

@section('page_content')

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Tenant</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Current Plan</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Starts</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Expires</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($tenants as $tenant)
                @php $sub = $tenant->activeSubscription; @endphp
                <tr class="border-t border-gray-100 hover:bg-slate-50 transition">
                    <td class="px-5 py-3">
                        <div class="font-semibold text-gray-900">{{ $tenant->name }}</div>
                        @if($tenant->trial_ends_at)
                            @php $trialExpired = $tenant->trial_ends_at->isPast(); @endphp
                            <div class="text-xs mt-0.5 {{ $trialExpired ? 'text-red-500' : 'text-amber-600' }}">
                                Trial {{ $trialExpired ? 'ended' : 'ends' }} {{ $tenant->trial_ends_at->format('d M Y') }}
                            </div>
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        @if($sub && $sub->plan)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold"
                                  style="background:#f0fdf4; color:#166534; border:1px solid #bbf7d0;">
                                {{ $sub->plan->name }}
                            </span>
                        @else
                            <span class="text-xs text-gray-400 italic">No subscription</span>
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        @if($sub)
                            @php
                                $statusColor = match($sub->status) {
                                    'active'   => ['bg:#f0fdf4', 'color:#166534', 'border:#bbf7d0'],
                                    'canceled' => ['bg:#fef2f2', 'color:#991b1b', 'border:#fecaca'],
                                    default    => ['bg:#fefce8', 'color:#854d0e', 'border:#fde68a'],
                                };
                            @endphp
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                  style="background:{{ str_replace('bg:', '', $statusColor[0]) }}; color:{{ str_replace('color:', '', $statusColor[1]) }}; border:1px solid {{ str_replace('border:', '', $statusColor[2]) }};">
                                {{ ucfirst($sub->status) }}
                            </span>
                        @else
                            <span class="text-gray-300">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-xs text-gray-600">
                        {{ $sub?->starts_at?->format('d M Y') ?? '—' }}
                    </td>
                    <td class="px-5 py-3">
                        @if($sub?->ends_at)
                            @php $expired = $sub->ends_at->isPast(); $soon = !$expired && $sub->ends_at->diffInDays(now()) <= 30; @endphp
                            <span class="text-xs font-medium {{ $expired ? 'text-red-600' : ($soon ? 'text-amber-600' : 'text-gray-600') }}">
                                {{ $sub->ends_at->format('d M Y') }}
                                @if($expired) <span class="text-red-400">(expired)</span>
                                @elseif($soon) <span class="text-amber-400">(soon)</span>
                                @endif
                            </span>
                        @else
                            <span class="text-gray-400 text-xs">No expiry</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-right">
                        <a href="{{ route('admin.subscriptions.edit', $tenant) }}"
                           class="text-xs text-emerald-600 font-medium hover:underline">
                            {{ $sub ? 'Change Plan' : 'Assign Plan' }}
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-5 py-14 text-center text-gray-400 text-sm">
                        No tenants found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
