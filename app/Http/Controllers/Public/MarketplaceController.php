<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CatalogCategory;
use App\Models\Listing;
use App\Models\Product;
use App\Models\QrCode;
use App\Models\Region;
use App\Models\Tenant;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    public function index(Request $request)
    {
        $q = Listing::query()
            ->withoutGlobalScopes()
            ->with(['product' => fn ($q) => $q->withoutGlobalScopes()->with('category', 'subcategory')])
            ->where('is_active', true);

        // Text search on product name (bypass tenant scope so cross-tenant search works)
        if ($search = trim((string) $request->input('search'))) {
            $q->whereHas('product', fn ($qq) =>
                $qq->withoutGlobalScopes()
                   ->where('default_name', 'like', '%'.$search.'%')
                   ->orWhere('default_description', 'like', '%'.$search.'%')
            );
        }

        if ($type = $request->string('type')->toString()) {
            $q->where('type', $type);
        }

        // Category filter: works for both parent categories and sub-categories
        if ($cat = $request->integer('category_id')) {
            $q->whereHas('product', function ($qq) use ($cat) {
                $qq->withoutGlobalScopes()
                   ->where('category_id', $cat)
                   ->orWhere('subcategory_id', $cat);
            });
        }

        if ($regionId = $request->integer('region_id')) {
            $q->whereIn('tenant_id', function ($sub) use ($regionId) {
                $sub->select('tenant_id')
                    ->from('farms')
                    ->where('region_id', $regionId);
            });
        }

        if ($city = trim((string) $request->input('city'))) {
            $q->whereIn('tenant_id', function ($sub) use ($city) {
                $sub->select('tenant_id')
                    ->from('farms')
                    ->where('city', 'like', '%'.$city.'%');
            });
        }

        $listings = $q->orderByDesc('created_at')->paginate(12)->withQueryString();

        $productIds = $listings->getCollection()->pluck('product_id')->unique()->values();
        $productQrs = QrCode::query()
            ->where('qrable_type', Product::class)
            ->whereIn('qrable_id', $productIds)
            ->get()
            ->keyBy('qrable_id');

        // Preload tenant names for seller display
        $tenantIds = $listings->getCollection()->pluck('tenant_id')->unique()->values();
        $tenants = Tenant::query()
            ->whereIn('id', $tenantIds)
            ->get()
            ->keyBy('id');

        // Load only top-level categories with their children for the filter sidebar
        $categories = CatalogCategory::query()
            ->whereNull('parent_id')
            ->with('children')
            ->orderBy('name')
            ->get();

        // Total active listing count for hero stats
        $totalListings = Listing::withoutGlobalScopes()->where('is_active', true)->count();

        return view('public.market.index', [
            'listings'      => $listings,
            'categories'    => $categories,
            'regions'       => Region::query()->orderBy('name')->get(),
            'productQrs'    => $productQrs,
            'tenants'       => $tenants,
            'totalListings' => $totalListings,
        ]);
    }

    public function show(string $listing)
    {
        // Bypass tenant scope — marketplace listings are public regardless of who is browsing
        $listing = Listing::withoutGlobalScopes()->findOrFail($listing);

        // Only show active listings publicly
        if (! $listing->is_active) {
            abort(404);
        }

        $listing->load(['product' => fn ($q) => $q->withoutGlobalScopes()->with('category', 'subcategory')]);

        $qr = QrCode::query()
            ->where('qrable_type', Product::class)
            ->where('qrable_id', $listing->product_id)
            ->first();

        $seller = Tenant::query()->find($listing->tenant_id);

        // Cart state for this listing
        $cart       = session('cart', []);
        $inCart     = isset($cart[$listing->id]);
        $cartQty    = $inCart ? (int) $cart[$listing->id]['qty'] : 0;

        // Single-seller warning: cart has items from a different seller?
        $cartSellerId    = null;
        $differentSeller = false;
        if ($cart) {
            $firstId      = array_key_first($cart);
            $cartSellerId = $cart[$firstId]['tenant_id'] ?? null;
            if ($cartSellerId && $cartSellerId !== $listing->tenant_id) {
                $differentSeller = true;
            }
        }

        // Own-listing guard: authenticated tenant members cannot buy their own products
        $isOwnListing = auth()->check()
            && session('tenant_id')
            && (int) session('tenant_id') === (int) $listing->tenant_id;

        // Pass filter data so the persistent sidebar works on the detail page too
        $categories = CatalogCategory::query()
            ->whereNull('parent_id')
            ->with('children')
            ->orderBy('name')
            ->get();

        return view('public.market.show', [
            'listing'         => $listing,
            'seller'          => $seller,
            'qr'              => $qr,
            'inCart'          => $inCart,
            'cartQty'         => $cartQty,
            'differentSeller' => $differentSeller,
            'cartSellerName'  => $cartSellerId ? (Tenant::find($cartSellerId)?->name ?? 'another seller') : null,
            'isOwnListing'    => $isOwnListing,
            'categories'      => $categories,
            'regions'         => Region::query()->orderBy('name')->get(),
        ]);
    }
}
