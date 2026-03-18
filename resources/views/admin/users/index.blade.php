@extends('layouts.app')

@section('content')
<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-2xl font-semibold">Users</h1>
    <p class="text-sm text-slate-600 mt-1">Super Admin: create users, assign them to organizations, and set roles.</p>
  </div>
  <a href="{{ route('admin.users.create') }}" class="inline-flex items-center h-10 px-4 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700">
    + New User
  </a>
</div>

<div class="rounded-2xl border border-emerald-100 bg-white overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-emerald-50">
      <tr class="text-left">
        <th class="p-3">Name</th>
        <th class="p-3">Email</th>
        <th class="p-3">Locale</th>
        <th class="p-3">Super Admin</th>
        <th class="p-3 w-32"></th>
      </tr>
    </thead>
    <tbody>
      @forelse($users as $u)
        <tr class="border-t border-emerald-100">
          <td class="p-3 font-medium">{{ $u->name }}</td>
          <td class="p-3">{{ $u->email }}</td>
          <td class="p-3">{{ strtoupper($u->locale ?? 'EN') }}</td>
          <td class="p-3">{{ $u->is_super_admin ? 'Yes' : 'No' }}</td>
          <td class="p-3 text-right">
            <a class="text-emerald-700 hover:underline" href="{{ route('admin.users.edit', $u) }}">Edit</a>
          </td>
        </tr>
      @empty
        <tr><td class="p-6 text-slate-600" colspan="5">No users found.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-6">
  {{ $users->links() }}
</div>
@endsection
