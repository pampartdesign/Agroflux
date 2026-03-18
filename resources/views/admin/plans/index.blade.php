@extends('admin._layout')

@section('page_title', 'Subscription Plans')
@section('page_subtitle', 'Create and manage the subscription packages available to tenants.')

@section('page_actions')
    <a href="{{ route('admin.plans.create') }}"
       class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-4 py-2 text-white text-sm font-medium hover:bg-emerald-700 transition">
        + New Plan
    </a>
@endsection

@section('page_content')

@php
$moduleLabels = [
    'core'         => 'Core',
    'farm'         => 'Farm',
    'livestock'    => 'Livestock',
    'water'        => 'Water',
    'traceability' => 'Traceability',
    'inventory'    => 'Inventory',
    'equipment'    => 'Equipment',
    'iot_sim'      => 'IoT Simulator',
    'iot'          => 'IoT Sensors',
    'logi'         => 'Logistics',
    'drone'        => 'Drones & Field Mapping',
];
@endphp

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Plan</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Price</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Modules</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Subscribers</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($plans as $plan)
                <tr class="border-t border-gray-100 hover:bg-slate-50 transition">
                    <td class="px-5 py-4">
                        <div class="font-semibold text-gray-900">{{ $plan->name }}</div>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="inline-block px-2 py-0.5 rounded-full text-xs font-mono font-semibold"
                                  style="background:#f0fdf4; color:#166534; border:1px solid #bbf7d0;">
                                {{ $plan->key }}
                            </span>
                            @if($plan->billing_cycle)
                                <span class="text-xs text-gray-400">{{ ucfirst($plan->billing_cycle) }}</span>
                            @endif
                        </div>
                        @if($plan->description)
                            <div class="text-xs text-gray-400 mt-1 max-w-xs truncate">{{ $plan->description }}</div>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        @if($plan->price !== null)
                            <span class="font-semibold text-gray-900">€{{ number_format((float)$plan->price, 2) }}</span>
                            @if($plan->billing_cycle)
                                <span class="text-xs text-gray-400">/{{ $plan->billing_cycle === 'monthly' ? 'mo' : ($plan->billing_cycle === 'yearly' ? 'yr' : 'custom') }}</span>
                            @endif
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        @php $modules = $plan->modules ?? []; @endphp
                        @if(count($modules) > 0)
                            <div class="flex flex-wrap gap-1">
                                @foreach(array_slice($modules, 0, 5) as $m)
                                    <span class="inline-block px-2 py-0.5 rounded-full text-xs"
                                          style="background:#f0f9ff; color:#0369a1; border:1px solid #bae6fd;">
                                        {{ $moduleLabels[$m] ?? $m }}
                                    </span>
                                @endforeach
                                @if(count($modules) > 5)
                                    <span class="text-xs text-gray-400">+{{ count($modules) - 5 }} more</span>
                                @endif
                            </div>
                        @else
                            <span class="text-xs text-gray-400 italic">No modules set</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <span class="font-semibold text-gray-700">{{ $plan->subscriptions_count }}</span>
                        <span class="text-xs text-gray-400 ml-1">active</span>
                    </td>
                    <td class="px-5 py-4">
                        @if($plan->is_active)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold"
                                  style="background:#f0fdf4; color:#166534; border:1px solid #bbf7d0;">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold"
                                  style="background:#fef2f2; color:#991b1b; border:1px solid #fecaca;">
                                <span class="h-1.5 w-1.5 rounded-full bg-red-400"></span>
                                Inactive
                            </span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.plans.edit', $plan) }}"
                               class="text-xs text-gray-600 hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.plans.toggle', $plan) }}" class="inline">
                                @csrf
                                <button type="submit"
                                        class="text-xs hover:underline {{ $plan->is_active ? 'text-amber-600' : 'text-emerald-600' }}">
                                    {{ $plan->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-5 py-14 text-center">
                        <div class="text-gray-400 text-sm">No subscription plans yet.</div>
                        <a href="{{ route('admin.plans.create') }}"
                           class="mt-3 inline-flex items-center rounded-lg bg-emerald-600 px-4 py-2 text-white text-sm hover:bg-emerald-700 transition">
                            Create your first plan
                        </a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
