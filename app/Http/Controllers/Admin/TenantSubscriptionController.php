<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantSubscriptionController extends Controller
{
    public function index()
    {
        $tenants = Tenant::with(['activeSubscription.plan'])
            ->orderBy('name')
            ->get();

        return view('admin.subscriptions.index', compact('tenants'));
    }

    public function edit(Tenant $tenant)
    {
        $tenant->load('activeSubscription.plan');
        $plans = Plan::where('is_active', true)->orderBy('name')->get();

        return view('admin.subscriptions.edit', compact('tenant', 'plans'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $data = $request->validate([
            'plan_id'    => ['required', 'integer', 'exists:plans,id'],
            'status'     => ['required', 'in:active,canceled,past_due'],
            'starts_at'  => ['nullable', 'date'],
            'ends_at'    => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);

        $plan = Plan::findOrFail($data['plan_id']);

        // Cancel any other active subscriptions for this tenant
        Subscription::withoutGlobalScopes()
            ->where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->update(['status' => 'canceled']);

        // Create new subscription
        Subscription::withoutGlobalScopes()->create([
            'tenant_id'          => $tenant->id,
            'plan_id'            => $plan->id,
            'status'             => $data['status'],
            'starts_at'          => $data['starts_at'] ?? now(),
            'ends_at'            => $data['ends_at'] ?? null,
            'provider'           => 'manual',
            'provider_reference' => null,
        ]);

        // Sync plan_key on tenant for FeatureGate
        $tenant->update(['plan_key' => $plan->key]);

        return redirect()->route('admin.subscriptions.index')
            ->with('status', 'Subscription for "' . $tenant->name . '" assigned to plan "' . $plan->name . '".');
    }
}
