<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Services\CurrentTenant;
use App\Services\FeatureGate;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request, CurrentTenant $currentTenant, FeatureGate $gate)
    {
        $user   = $request->user();
        $tenant = $currentTenant->model();

        $effectivePlan = $tenant ? $gate->effectivePlanKey($tenant) : null;
        $trialEndsAt   = $tenant?->trial_ends_at;

        $tenants = $user->is_super_admin
            ? Tenant::query()->orderBy('name')->get()
            : $user->tenants()->orderBy('name')->get();

        // Time-of-day greeting
        $hour     = (int) now()->format('H');
        $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');

        // First name only
        $firstName = explode(' ', trim($user->name))[0];

        return view('app.dashboard', [
            'user'          => $user,
            'tenant'        => $tenant,
            'tenants'       => $tenants,
            'effectivePlan' => $effectivePlan,
            'trialEndsAt'   => $trialEndsAt,
            'greeting'      => $greeting,
            'firstName'     => $firstName,
        ]);
    }
}
