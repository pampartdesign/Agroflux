@extends('admin._layout')

@section('page_title', 'Regions')
@section('page_subtitle', 'Platform-managed regions. Tenants can have farms in multiple regions.')

@section('page_actions')
  <a href="{{ route('admin.regions.create') }}" class="inline-flex items-center rounded-lg bg-emerald-600 px-4 py-2 text-white text-sm">+ Add Region</a>
@endsection

@section('page_content')
  <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <table class="w-full text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="text-left px-4 py-3">Name</th>
          <th class="text-left px-4 py-3">Code</th>
          <th class="text-left px-4 py-3">Active</th>
          <th class="text-right px-4 py-3">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($regions as $region)
          <tr class="border-t">
            <td class="px-4 py-3 font-medium">{{ $region->name }}</td>
            <td class="px-4 py-3 text-gray-600">{{ $region->code ?: '—' }}</td>
            <td class="px-4 py-3">
              <span class="inline-flex rounded-full px-2 py-1 text-xs {{ $region->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                {{ $region->is_active ? 'ACTIVE' : 'INACTIVE' }}
              </span>
            </td>
            <td class="px-4 py-3 text-right">
              <a href="{{ route('admin.regions.edit', $region) }}" class="underline text-gray-700">Edit</a>
              <form method="POST" action="{{ route('admin.regions.destroy', $region) }}" class="inline">
                @csrf @method('DELETE')
                <button class="underline text-red-600 ml-3" onclick="return confirm('Delete region?')">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="4" class="px-4 py-10 text-center text-gray-500">No regions yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    {{ $regions->links() }}
  </div>
@endsection
