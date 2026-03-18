<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Services\FeatureGate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubscriptionPlanController extends Controller
{
    public function index()
    {
        $plans = Plan::withCount(['subscriptions' => fn($q) => $q->where('status', 'active')])
            ->orderBy('name')
            ->get();

        return view('admin.plans.index', compact('plans'));
    }

    public function create()
    {
        $allModules = FeatureGate::ALL_MODULES;

        return view('admin.plans.create', compact('allModules'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'key'           => ['required', 'string', 'max:50', 'regex:/^[a-z0-9_]+$/', 'unique:plans,key'],
            'name'          => ['required', 'string', 'max:100'],
            'description'   => ['nullable', 'string', 'max:500'],
            'price'         => ['nullable', 'numeric', 'min:0'],
            'billing_cycle' => ['nullable', 'in:monthly,yearly,custom'],
            'modules'       => ['nullable', 'array'],
            'modules.*'     => ['string', 'in:' . implode(',', FeatureGate::ALL_MODULES)],
            'is_active'     => ['nullable', 'boolean'],
        ]);

        $data['modules']   = $data['modules'] ?? [];
        $data['is_active'] = $request->boolean('is_active', true);

        Plan::create($data);

        return redirect()->route('admin.plans.index')
            ->with('status', 'Plan "' . $data['name'] . '" created successfully.');
    }

    public function edit(Plan $plan)
    {
        $allModules = FeatureGate::ALL_MODULES;

        return view('admin.plans.edit', compact('plan', 'allModules'));
    }

    public function update(Request $request, Plan $plan)
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:100'],
            'description'   => ['nullable', 'string', 'max:500'],
            'price'         => ['nullable', 'numeric', 'min:0'],
            'billing_cycle' => ['nullable', 'in:monthly,yearly,custom'],
            'modules'       => ['nullable', 'array'],
            'modules.*'     => ['string', 'in:' . implode(',', FeatureGate::ALL_MODULES)],
            'is_active'     => ['nullable', 'boolean'],
        ]);

        $data['modules']   = $data['modules'] ?? [];
        $data['is_active'] = $request->boolean('is_active', true);

        $plan->update($data);

        return redirect()->route('admin.plans.index')
            ->with('status', 'Plan "' . $plan->name . '" updated successfully.');
    }

    public function toggle(Plan $plan)
    {
        $plan->update(['is_active' => !$plan->is_active]);

        $state = $plan->is_active ? 'activated' : 'deactivated';

        return back()->with('status', 'Plan "' . $plan->name . '" ' . $state . '.');
    }
}
