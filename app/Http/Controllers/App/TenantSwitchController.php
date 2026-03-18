<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantSwitchController extends Controller
{
    public function switch(Request $request)
    {
        $data = $request->validate([
            'tenant_id' => ['required', 'integer', 'exists:tenants,id'],
        ]);

        $tenant = Tenant::query()->findOrFail($data['tenant_id']);

        // Store selected tenant in session (middleware reads this)
        session(['tenant_id' => $tenant->id]);

        return redirect()->route('dashboard');
    }
}
