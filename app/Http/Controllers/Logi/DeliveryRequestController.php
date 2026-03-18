<?php

namespace App\Http\Controllers\Logi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Logi\StoreDeliveryRequestRequest;
use App\Models\DeliveryOffer;
use App\Models\DeliveryRequest;
use App\Models\Farm;
use App\Services\CurrentTenant;
use App\Services\Logi\DeliveryRequestService;
use Illuminate\Http\Request as HttpRequest;

class DeliveryRequestController extends Controller
{
    public function index(HttpRequest $httpRequest, CurrentTenant $currentTenant)
    {
        $tenant = $currentTenant->model();

        $requests = DeliveryRequest::query()
            ->where('tenant_id', $tenant->id)
            ->latest()
            ->paginate(15);

        return view('logitrace.requests.index', [
            'tenant' => $tenant,
            'requests' => $requests,
        ]);
    }

    public function create(HttpRequest $httpRequest, CurrentTenant $currentTenant)
    {
        $tenant = $currentTenant->model();

        $farms = Farm::query()
            ->where('tenant_id', $tenant->id)
            ->orderBy('name')
            ->get();

        return view('logitrace.requests.create', [
            'tenant' => $tenant,
            'farms' => $farms,
        ]);
    }

    public function store(StoreDeliveryRequestRequest $request, CurrentTenant $currentTenant)
    {
        $tenant = $currentTenant->model();

        $dr = new DeliveryRequest();
        $dr->tenant_id = $tenant->id;
        $dr->farm_id = (int) $request->validated('farm_id');
        $dr->pickup_address = $request->validated('pickup_address');
        $dr->delivery_address = $request->validated('delivery_address');
        $dr->cargo_description = $request->validated('cargo_description');
        $dr->cargo_weight_kg = $request->validated('cargo_weight_kg');
        $dr->requested_date = $request->validated('requested_date');
        $dr->status = 'draft';
        $dr->delivery_mode = 'marketplace';
        $dr->save();

        return redirect()->route('logi.requests.show', $dr)->with('status', 'Delivery request created');
    }

    public function show(HttpRequest $httpRequest, CurrentTenant $currentTenant, DeliveryRequest $request)
    {
        $tenant = $currentTenant->model();
        abort_unless($request->tenant_id === $tenant->id, 404);

        $offers = $request->offers()->with('trucker')->latest()->get();

        return view('logitrace.requests.show', [
            'tenant' => $tenant,
            'requestModel' => $request,
            'offers' => $offers,
        ]);
    }

    public function publish(HttpRequest $httpRequest, CurrentTenant $currentTenant, DeliveryRequest $request, DeliveryRequestService $service)
    {
        $tenant = $currentTenant->model();
        abort_unless($request->tenant_id === $tenant->id, 404);

        $service->publish($request);

        return back()->with('status', 'Request published to marketplace');
    }

    public function selfDelivered(HttpRequest $httpRequest, CurrentTenant $currentTenant, DeliveryRequest $request, DeliveryRequestService $service)
    {
        $tenant = $currentTenant->model();
        abort_unless($request->tenant_id === $tenant->id, 404);

        $service->markSelfDelivered($request);

        return back()->with('status', 'Marked as self delivered');
    }

    public function cancel(HttpRequest $httpRequest, CurrentTenant $currentTenant, DeliveryRequest $request, DeliveryRequestService $service)
    {
        $tenant = $currentTenant->model();
        abort_unless($request->tenant_id === $tenant->id, 404);

        $service->cancel($request);

        return back()->with('status', 'Request cancelled');
    }

    public function complete(HttpRequest $httpRequest, CurrentTenant $currentTenant, DeliveryRequest $request, DeliveryRequestService $service)
    {
        $tenant = $currentTenant->model();
        abort_unless($request->tenant_id === $tenant->id, 404);
        abort_unless($request->status === 'accepted', 422);

        $service->markCompleted($request);

        return back()->with('status', 'Shipment marked as completed.');
    }

    public function truckerComplete(HttpRequest $httpRequest, DeliveryRequest $request, DeliveryRequestService $service)
    {
        $user = $httpRequest->user();
        $request->loadMissing('acceptedOffer');

        abort_unless(
            $request->status === 'accepted' &&
            $request->acceptedOffer &&
            $request->acceptedOffer->trucker_user_id === $user->id,
            403
        );

        $service->markCompleted($request);

        return back()->with('status', 'Delivery marked as completed.');
    }

    public function acceptOffer(HttpRequest $httpRequest, CurrentTenant $currentTenant, DeliveryRequest $request, DeliveryOffer $offer, DeliveryRequestService $service)
    {
        $tenant = $currentTenant->model();
        abort_unless($request->tenant_id === $tenant->id, 404);
        abort_unless($offer->delivery_request_id === $request->id, 404);

        $service->acceptOffer($request, $offer);

        return back()->with('status', 'Offer accepted');
    }
}
