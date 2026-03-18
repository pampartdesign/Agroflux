@extends('layouts.app')
@section('content')
<x-page-header title="{{ __('app.org_members_title') }}" subtitle="{{ __('app.org_members_subtitle') }}" />
<div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-6">
  <div class="text-sm text-slate-600">{{ __('app.members_page_wired') }}</div>
</div>
@endsection
