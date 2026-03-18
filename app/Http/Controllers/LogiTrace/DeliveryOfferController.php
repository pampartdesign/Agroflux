<?php

namespace App\Http\Controllers\LogiTrace;

use App\Http\Controllers\Controller;
use App\Models\DeliveryOffer;
use App\Models\DeliveryRequest;
use App\Services\CurrentTenant;
use Illuminate\Http\Request;

class DeliveryOfferController extends Controller
{
    public function inbox(Request $request, CurrentTenant $tenant)
    {
        $user = $request->user();
        if (!$user || !method_exists($user, 'hasRole') || !($user->hasRole('farmer') || $user->hasRole('admin') || $user->hasRole('super-admin'))) {
            abort(403, 'Farmer/Admin access required.');
        }

        $q = DeliveryOffer::query()
            ->where('farmer_tenant_id', $tenant->id())
            ->orderByDesc('created_at');

        if ($status = $request->string('status')->toString()) {
            $q->where('status', $status);
        }

        return view('logitrace.offers.inbox', [
            'offers' => $q->paginate(15)->withQueryString(),
        ]);
    }

    public function outbox(Request $request, CurrentTenant $tenant)
    {
        $this->authorizeTrucker($request);

        $q = DeliveryOffer::query()
            ->where('tenant_id', $tenant->id())
            ->orderByDesc('created_at');

        if ($status = $request->string('status')->toString()) {
            $q->where('status', $status);
        }

        return view('logitrace.offers.outbox', [
            'offers' => $q->paginate(15)->withQueryString(),
        ]);
    }

    public function createForRequest(Request $request, CurrentTenant $tenant, DeliveryRequest $deliveryRequest)
    {
        $this->authorizeTrucker($request);

        return view('logitrace.offers.create', [
            'request' => $deliveryRequest,
        ]);
    }

    public function storeForRequest(Request $request, CurrentTenant $tenant, DeliveryRequest $deliveryRequest)
    {
        $this->authorizeTrucker($request);

        $data = $request->validate([
            'price' => ['nullable','numeric','min:0'],
            'message' => ['nullable','string'],
            'available_date' => ['nullable','date'],
        ]);

        DeliveryOffer::create([
            'tenant_id' => $tenant->id(),
            'farmer_tenant_id' => $deliveryRequest->tenant_id,
            'delivery_request_id' => $deliveryRequest->id,
            'price' => $data['price'] ?? null,
            'currency' => 'EUR',
            'vehicle_type' => $deliveryRequest->vehicle_type,
            'available_date' => $data['available_date'] ?? null,
            'status' => 'sent',
            'message' => $data['message'] ?? null,
        ]);

        return redirect()->route('logi.offers.outbox')->with('status', 'Offer sent.');
    }

    private function authorizeTrucker(Request $request): void
    {
        $user = $request->user();
        if (!$user || !method_exists($user, 'hasRole') || !$user->hasRole('trucker')) {
            abort(403, 'Trucker access required.');
        }
    }
}
