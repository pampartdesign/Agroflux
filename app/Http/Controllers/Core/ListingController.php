<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListingRequest;
use App\Models\Listing;
use App\Models\Product;
use App\Services\CurrentTenant;
use Illuminate\Http\Request;

class ListingController extends Controller
{
    public function index(Request $request)
    {
        $q = Listing::query()->with('product')->orderByDesc('created_at');

        if ($type = $request->string('type')->toString()) {
            $q->where('type', $type);
        }

        $user = auth()->user();
        $hasPaymentInfo = !empty($user->bank_name) && !empty($user->iban);

        return view('core.listings.index', [
            'listings'       => $q->paginate(15)->withQueryString(),
            'hasPaymentInfo' => $hasPaymentInfo,
        ]);
    }

    public function create()
    {
        $user = auth()->user();
        $hasPaymentInfo = !empty($user->bank_name) && !empty($user->iban);

        return view('core.listings.create', [
            'products'       => Product::query()->orderBy('default_name')->get(),
            'hasPaymentInfo' => $hasPaymentInfo,
        ]);
    }

    public function store(ListingRequest $request)
    {
        $data = $request->validated();

        // Derive tenant_id from the selected product so this works correctly
        // for both regular subscribers and super-admins (who bypass tenant middleware).
        $product = Product::query()
            ->withoutGlobalScopes()
            ->findOrFail($data['product_id']);
        $data['tenant_id'] = $product->tenant_id;

        if ($data['type'] === 'instock') {
            $data['expected_harvest_at'] = null;
            $data['upfront_percent'] = 25.00;
        } else {
            $data['available_qty'] = null;
        }

        Listing::query()->create($data);

        return redirect()->route('core.listings.index')->with('success', 'Listing created successfully.');
    }

    public function edit(Listing $listing)
    {
        return view('core.listings.edit', [
            'listing'  => $listing,
            'products' => Product::query()->orderBy('default_name')->get(),
        ]);
    }

    public function update(ListingRequest $request, Listing $listing)
    {
        $data = $request->validated();

        if ($data['type'] === 'instock') {
            $data['expected_harvest_at'] = null;
            $data['upfront_percent'] = 25.00;
        } else {
            $data['available_qty'] = null;
        }

        $listing->update($data);

        return redirect()->route('core.listings.index');
    }

    public function destroy(Listing $listing)
    {
        // Block if listing has any orders tied to it
        $orderCount = \App\Models\OrderItem::where('listing_id', $listing->id)->count();
        if ($orderCount > 0) {
            return back()->with('error', 'Cannot delete this listing — it has ' . $orderCount . ' associated order item(s). Hide it from the marketplace instead.');
        }

        $name = $listing->product?->default_name ?? ('Listing #' . $listing->id);
        $listing->delete();

        return redirect()->route('core.listings.index')
            ->with('success', "\"{$name}\" listing has been deleted.");
    }

    public function toggle(Listing $listing, CurrentTenant $currentTenant)
    {
        // Tenant guard: only the listing's tenant can toggle
        $tenant = $currentTenant->model();
        abort_unless($listing->tenant_id === $tenant->id, 403);

        $listing->update(['is_active' => ! $listing->is_active]);

        $name = $listing->product->default_name ?? 'Listing';

        return back()->with(
            'success',
            $listing->is_active
                ? "\"{$name}\" is now live on the marketplace."
                : "\"{$name}\" has been hidden from the marketplace."
        );
    }
}
