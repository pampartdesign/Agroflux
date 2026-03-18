<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\MarketplaceDeliveryRequest;
use Illuminate\Http\Request;

class DeliveryRequestController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'listing_id' => ['required', 'integer'],
            'name'       => ['required', 'string', 'max:150'],
            'phone'      => ['required', 'string', 'max:50'],
            'email'      => ['nullable', 'email', 'max:150'],
            'address'    => ['required', 'string', 'max:255'],
            'qty'        => ['required', 'numeric', 'min:0.01'],
            'frequency'  => ['required', 'in:daily,weekly,biweekly,monthly'],
            'start_date' => ['nullable', 'date', 'after_or_equal:today'],
            'notes'      => ['nullable', 'string', 'max:1000'],
        ]);

        // Fetch listing without tenant scope (marketplace is public)
        $listing = Listing::withoutGlobalScopes()->findOrFail($data['listing_id']);

        MarketplaceDeliveryRequest::create([
            'listing_id' => $listing->id,
            'tenant_id'  => $listing->tenant_id,
            'name'       => $data['name'],
            'phone'      => $data['phone'],
            'email'      => $data['email'] ?? null,
            'address'    => $data['address'],
            'qty'        => $data['qty'],
            'frequency'  => $data['frequency'],
            'start_date' => $data['start_date'] ?? null,
            'notes'      => $data['notes'] ?? null,
            'status'     => 'pending',
        ]);

        return redirect()
            ->route('public.marketplace.show', $listing->id)
            ->with('delivery_sent', $listing->id);
    }
}
