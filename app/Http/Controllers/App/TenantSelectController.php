<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\TenantUser;
use Illuminate\Http\Request;

class TenantSelectController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->is_super_admin) {
            $tenants = Tenant::query()->orderBy('name')->get();
        } else {
            $tenantIds = TenantUser::query()
                ->where('user_id', $user->id)
                ->pluck('tenant_id');

            $tenants = Tenant::query()
                ->whereIn('id', $tenantIds)
                ->orderBy('name')
                ->get();
        }

        return view('app.tenant-select', [
            'tenants' => $tenants,
            'selectedTenantId' => (int) session('tenant_id'),
        ]);
    }
}
