@extends('layouts.app')

@section('content')
<x-page-header title="Select Organization" subtitle="Choose which organization you want to work in." />

<div class="max-w-xl">
  <form method="POST" action="{{ route('tenant.switch') }}" class="rounded-2xl bg-white border border-slate-100 shadow-sm p-6 space-y-4">
    @csrf
    <div>
      <label class="block text-sm font-medium mb-1" for="tenant_id">Organization</label>
      <select id="tenant_id" name="tenant_id" class="w-full rounded-xl border-slate-200 focus:border-emerald-400 focus:ring-emerald-200">
        @forelse($tenants as $t)
          <option value="{{ $t->id }}">{{ $t->name }}</option>
        @empty
          <option value="" disabled selected>No organizations available</option>
        @endforelse
      </select>
      <p class="mt-2 text-xs text-slate-500">If you don’t see your organization, create one from onboarding or ask an admin to invite you.</p>
    </div>

    <div class="flex items-center justify-between">
      <div class="flex items-center gap-2 text-sm">
        <a class="text-emerald-700 hover:underline" href="{{ url('locale/en') }}">English</a>
        <span class="text-slate-300">•</span>
        <a class="text-emerald-700 hover:underline" href="{{ url('locale/el') }}">Ελληνικά</a>
      </div>
      <button class="inline-flex items-center rounded-xl bg-emerald-600 px-4 py-2 text-white text-sm font-medium hover:bg-emerald-700 disabled:opacity-50"
              type="submit" @disabled($tenants->isEmpty())>
        Continue
      </button>
    </div>
  </form>
</div>
@endsection
