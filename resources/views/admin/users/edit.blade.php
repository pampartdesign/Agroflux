@extends('layouts.app')

@section('content')
<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-2xl font-semibold">Edit User</h1>
    <p class="text-sm text-slate-600 mt-1">{{ $user->email }}</p>
  </div>
  <a class="text-sm text-slate-600 hover:underline" href="{{ route('admin.users.index') }}">← Back</a>
</div>

@if(session('status'))
  <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

  {{-- ── Account Details ─────────────────────────────────────────────── --}}
  <div class="rounded-2xl border border-emerald-100 bg-white p-6">
    <div class="font-semibold mb-4">Account Details</div>
    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5">
      @csrf
      @method('PUT')

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="text-sm font-medium">Name</label>
          <input name="name" value="{{ old('name',$user->name) }}" class="mt-1 w-full rounded-xl border-emerald-200" required>
          @error('name') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
        </div>
        <div>
          <label class="text-sm font-medium">Email</label>
          <input name="email" type="email" value="{{ old('email',$user->email) }}" class="mt-1 w-full rounded-xl border-emerald-200" required>
          @error('email') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
        </div>
        <div>
          <label class="text-sm font-medium">New Password <span class="text-slate-400">(optional)</span></label>
          <input name="password" type="password" class="mt-1 w-full rounded-xl border-emerald-200">
          @error('password') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
        </div>
        <div>
          <label class="text-sm font-medium">Locale</label>
          <select name="locale" class="mt-1 w-full rounded-xl border-emerald-200">
            @foreach(config('agroflux.locales') as $code => $meta)
              <option value="{{ $code }}" @selected(old('locale',$user->locale ?? 'en')===$code)>{{ $meta['flag'] }} {{ $meta['label'] }}</option>
            @endforeach
          </select>
        </div>
      </div>

      {{-- User type --}}
      <div class="border-t border-slate-100 pt-4">
        <div class="text-sm font-semibold mb-2">User Type</div>
        <div class="flex flex-wrap items-center gap-6">
          <label class="flex items-center gap-2 cursor-pointer select-none">
            <input type="radio" name="user_type" value="farmer"
                   class="accent-emerald-600"
                   @checked(old('user_type', $user->user_type ?? 'farmer')==='farmer')>
            <span class="text-sm font-medium">🌾 Farmer</span>
            <span class="text-xs text-slate-400">farm organisation</span>
          </label>
          <label class="flex items-center gap-2 cursor-pointer select-none">
            <input type="radio" name="user_type" value="trucker"
                   class="accent-emerald-600"
                   @checked(old('user_type', $user->user_type ?? 'farmer')==='trucker')>
            <span class="text-sm font-medium">🚛 Trucker</span>
            <span class="text-xs text-slate-400">independent driver</span>
          </label>
        </div>
      </div>

      <div class="flex items-center gap-2 pt-1">
        <input id="is_super_admin" name="is_super_admin" value="1" type="checkbox"
               class="rounded border-emerald-300" @checked(old('is_super_admin',$user->is_super_admin))>
        <label for="is_super_admin" class="text-sm font-medium">Super Admin</label>
        <span class="text-xs text-slate-500">(bypasses all plan and module restrictions)</span>
      </div>

      <div class="pt-2">
        <button class="inline-flex items-center h-10 px-5 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700">
          Save Account
        </button>
      </div>
    </form>
  </div>

  {{-- ── Organization Membership & Privileges ──────────────────────── --}}
  <div class="rounded-2xl border border-emerald-100 bg-white p-6">
    <div class="font-semibold mb-4">Organization Membership &amp; Privileges</div>

    <form method="POST" action="{{ route('admin.users.assignTenant', $user) }}" class="space-y-4">
      @csrf

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="text-sm font-medium">Organization</label>
          <div class="flex items-center gap-2 mt-1">
            <select id="editTenantSelect" name="tenant_id" class="flex-1 rounded-xl border-emerald-200 text-sm" required
                    onchange="if(this.value) window.location.href='{{ route('admin.users.edit', $user) }}?tenant_id='+this.value">
              <option value="">— Select —</option>
              @foreach($tenants as $t)
                <option value="{{ $t->id }}" @selected((string)$selectedTenantId===(string)$t->id)>{{ $t->name }}</option>
              @endforeach
            </select>
            <button type="button" id="btnEditNewOrg"
                    title="Create new organisation"
                    class="shrink-0 h-9 px-3 rounded-xl border border-emerald-300 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition text-sm font-bold">
              + New
            </button>
          </div>

          {{-- Inline new-org panel --}}
          <div id="editNewOrgPanel" class="hidden rounded-xl border border-emerald-200 bg-emerald-50 p-4 mt-3">
            <div class="text-sm font-semibold text-emerald-800 mb-3">New Organisation</div>
            <div class="flex items-center gap-3">
              <input id="editNewOrgName" type="text" placeholder="Organisation name…"
                     class="flex-1 rounded-xl border-emerald-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
              <button type="button" id="btnEditCreateOrg"
                      class="h-9 px-4 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition">
                Create
              </button>
              <button type="button" id="btnEditCancelOrg"
                      class="h-9 px-3 rounded-xl border border-slate-200 text-slate-600 text-sm hover:bg-slate-50 transition">
                Cancel
              </button>
            </div>
            <div id="editNewOrgError" class="hidden text-xs text-red-600 mt-2"></div>
          </div>
        </div>
        <div>
          <label class="text-sm font-medium">Role</label>
          <select name="role" class="mt-1 w-full rounded-xl border-emerald-200">
            <option value="member" @selected(($membership?->role ?? 'member')==='member')>Member</option>
            <option value="admin" @selected(($membership?->role ?? '')==='admin')>Admin</option>
          </select>
        </div>
      </div>

      {{-- Privilege control --}}
      <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
        <div class="flex items-center gap-3 mb-3">
          <input type="checkbox" name="restrict" id="restrictToggle" value="1"
                 class="rounded border-slate-300"
                 @checked(!is_null($membership?->permissions))
                 onchange="document.getElementById('privilegeGrid').classList.toggle('hidden',!this.checked)">
          <label for="restrictToggle" class="text-sm font-semibold">
            Restrict module access for this member
          </label>
        </div>
        <p class="text-xs text-slate-500 mb-3">
          When unchecked, the member inherits all modules the organization's plan allows.
          Enable this to hand-pick which modules this specific member can access.
        </p>
        <div id="privilegeGrid" class="{{ is_null($membership?->permissions) ? 'hidden' : '' }} grid grid-cols-1 sm:grid-cols-2 gap-2">
          @foreach($allModules as $key => $label)
            @php $isChecked = $membership?->permissions !== null && in_array($key, $membership->permissions, true); @endphp
            <label class="flex items-center gap-2 text-sm cursor-pointer select-none">
              <input type="checkbox" name="permissions[]" value="{{ $key }}"
                     class="rounded border-slate-300" @checked($isChecked)>
              {{ $label }}
            </label>
          @endforeach
        </div>
      </div>

      <button class="inline-flex items-center h-10 px-5 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700">
        Save Membership
      </button>
    </form>

    {{-- Current memberships list --}}
    <div class="mt-6">
      <div class="text-sm font-semibold mb-2">Current Memberships</div>
      <div class="space-y-2 text-sm">
        @php $memberships = $user->tenantMemberships()->with('tenant')->get(); @endphp
        @forelse($memberships as $m)
          <div class="flex items-start justify-between p-3 rounded-xl border border-emerald-100 bg-emerald-50/40">
            <div>
              <div class="font-medium">{{ $m->tenant->name ?? 'Org #'.$m->tenant_id }}</div>
              <div class="text-xs text-slate-600 mt-0.5">
                Role: <span class="font-semibold">{{ $m->role }}</span>
              </div>
              @if($m->permissions !== null)
                <div class="text-xs text-slate-500 mt-0.5">
                  Modules: {{ implode(', ', $m->permissions) ?: 'none' }}
                </div>
              @else
                <div class="text-xs text-slate-400 mt-0.5">Inherits plan modules</div>
              @endif
            </div>
            <div class="flex items-center gap-3 ml-3 flex-shrink-0">
              <a href="{{ route('admin.users.edit', [$user, 'tenant_id' => $m->tenant_id]) }}"
                 class="text-xs text-emerald-700 hover:underline">Edit privileges</a>
              <form method="POST" action="{{ route('admin.users.removeTenant', [$user, $m->tenant_id]) }}">
                @csrf
                @method('DELETE')
                <button class="text-xs text-red-600 hover:underline">Remove</button>
              </form>
            </div>
          </div>
        @empty
          <div class="text-slate-500 text-sm">No organization memberships yet.</div>
        @endforelse
      </div>
    </div>
  </div>

