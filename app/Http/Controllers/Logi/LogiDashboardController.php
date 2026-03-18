<?php

namespace App\Http\Controllers\Logi;

use App\Http\Controllers\Controller;
use App\Models\DeliveryOffer;
use App\Models\DeliveryRequest;
use App\Services\CurrentTenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class LogiDashboardController extends Controller
{
    public function index(Request $request, CurrentTenant $currentTenant)
    {
        $user   = $request->user();
        $tenant = $currentTenant->model();

        // ── Pickup Requests KPIs (draft + open = awaiting pickup) ──────────
        $pickupDraft = DeliveryRequest::query()->where('status', 'draft')->count();
        $pickupOpen  = DeliveryRequest::query()->where('status', 'open')->count();
        $pickupTotal = $pickupDraft + $pickupOpen;

        // ── Shipments KPIs ─────────────────────────────────────────────────
        $shipmentsActive    = DeliveryRequest::query()->where('status', 'accepted')->count();
        $shipmentsCompleted = DeliveryRequest::query()->whereIn('status', ['completed', 'self_delivered'])->count();
        $shipmentsOffered   = DeliveryRequest::query()->where('status', 'offered')->count();

        // ── Marketplace ────────────────────────────────────────────────────
        $marketplaceOpen = DeliveryRequest::query()->where('status', 'open')->count();

        // ── My Offers (trucker view) ───────────────────────────────────────
        // The delivery_offers table may have been created with either
        // 'trucker_user_id' (new migration) or 'trucker_id' (old schema).
        // We detect the actual column at runtime so the dashboard never crashes.
        $myOffersTotal    = 0;
        $myOffersPending  = 0;
        $myOffersAccepted = 0;

        try {
            $cols = Schema::getColumnListing('delivery_offers');

            if (in_array('trucker_user_id', $cols, true)) {
                $truckerCol    = 'trucker_user_id';
                $pendingStatus = 'sent';
            } else {
                $truckerCol    = 'trucker_id';
                $pendingStatus = 'pending';
            }

            $myOffersTotal    = DeliveryOffer::query()->where($truckerCol, $user->id)->count();
            $myOffersPending  = DeliveryOffer::query()->where($truckerCol, $user->id)->where('status', $pendingStatus)->count();
            $myOffersAccepted = DeliveryOffer::query()->where($truckerCol, $user->id)->where('status', 'accepted')->count();

            $recentMyOffers = DeliveryOffer::query()
                ->where($truckerCol, $user->id)
                ->latest()
                ->take(5)
                ->get();
        } catch (\Throwable $e) {
            $recentMyOffers = collect();
        }

        // ── Tenant requests (farmer view) ───────────────────────────────────
        $tenantRequestCount = 0;
        if ($tenant) {
            $tenantRequestCount = DeliveryRequest::query()
                ->where('tenant_id', $tenant->id)
                ->count();
        }

        // ── Recent feeds ───────────────────────────────────────────────────
        $recentOpenRequests = DeliveryRequest::query()
            ->where('status', 'open')
            ->latest()
            ->take(5)
            ->get();

        $recentShipments = DeliveryRequest::query()
            ->whereIn('status', ['accepted', 'completed', 'self_delivered'])
            ->latest()
            ->take(5)
            ->get();

        return view('logitrace.dashboard', [
            'tenant'             => $tenant,
            // Pickup Requests
            'pickupTotal'        => $pickupTotal,
            'pickupOpen'         => $pickupOpen,
            'pickupDraft'        => $pickupDraft,
            // Shipments
            'shipmentsActive'    => $shipmentsActive,
            'shipmentsCompleted' => $shipmentsCompleted,
            'shipmentsOffered'   => $shipmentsOffered,
            // Marketplace
            'marketplaceOpen'    => $marketplaceOpen,
            // My offers
            'myOffersTotal'      => $myOffersTotal,
            'myOffersPending'    => $myOffersPending,
            'myOffersAccepted'   => $myOffersAccepted,
            // Tenant
            'tenantRequestCount' => $tenantRequestCount,
            // Feeds
            'recentOpenRequests' => $recentOpenRequests,
            'recentMyOffers'     => $recentMyOffers,
            'recentShipments'    => $recentShipments,
        ]);
    }

    public function pickupRequests(Request $request, CurrentTenant $currentTenant)
    {
        $tenant   = $currentTenant->model();
        $tenantId = $tenant?->id;

        $kpiDraft    = DeliveryRequest::query()->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))->where('status', 'draft')->count();
        $kpiOpen     = DeliveryRequest::query()->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))->where('status', 'open')->count();
        $kpiOffered  = DeliveryRequest::query()->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))->where('status', 'offered')->count();
        $kpiAccepted = DeliveryRequest::query()->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))->where('status', 'accepted')->count();

        $statusFilter = $request->query('status', 'all');

        $requests = DeliveryRequest::query()
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->when($statusFilter !== 'all', fn ($q) => $q->where('status', $statusFilter))
            ->with('farm')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('logitrace.pickup.index', compact(
            'tenant', 'requests', 'kpiDraft', 'kpiOpen', 'kpiOffered', 'kpiAccepted', 'statusFilter'
        ));
    }

    public function shipments(Request $request, CurrentTenant $currentTenant)
    {
        $tenant   = $currentTenant->model();
        $tenantId = $tenant?->id;

        $kpiOffered       = DeliveryRequest::query()->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))->where('status', 'offered')->count();
        $kpiAccepted      = DeliveryRequest::query()->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))->where('status', 'accepted')->count();
        $kpiCompleted     = DeliveryRequest::query()->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))->where('status', 'completed')->count();
        $kpiSelfDelivered = DeliveryRequest::query()->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))->where('status', 'self_delivered')->count();

        $statusFilter = $request->query('status', 'all');

        $shipments = DeliveryRequest::query()
            ->when($tenantId, fn ($q) => $q->where('tenant_id', $tenantId))
            ->whereIn('status', ['offered', 'accepted', 'completed', 'self_delivered', 'cancelled'])
            ->when($statusFilter !== 'all', fn ($q) => $q->where('status', $statusFilter))
            ->with(['farm', 'acceptedOffer.trucker'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('logitrace.shipments.index', compact(
            'tenant', 'shipments', 'kpiOffered', 'kpiAccepted', 'kpiCompleted', 'kpiSelfDelivered', 'statusFilter'
        ));
    }

    public function routePlanning(Request $request, CurrentTenant $currentTenant)
    {
        $user     = $request->user();
        $tenant   = $currentTenant->model();
        $tenantId = $tenant?->id;

        // Trucker's confirmed routes.
        // SQL: WHERE status = 'accepted'
        //   AND EXISTS (SELECT * FROM delivery_offers
        //               WHERE delivery_requests.accepted_offer_id = delivery_offers.id
        //               AND trucker_user_id = ?)
        $myRoutes = DeliveryRequest::query()
            ->where('status', 'accepted')
            ->whereExists(function ($q) use ($user) {
                $q->from('delivery_offers')
                  ->whereColumn('delivery_requests.accepted_offer_id', 'delivery_offers.id')
                  ->where('trucker_user_id', $user->id);
            })
            ->with(['farm', 'acceptedOffer'])
            ->orderBy('requested_date')
            ->orderBy('id')
            ->get();

        $grouped     = $myRoutes->groupBy(fn ($r) => $r->requested_date?->format('Y-m-d') ?? 'unscheduled');
        $totalWeight = $myRoutes->sum('cargo_weight_kg');

        // Farmer's in-transit deliveries (tenant view)
        $tenantRoutes = collect();
        if ($tenantId) {
            $tenantRoutes = DeliveryRequest::query()
                ->where('tenant_id', $tenantId)
                ->where('status', 'accepted')
                ->with(['acceptedOffer.trucker'])
                ->orderBy('requested_date')
                ->get();
        }

        return view('logitrace.route-planning.index', compact(
            'tenant', 'grouped', 'totalWeight', 'tenantRoutes'
        ));
    }
}
