<?php

namespace App\Http\Controllers\Logistics;

use App\Enums\DeliveryRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\DeliveryRequest;
use Illuminate\Http\Request;

class FarmerDeliveryController extends Controller
{
    public function index()
    {
        $requests = DeliveryRequest::query()
            ->where('tenant_id', session('tenant_id'))
            ->with(['offers'])
            ->latest()
            ->get();

        return view('logistics.requests', compact('requests'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pickup_address' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
        ]);

        DeliveryRequest::create([
            'tenant_id' => session('tenant_id'),
            'pickup_address' => $data['pickup_address'],
            'destination' => $data['destination'],
            'status' => DeliveryRequestStatus::DRAFT->value,
        ]);

        return back()->with('status', 'Request created (draft).');
    }

    public function publish(DeliveryRequest $requestModel)
    {
        $this->assertTenant($requestModel);

        if (!in_array($requestModel->status, [DeliveryRequestStatus::DRAFT->value, DeliveryRequestStatus::CANCELLED->value], true)) {
            return back()->with('error', 'Request cannot be published from current status.');
        }

        $requestModel->update(['status' => DeliveryRequestStatus::OPEN->value]);

        return back()->with('status', 'Request published to marketplace.');
    }

    public function selfDeliver(DeliveryRequest $requestModel)
    {
        $this->assertTenant($requestModel);

        if (!in_array($requestModel->status, [DeliveryRequestStatus::DRAFT->value, DeliveryRequestStatus::OPEN->value, DeliveryRequestStatus::OFFERED->value], true)) {
            return back()->with('error', 'Self-delivery not allowed from current status.');
        }

        $requestModel->update(['status' => DeliveryRequestStatus::SELF_DELIVERED->value]);

        return back()->with('status', 'Marked as self-delivered.');
    }

    public function complete(DeliveryRequest $requestModel)
    {
        $this->assertTenant($requestModel);

        if (!in_array($requestModel->status, [DeliveryRequestStatus::ACCEPTED->value, DeliveryRequestStatus::SELF_DELIVERED->value], true)) {
            return back()->with('error', 'Complete not allowed from current status.');
        }

        $requestModel->update(['status' => DeliveryRequestStatus::COMPLETED->value]);

        return back()->with('status', 'Marked as completed.');
    }

    public function cancel(DeliveryRequest $requestModel)
    {
        $this->assertTenant($requestModel);

        if (in_array($requestModel->status, [DeliveryRequestStatus::COMPLETED->value, DeliveryRequestStatus::CANCELLED->value], true)) {
            return back()->with('error', 'Cannot cancel.');
        }

        $requestModel->update(['status' => DeliveryRequestStatus::CANCELLED->value]);

        return back()->with('status', 'Request cancelled.');
    }

    private function assertTenant(DeliveryRequest $requestModel): void
    {
        if ((int) $requestModel->tenant_id !== (int) session('tenant_id')) {
            abort(404);
        }
    }
}
