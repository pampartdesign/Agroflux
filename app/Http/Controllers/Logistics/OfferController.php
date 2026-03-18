<?php

namespace App\Http\Controllers\Logistics;

use App\Enums\DeliveryRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\DeliveryOffer;
use App\Models\DeliveryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OfferController extends Controller
{
    public function store(Request $request, DeliveryRequest $requestModel)
    {
        $data = $request->validate([
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        DeliveryOffer::create([
            'delivery_request_id' => $requestModel->id,
            'trucker_id' => auth()->id(),
            'price' => $data['price'],
            'status' => 'pending',
        ]);

        // If request is open/draft, mark as offered when first offer arrives.
        if (in_array($requestModel->status, [DeliveryRequestStatus::OPEN->value, DeliveryRequestStatus::DRAFT->value], true)) {
            $requestModel->update(['status' => DeliveryRequestStatus::OFFERED->value]);
        }

        return back()->with('status', 'Offer sent.');
    }

    public function accept(DeliveryOffer $offer)
    {
        $requestModel = $offer->deliveryRequest;

        // Basic tenant ownership check
        if ((int) $requestModel->tenant_id !== (int) session('tenant_id')) {
            abort(404);
        }

        DB::transaction(function () use ($offer, $requestModel) {
            // Accept this offer
            $offer->update(['status' => 'accepted']);

            // Reject other offers
            DeliveryOffer::query()
                ->where('delivery_request_id', $requestModel->id)
                ->where('id', '!=', $offer->id)
                ->update(['status' => 'rejected']);

            // Move request to accepted
            $requestModel->update(['status' => DeliveryRequestStatus::ACCEPTED->value]);
        });

        return back()->with('status', 'Offer accepted.');
    }
}
