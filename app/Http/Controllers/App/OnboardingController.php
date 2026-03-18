<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTenantRequest;
use App\Models\Tenant;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function show(Request $request)
    {
        return view('app.onboarding.create-tenant');
    }

    public function store(CreateTenantRequest $request)
    {
        $user = $request->user();

        $tenant = Tenant::query()->create([
            'name' => $request->string('name')->toString(),
            'trial_ends_at' => now()->addDays((int) config('agroflux.trial_days', 14)),
        ]);

        $tenant->users()->attach($user->id);

        session(['tenant_id' => $tenant->id]);

        return redirect()->route('dashboard');
    }
}
