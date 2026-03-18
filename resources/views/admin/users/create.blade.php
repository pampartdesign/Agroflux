@extends('layouts.app')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-semibold">Create User</h1>
        <p class="text-sm text-slate-500 mt-1">Add a new farmer or trucker account.</p>
    </div>
    <a class="text-sm text-slate-600 hover:underline" href="{{ route('admin.users.index') }}">← Back</a>
</div>

<div class="max-w-3xl rounded-2xl border border-emerald-100 bg-white p-6">
    <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
        @csrf

        {{-- Basic fields --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-medium">Name</label>
                <input name="name" value="{{ old('name') }}"
                       class="mt-1 w-full rounded-xl border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500" required>
                @error('name') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="text-sm font-medium">Email</label>
                <input name="email" type="email" value="{{ old('email') }}"
                       class="mt-1 w-full rounded-xl border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500" required>
                @error('email') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="text-sm font-medium">Password</label>
                <input name="password" type="password"
                       class="mt-1 w-full rounded-xl border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500" required>
                @error('password') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="text-sm font-medium">Locale</label>
                <select name="locale" class="mt-1 w-full rounded-xl border-emerald-200">
                    @foreach(config('agroflux.locales') as $code => $meta)
                        <option value="{{ $code }}" @selected(old('locale','en')===$code)>{{ $meta['flag'] }} {{ $meta['label'] }}</option>
                    @endforeach
                </select>
                @error('locale') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
            </div>
        </div>

        {{-- User type --}}
        <div class="border-t border-slate-100 pt-5">
            <div class="text-sm font-semibold mb-3">User Type</div>
            <div class="flex flex-wrap items-center gap-6">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="radio" name="user_type" value="farmer" id="ut_farmer"
                           class="accent-emerald-600" @checked(old('user_type','farmer')==='farmer')>
                    <span class="text-sm font-medium">🌾 Farmer</span>
                    <span class="text-xs text-slate-400">belongs to a farm organisation</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="radio" name="user_type" value="trucker" id="ut_trucker"
                           class="accent-emerald-600" @checked(old('user_type')==='trucker')>
                    <span class="text-sm font-medium">🚛 Trucker</span>
                    <span class="text-xs text-slate-400">independent driver, no org required</span>
                </label>
            </div>
        </div>

        {{-- Flags --}}
        <div class="flex items-center gap-2">
            <input id="is_super_admin" name="is_super_admin" value="1" type="checkbox"
                   class="rounded border-emerald-300" @checked(old('is_super_admin'))>
            <label for="is_super_admin" class="text-sm">Super Admin</label>
        </div>

        {{-- Organisation assignment (hidden for truckers) --}}
        <div id="orgSection" class="border-t border-emerald-100 pt-5 space-y-4">
            <div class="text-sm font-semibold text-slate-700">Optional: Assign to an Organisation</div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium">Organisation</label>
                    <div class="flex items-center gap-2 mt-1">
                        <select id="tenantSelect" name="tenant_id"
                                class="flex-1 rounded-xl border-emerald-200 text-sm">
                            <option value="">— Not assigned —</option>
                            @foreach($tenants as $t)
                                <option value="{{ $t->id }}" @selected(old('tenant_id')==$t->id)>{{ $t->name }}</option>
                            @endforeach
                        </select>
                        <button type="button" id="btnNewOrg"
                                title="Create new organisation"
                                class="shrink-0 h-9 px-3 rounded-xl border border-emerald-300 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition text-sm font-bold">
                            + New
                        </button>
                    </div>
                    @error('tenant_id') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium">Role in org</label>
                    <select name="tenant_role" class="mt-1 w-full rounded-xl border-emerald-200">
                        <option value="member" @selected(old('tenant_role','member')==='member')>Member</option>
                        <option value="admin"  @selected(old('tenant_role')==='admin')>Admin</option>
                    </select>
                    @error('tenant_role') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- Inline new-org panel --}}
            <div id="newOrgPanel" class="hidden rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                <div class="text-sm font-semibold text-emerald-800 mb-3">New Organisation</div>
                <div class="flex items-center gap-3">
                    <input id="newOrgName" type="text" placeholder="Organisation name…"
                           class="flex-1 rounded-xl border-emerald-300 text-sm
                                  focus:border-emerald-500 focus:ring-emerald-500">
                    <button type="button" id="btnCreateOrg"
                            class="h-9 px-4 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition">
                        Create
                    </button>
                    <button type="button" id="btnCancelOrg"
                            class="h-9 px-3 rounded-xl border border-slate-200 text-slate-600 text-sm hover:bg-slate-50 transition">
                        Cancel
                    </button>
                </div>
                <div id="newOrgError" class="hidden text-xs text-red-600 mt-2"></div>
            </div>
        </div>

        <div class="pt-2 flex items-center gap-3">
            <button class="inline-flex items-center h-10 px-5 rounded-xl bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700">
                Create User
            </button>
            <a class="text-sm text-slate-600 hover:underline" href="{{ route('admin.users.index') }}">Cancel</a>
        </div>

    </form>
</div>

<script>
(function () {
    const orgSection   = document.getElementById('orgSection');
    const newOrgPanel  = document.getElementById('newOrgPanel');
    const btnNewOrg    = document.getElementById('btnNewOrg');
    const btnCreateOrg = document.getElementById('btnCreateOrg');
    const btnCancelOrg = document.getElementById('btnCancelOrg');
    const newOrgName   = document.getElementById('newOrgName');
    const newOrgError  = document.getElementById('newOrgError');
    const tenantSelect = document.getElementById('tenantSelect');

    // Show/hide org section based on user type
    function syncOrgSection() {
        const type = document.querySelector('input[name="user_type"]:checked')?.value;
        orgSection.style.display = (type === 'trucker') ? 'none' : '';
    }
    document.querySelectorAll('input[name="user_type"]').forEach(r => r.addEventListener('change', syncOrgSection));
    syncOrgSection();

    // Toggle inline panel
    btnNewOrg.addEventListener('click', () => {
        newOrgPanel.classList.remove('hidden');
        newOrgName.focus();
    });
    btnCancelOrg.addEventListener('click', () => {
        newOrgPanel.classList.add('hidden');
        newOrgName.value = '';
        newOrgError.classList.add('hidden');
    });

    // Create org via fetch
    async function doCreate() {
        const name = newOrgName.value.trim();
        if (!name) { showErr('Organisation name is required.'); return; }

        btnCreateOrg.disabled = true;
        btnCreateOrg.textContent = 'Creating…';
        newOrgError.classList.add('hidden');

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

            // Inject new <option> and select it
            tenantSelect.add(new Option(json.name, json.id, true, true));
            newOrgPanel.classList.add('hidden');
            newOrgName.value = '';
        } catch {
            showErr('Network error — please try again.');
        } finally {
            btnCreateOrg.disabled = false;
            btnCreateOrg.textContent = 'Create';
        }
    }

    btnCreateOrg.addEventListener('click', doCreate);
    newOrgName.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); doCreate(); } });

    function showErr(msg) {
        newOrgError.textContent = msg;
        newOrgError.classList.remove('hidden');
    }
})();
</script>
@endsection
