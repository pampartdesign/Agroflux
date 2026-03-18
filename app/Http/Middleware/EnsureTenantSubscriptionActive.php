<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Services\CurrentTenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantSubscriptionActive
{
    public function __construct(protected CurrentTenant $currentTenant) {}

    /**
     * Trial logic:
     * - User can login always.
     * - If trial expired and not paid, block actions (POST/PUT/PATCH/DELETE).
     * - Allow GET pages so they can see the paywall screen.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = $this->currentTenant->model();

        if (!$tenant instanceof Tenant) {
            return redirect()->route('tenant.select');
        }

        // If tenant has "is_paid" or "subscription_status" field, respect it if present.
        $isPaid = false;
        if (isset($tenant->is_paid)) {
            $isPaid = (bool) $tenant->is_paid;
        } elseif (isset($tenant->subscription_status)) {
            $isPaid = in_array(strtolower((string) $tenant->subscription_status), ['active', 'paid'], true);
        }

        $trialEndsAt = $tenant->trial_ends_at ?? null;
        $trialActive = false;

        if ($trialEndsAt) {
            try {
                $trialActive = \Carbon\Carbon::parse($trialEndsAt)->isFuture();
            } catch (\Throwable $e) {
                $trialActive = false;
            }
        }

        // Allow everything if trial active or paid.
        if ($trialActive || $isPaid) {
            return $next($request);
        }

        // Trial expired and unpaid: allow only GET/HEAD so they can navigate to billing.
        if (in_array($request->method(), ['GET', 'HEAD'], true)) {
            $request->session()->flash('warning', 'Trial expired. Please complete payment to unlock actions.');
            return $next($request);
        }

        return redirect()->route('plus.billing.locked');
    }
}
