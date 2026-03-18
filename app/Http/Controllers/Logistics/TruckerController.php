<?php

namespace App\Http\Controllers\Logistics;

use App\Enums\DeliveryRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\DeliveryRequest;

class TruckerController extends Controller
{
    public function market()
    {
        $requests = DeliveryRequest::query()
            ->whereIn('status', [DeliveryRequestStatus::OPEN->value, DeliveryRequestStatus::OFFERED->value])
            ->latest()
            ->get();

        return view('logistics.market', compact('requests'));
    }
}
