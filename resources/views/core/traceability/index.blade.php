@extends('layouts.app')

@section('content')
<div class="flex items-start justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-semibold">Traceability</h1>
        <p class="text-sm text-slate-600 mt-1">Create batches and record events. Public trace tokens can be shared via QR.</p>
    </div>
    <a href="{{ route('core.traceability.batch.create') }}" class="inline-flex items-center h-10 px-4 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 transition">
        + Create Batch
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    <div class="lg:col-span-2 rounded-2xl bg-white border border-emerald-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-emerald-100 flex items-center justify-between">
            <div class="font-semibold text-sm">Batches</div>
            <div class="text-xs text-slate-500">Organization: <span class="font-medium text-slate-700">{{ $tenant->name ?? '—' }}</span></div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="text-left font-medium px-5 py-3">Batch</th>
                    <th class="text-left font-medium px-5 py-3">Product</th>
                    <th class="text-left font-medium px-5 py-3">Status</th>
                    <th class="text-right font-medium px-5 py-3">Actions</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-emerald-50">
                @forelse($batches as $batch)
                    <tr class="hover:bg-emerald-50/40">
                        <td class="px-5 py-3 font-medium">{{ $batch->code ?? ('#'.$batch->id) }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ $batch->product?->default_name ?? $batch->product_name ?? '-' }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ ucfirst($batch->status ?? 'active') }}</td>
                        <td class="px-5 py-3 text-right">
                            <a class="text-emerald-700 hover:underline" href="{{ route('core.traceability.batch.show', $batch) }}">Open</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-10 text-center text-slate-500">
                            No batches yet.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="rounded-2xl bg-white border border-emerald-100 shadow-sm p-5">
        <div class="font-semibold">How it works</div>
        <div class="text-sm text-slate-600 mt-2">
            <div class="mb-2">1) Create a batch for a product.</div>
            <div class="mb-2">2) Add events (harvest, packing, shipping, etc.).</div>
            <div class="mb-2">3) Share the public token / QR with buyers.</div>
        </div>
        <div class="mt-4">
            <a class="text-emerald-700 hover:underline text-sm" href="{{ route('public.trace', 'DEMO') }}">View public trace demo</a>
        </div>
    </div>
</div>
@endsection
