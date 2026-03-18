<?php

namespace App\Services\Logi;

use App\Models\DeliveryOffer;
use App\Models\DeliveryRequest;

class DeliveryOfferService
{
    public function createOffer(DeliveryRequest $request, int $truckerUserId, float $price, ?string $message = null, string $currency = 'EUR'): DeliveryOffer
    {
        $offer = new DeliveryOffer();
        $offer->delivery_request_id = $request->id;
        $offer->trucker_user_id = $truckerUserId;
        $offer->price = $price;
        $offer->currency = $currency;
        $offer->message = $message;
        $offer->status = 'sent';
        $offer->save();

        if ($request->status === 'open') {
            $request->status = 'offered';
            $request->save();
        }

        return $offer;
    }
}
