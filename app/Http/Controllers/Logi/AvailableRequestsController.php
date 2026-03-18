<?php

namespace App\Http\Controllers\Logi;

use App\Http\Controllers\Controller;
use App\Models\DeliveryOffer;
use App\Models\DeliveryRequest;
use App\Models\TruckerProfile;
use Illuminate\Http\Request as HttpRequest;

class AvailableRequestsController extends Controller
{
    public function index(HttpRequest $httpRequest)
    {
        $profile = TruckerProfile::query()->where('user_id', $httpRequest->user()->id)->first();

        $requests = DeliveryRequest::query()
            ->where('status', 'open')
            ->latest()
            ->paginate(15);

        return view('logitrace.available.index', [
            'profile' => $profile,
            'requests' => $requests,
        ]);
    }

    public function show(HttpRequest $httpRequest, DeliveryRequest $request)
    {
        $profile = TruckerProfile::query()->where('user_id', $httpRequest->user()->id)->first();

        abort_unless($request->status === 'open', 404);

        $myOffer = DeliveryOffer::query()
            ->where('delivery_request_id', $request->id)
            ->where('trucker_user_id', $httpRequest->user()->id)
            ->latest()
            ->first();

        return view('logitrace.available.show', [
            'profile' => $profile,
            'requestModel' => $request,
            'myOffer' => $myOffer,
        ]);
    }
}
