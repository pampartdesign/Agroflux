<?php

namespace App\Http\Controllers\Logi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Logi\StoreDeliveryOfferRequest;
use App\Models\DeliveryOffer;
use App\Models\DeliveryRequest;
use App\Services\Logi\DeliveryOfferService;
use Illuminate\Http\Request as HttpRequest;

class DeliveryOffersController extends Controller
{
    public function store(StoreDeliveryOfferRequest $request, DeliveryRequest $requestModel, DeliveryOfferService $service)
    {
        abort_unless($requestModel->status === 'open', 404);

        $service->createOffer(
            $requestModel,
            $request->user()->id,
            (float) $request->validated('price'),
            $request->validated('message')
        );

        return redirect()->route('logi.available.show', $requestModel)->with('status', 'Offer sent');
    }

    public function mine(HttpRequest $request)
    {
        $offers = DeliveryOffer::query()
            ->where('trucker_user_id', $request->user()->id)
            ->with('request')
            ->latest()
            ->paginate(15);

        return view('logitrace.offers.mine', [
            'offers' => $offers,
        ]);
    }
}
