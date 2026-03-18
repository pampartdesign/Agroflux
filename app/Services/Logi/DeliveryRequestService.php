<?php

namespace App\Services\Logi;

use App\Models\DeliveryOffer;
use App\Models\DeliveryRequest;

class DeliveryRequestService
{
    public function publish(DeliveryRequest $request): DeliveryRequest
    {
        if (in_array($request->status, ['cancelled','completed','self_delivered'], true)) {
            return $request;
        }

        $request->delivery_mode = 'marketplace';
        $request->status = 'open';
        $request->save();

        return $request;
    }

    public function markSelfDelivered(DeliveryRequest $request): DeliveryRequest
    {
        if (in_array($request->status, ['completed','cancelled'], true)) {
            return $request;
        }

        $request->delivery_mode = 'self';
        $request->status = 'self_delivered';
        $request->accepted_offer_id = null;
        $request->save();

        $request->offers()->where('status', 'sent')->update(['status' => 'rejected']);

        return $request;
    }

    public function cancel(DeliveryRequest $request): DeliveryRequest
    {
        if ($request->status === 'completed') {
            return $request;
        }

        $request->status = 'cancelled';
        $request->accepted_offer_id = null;
        $request->save();

        $request->offers()->where('status', 'sent')->update(['status' => 'rejected']);

        return $request;
    }

    public function markCompleted(DeliveryRequest $request): DeliveryRequest
    {
        if (in_array($request->status, ['completed', 'cancelled'], true)) {
            return $request;
        }

        $request->status = 'completed';
        $request->save();

        return $request;
    }

    public function acceptOffer(DeliveryRequest $request, DeliveryOffer $offer): DeliveryRequest
    {
        if (in_array($request->status, ['cancelled','completed','self_delivered'], true)) {
            return $request;
        }

        $request->delivery_mode = 'marketplace';
        $request->status = 'accepted';
        $request->accepted_offer_id = $offer->id;
        $request->save();

        $offer->status = 'accepted';
        $offer->save();

        $request->offers()
            ->where('id', '!=', $offer->id)
            ->where('status', 'sent')
            ->update(['status' => 'rejected']);

        return $request;
    }
}