</div>

<script>
(function () {
    const btnEditNewOrg    = document.getElementById('btnEditNewOrg');
    const editNewOrgPanel  = document.getElementById('editNewOrgPanel');
    const btnEditCreateOrg = document.getElementById('btnEditCreateOrg');
    const btnEditCancelOrg = document.getElementById('btnEditCancelOrg');
    const editNewOrgName   = document.getElementById('editNewOrgName');
    const editNewOrgError  = document.getElementById('editNewOrgError');
    const editTenantSelect = document.getElementById('editTenantSelect');

    if (!btnEditNewOrg) return;

    btnEditNewOrg.addEventListener('click', () => {
        editNewOrgPanel.classList.remove('hidden');
        editNewOrgName.focus();
    });
    btnEditCancelOrg.addEventListener('click', () => {
        editNewOrgPanel.classList.add('hidden');
        editNewOrgName.value = '';
        editNewOrgError.classList.add('hidden');
    });

    async function doCreate() {
        const name = editNewOrgName.value.trim();
        if (!name) { showErr('Organisation name is required.'); return; }

        btnEditCreateOrg.disabled = true;
        btnEditCreateOrg.textContent = 'Creating…';
        editNewOrgError.classList.add('hidden');

        try {
            const res  = await fetch('{{ route('admin.orgs.quick') }}', {
                method:  'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body:    JSON.stringify({ name }),
            });
            const json = await res.json();

            if (!res.ok) {
                showErr(json?.errors?.name?.[0] ?? json?.message ?? 'Could not create organisation.');
                return;
            }

            // Remove onchange redirect temporarily, add option and select it
            const old = editTenantSelect.onchange;
            editTenantSelect.onchange = null;
            editTenantSelect.add(new Option(json.name, json.id, true, true));
            editTenantSelect.onchange = old;

            editNewOrgPanel.classList.add('hidden');
            editNewOrgName.value = '';
        } catch {
            showErr('Network error — please try again.');
        } finally {
            btnEditCreateOrg.disabled = false;
            btnEditCreateOrg.textContent = 'Create';
        }
    }

    btnEditCreateOrg.addEventListener('click', doCreate);
    editNewOrgName.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); doCreate(); } });

    function showErr(msg) {
        editNewOrgError.textContent = msg;
        editNewOrgError.classList.remove('hidden');
    }
})();
</script>
@endsection