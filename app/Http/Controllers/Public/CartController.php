<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function add(Request $request, string $listing)
    {
        // Bypass tenant scope — the marketplace is public; any tenant can buy from any other
        $listing = Listing::withoutGlobalScopes()
            ->with(['product' => fn ($q) => $q->withoutGlobalScopes()])
            ->findOrFail($listing);

        // Only active listings can be added
        if (! $listing->is_active) {
            return back()->withErrors(['cart' => 'This listing is no longer available.']);
        }

        // Tenants cannot purchase their own products
        if (auth()->check() && session('tenant_id') &&
            (int) session('tenant_id') === (int) $listing->tenant_id) {
            return back()->withErrors(['cart' => 'You cannot add your own products to the cart.']);
        }

        $cart = session('cart', []);

        // Single-seller enforcement
        if ($cart) {
            $firstId       = array_key_first($cart);
            $cartTenantId  = $cart[$firstId]['tenant_id'] ?? null;

            if ($cartTenantId && $cartTenantId !== $listing->tenant_id) {
                // If buyer explicitly asked to clear cart (via ?clear=1)
                if ($request->boolean('clear')) {
                    $cart = [];
                } else {
                    return back()->with('cart_seller_conflict', $listing->id);
                }
            }
        }

        $qty = max(1, (int) $request->input('qty', 1));

        $cart[$listing->id] = [
            'name'      => $listing->product->default_name,
            'price'     => (float) $listing->price,
            'qty'       => ($cart[$listing->id]['qty'] ?? 0) + $qty,
            'tenant_id' => $listing->tenant_id,
            'type'      => $listing->type,
            'unit'      => $listing->product->unit ?? 'unit',
        ];

        session(['cart' => $cart]);

        return back()->with('cart_added', $listing->id);
    }

    public function show()
    {
        $cart = session('cart', []);

        // Enrich with listing model for availability check
        $listingIds = array_keys($cart);
        $listings   = Listing::query()
            ->withoutGlobalScopes()
            ->with(['product' => fn ($q) => $q->withoutGlobalScopes()])
            ->whereIn('id', $listingIds)
            ->get()
            ->keyBy('id');

        // Resolve seller name
        $sellerName = null;
        if ($cart) {
            $firstId  = array_key_first($cart);
            $tenantId = $cart[$firstId]['tenant_id'] ?? null;
            if ($tenantId) {
                $sellerName = \App\Models\Tenant::find($tenantId)?->name;
            }
        }

        $total = collect($cart)->sum(fn ($i) => (float) $i['price'] * (float) $i['qty']);

        return view('public.market.cart', [
            'cart'       => $cart,
            'listings'   => $listings,
            'total'      => $total,
            'sellerName' => $sellerName,
        ]);
    }

    public function remove($id)
    {
        $cart = session('cart', []);
        unset($cart[$id]);
        session(['cart' => $cart]);

        return back();
    }

    public function clear()
    {
        session()->forget('cart');
        return back();
    }
}
