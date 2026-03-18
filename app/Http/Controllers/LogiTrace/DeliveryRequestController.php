<?php

namespace App\Http\Controllers\LogiTrace;

use App\Http\Controllers\Controller;
use App\Models\DeliveryRequest;
use App\Models\TruckerProfile;
use App\Services\CurrentTenant;
use Illuminate\Http\Request;

class DeliveryRequestController extends Controller
{
    public function index(Request $request, CurrentTenant $tenant)
    {
        $user = $request->user();
        if (!$user || !method_exists($user, 'hasRole') || !($user->hasRole('farmer') || $user->hasRole('admin') || $user->hasRole('super-admin'))) {
            abort(403, 'Farmer/Admin access required.');
        }

        $q = DeliveryRequest::query()
            ->where('tenant_id', $tenant->id())
            ->orderByDesc('created_at');

        if ($status = $request->string('status')->toString()) {
            $q->where('status', $status);
        }

        return view('logitrace.requests.index', [
            'requests' => $q->paginate(15)->withQueryString(),
        ]);
    }

    public function create(Request $request)
    {
        $user = $request->user();
        if (!$user || !method_exists($user, 'hasRole') || !($user->hasRole('farmer') || $user->hasRole('admin') || $user->hasRole('super-admin'))) {
            abort(403, 'Farmer/Admin access required.');
        }

        return view('logitrace.requests.create', [
            'truckers' => TruckerProfile::query()->with('tenant')->orderBy('company_name')->get(),
        ]);
    }

    public function store(Request $request, CurrentTenant $tenant)
    {
        $user = $request->user();
        if (!$user || !method_exists($user, 'hasRole') || !($user->hasRole('farmer') || $user->hasRole('admin') || $user->hasRole('super-admin'))) {
            abort(403, 'Farmer/Admin access required.');
        }

        $data = $request->validate([
            'title' => ['required','string','max:180'],
            'description' => ['nullable','string'],
            'vehicle_type' => ['required','in:van,small_pickup,refrigerated_truck'],
            'pickup_date' => ['nullable','date'],
            'pickup_address' => ['required','string','max:255'],
            'dropoff_address' => ['required','string','max:255'],
            'target_trucker_tenant_id' => ['nullable','integer'],
            'estimated_weight_kg' => ['nullable','numeric','min:0'],
            'estimated_volume_m3' => ['nullable','numeric','min:0'],
        ]);

        $data['tenant_id'] = $tenant->id();
        $data['status'] = 'open';

        $req = DeliveryRequest::create($data);

        return redirect()->route('logi.requests.show', $req)->with('status', 'Delivery request created.');
    }

    public function show(Request $request, CurrentTenant $tenant, DeliveryRequest $deliveryRequest)
    {
        $user = $request->user();
        if (!$user || !method_exists($user, 'hasRole') || !($user->hasRole('farmer') || $user->hasRole('admin') || $user->hasRole('super-admin'))) {
            abort(403, 'Farmer/Admin access required.');
        }

        if ((int)$deliveryRequest->tenant_id !== (int)$tenant->id()) {
            abort(404);
        }

        $deliveryRequest->load('offers.tenant');

        $directionsUrl = $this->googleDirectionsUrl($deliveryRequest->pickup_address, $deliveryRequest->dropoff_address);

        return view('logitrace.requests.show', [
            'request' => $deliveryRequest,
            'directionsUrl' => $directionsUrl,
        ]);
    }

    private function googleDirectionsUrl(string $from, string $to): string
    {
        $from = urlencode($from);
        $to = urlencode($to);
        return "https://www.google.com/maps/dir/?api=1&origin={$from}&destination={$to}&travelmode=driving";
    }
}
