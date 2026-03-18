<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\App\InviteTenantMemberRequest;
use App\Http\Requests\App\UpdateTenantMemberRoleRequest;
use App\Models\TenantUser;
use App\Services\CurrentTenant;
use App\Services\TenantMembership;
use Illuminate\Http\Request;

class TenantMemberController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'tenant.selected', 'tenant.active', 'tenant.member', 'tenant.role:admin']);
    }

    public function index(CurrentTenant $currentTenant, TenantMembership $membership)
    {
        $tenant = $currentTenant->model();
        $members = $membership->listMembers($tenant);

        return view('app.members.index', [
            'tenant' => $tenant,
            'members' => $members,
            'roles' => TenantMembership::allowedRoles(),
        ]);
    }

    public function store(InviteTenantMemberRequest $request, CurrentTenant $currentTenant, TenantMembership $membership)
    {
        $tenant = $currentTenant->model();
        $membership->addMemberByEmail($tenant, $request->string('email'), $request->string('role'));

        return redirect()->route('members.index')->with('status', 'Member added/updated.');
    }

    public function update(UpdateTenantMemberRoleRequest $request, TenantUser $member, CurrentTenant $currentTenant, TenantMembership $membership)
    {
        $tenant = $currentTenant->model();

        if ((int) $member->tenant_id !== (int) $tenant->id) {
            abort(404);
        }

        $membership->updateRole($member, $request->string('role'));

        return redirect()->route('members.index')->with('status', 'Role updated.');
    }

    public function destroy(Request $request, TenantUser $member, CurrentTenant $currentTenant, TenantMembership $membership)
    {
        $tenant = $currentTenant->model();

        if ((int) $member->tenant_id !== (int) $tenant->id) {
            abort(404);
        }

        if ((int) $member->user_id === (int) $request->user()->id) {
            return redirect()->route('members.index')->with('error', 'You cannot remove yourself.');
        }

        $membership->removeMember($member);

        return redirect()->route('members.index')->with('status', 'Member removed.');
    }
}
