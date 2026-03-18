<?php

namespace App\Http\Controllers\LogiTrace;

use App\Http\Controllers\Controller;
use App\Models\DeliveryOffer;
use App\Models\DeliveryRequest;
use App\Models\TruckerProfile;
use App\Services\CurrentTenant;
use Illuminate\Http\Request;

class LogiDashboardController extends Controller
{
    public function index(Request $request, CurrentTenant $tenant)
    {
        $tenantId = $tenant->id();

        $isTrucker = $request->user() && method_exists($request->user(), 'hasRole') && $request->user()->hasRole('trucker');

        $profile = TruckerProfile::query()->where('tenant_id', $tenantId)->first();

        $stats = [
            'open_requests' => DeliveryRequest::query()->where('tenant_id', $tenantId)->where('status','open')->count(),
            'sent_offers' => DeliveryOffer::query()->where('tenant_id', $tenantId)->where('status','sent')->count(),
            'received_offers' => DeliveryOffer::query()->where('farmer_tenant_id', $tenantId)->where('status','sent')->count(),
        ];

        return view('logitrace.dashboard', [
            'isTrucker' => $isTrucker,
            'profile' => $profile,
            'stats' => $stats,
        ]);
    }
}
