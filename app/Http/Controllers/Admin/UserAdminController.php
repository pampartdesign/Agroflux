<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserAdminController extends Controller
{
    public function index()
    {
        $users = User::query()
            ->orderByDesc('id')
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $tenants = Tenant::query()->orderBy('name')->get();

        return view('admin.users.create', compact('tenants'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => ['required','string','max:150'],
            'email'          => ['required','email','max:190','unique:users,email'],
            'password'       => ['required','string','min:8'],
            'is_super_admin' => ['nullable','boolean'],
            'user_type'      => ['nullable', Rule::in(['farmer','trucker'])],
            'locale'         => ['required', Rule::in(array_keys(config('agroflux.locales')))],
            'tenant_id'      => ['nullable','integer','exists:tenants,id'],
            'tenant_role'    => ['nullable', Rule::in(['admin','member'])],
        ]);

        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'locale'    => $data['locale'] ?? 'en',
            'user_type' => $data['user_type'] ?? 'farmer',
        ]);

        $user->is_super_admin = (bool)($data['is_super_admin'] ?? false);
        $user->save();

        if (!empty($data['tenant_id'])) {
            $user->tenantMemberships()->updateOrCreate(
                ['tenant_id' => (int)$data['tenant_id']],
                ['role' => $data['tenant_role'] ?? 'member']
            );
        }

        return redirect()->route('admin.users.index')->with('status', 'User created.');
    }

    public function edit(User $user)
    {
        $tenants = Tenant::query()->orderBy('name')->get();
        $selectedTenantId = request('tenant_id');
        $membership = null;

        if ($selectedTenantId) {
            $membership = $user->tenantMemberships()->where('tenant_id', (int)$selectedTenantId)->first();
        }

        $allModules = \App\Models\TenantMember::allModules();

        return view('admin.users.edit', compact('user','tenants','membership','selectedTenantId','allModules'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'           => ['required','string','max:150'],
            'email'          => ['required','email','max:190', Rule::unique('users','email')->ignore($user->id)],
            'password'       => ['nullable','string','min:8'],
            'is_super_admin' => ['nullable','boolean'],
            'user_type'      => ['nullable', Rule::in(['farmer','trucker'])],
            'locale'         => ['required', Rule::in(array_keys(config('agroflux.locales')))],
        ]);

        $user->name          = $data['name'];
        $user->email         = $data['email'];
        $user->is_super_admin = (bool)($data['is_super_admin'] ?? false);
        $user->user_type     = $data['user_type'] ?? 'farmer';
        $user->locale        = $data['locale'];

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('status', 'User updated.');
    }

    /** POST /admin/orgs/quick — create an org inline; returns JSON {id, name} */
    public function quickCreateOrg(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:150','unique:tenants,name'],
        ]);

        $tenant = Tenant::create(['name' => $data['name']]);

        return response()->json(['id' => $tenant->id, 'name' => $tenant->name]);
    }

    public function assignTenant(Request $request, User $user)
    {
        $allModules = array_keys(\App\Models\TenantMember::allModules());

        $data = $request->validate([
            'tenant_id'     => ['required','integer','exists:tenants,id'],
            'role'          => ['required', Rule::in(['admin','member'])],
            'restrict'      => ['nullable','boolean'],
            'permissions'   => ['nullable','array'],
            'permissions.*' => ['string', Rule::in($allModules)],
        ]);

        // null = inherit from plan; explicit array = custom restriction
        $permissions = ($data['restrict'] ?? false)
            ? array_values($data['permissions'] ?? [])
            : null;

        $user->tenantMemberships()->updateOrCreate(
            ['tenant_id' => (int)$data['tenant_id']],
            ['role' => $data['role'], 'permissions' => $permissions]
        );

        return back()->with('status', 'Tenant membership saved.');
    }

    public function removeTenant(Request $request, User $user, Tenant $tenant)
    {
        $user->tenantMemberships()->where('tenant_id', $tenant->id)->delete();
        return back()->with('status', 'Tenant membership removed.');
    }
}
