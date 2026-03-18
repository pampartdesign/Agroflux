@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-semibold text-slate-900">Add Condition</h1>
        <p class="text-sm text-slate-500 mt-1">Define a new sensor rule with triggers, override conditions, and retry logic.</p>
    </div>
    <a href="{{ route('plus.iot.rules.index') }}"
       class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 transition">
        ← Back to Rules
    </a>
</div>

@if($errors->any())
    <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3">
        <p class="text-sm font-medium text-red-700 mb-1">Please fix the following errors:</p>
        <ul class="list-disc list-inside text-xs text-red-600 space-y-0.5">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

@include('plus.iot.rules._form', [
    'action' => route('plus.iot.rules.store'),
    'method' => 'POST',
])

@endsection
