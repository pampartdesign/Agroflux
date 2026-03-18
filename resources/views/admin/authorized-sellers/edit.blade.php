@extends('layouts.app')
@section('content')

<div class="flex items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-semibold text-slate-900">Edit: {{ $seller->company_name }}</h1>
        <p class="text-sm text-slate-500 mt-1">
            <a href="{{ route('admin.authorized-sellers.index') }}" class="hover:underline" style="color:#047857;">← Back to list</a>
        </p>
    </div>
</div>

@include('admin.authorized-sellers._form', [
    'seller' => $seller,
    'action' => route('admin.authorized-sellers.update', $seller),
    'method' => 'PUT',
])

@endsection
