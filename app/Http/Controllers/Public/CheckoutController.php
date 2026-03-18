<?php

namespace App\Http\Controllers\Public;

use App\Events\OrderPlaced;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Listing;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Tenant;
use App\Notifications\OrderPlacedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CheckoutController extends Controller
{
    public function print(Order $order)
    {
        $order->load(['tenant.users', 'items.listing.product']);
        $seller = $order->tenant?->users->first();

        return view('core.orders.print', [
            'order'  => $order,
            'seller' => $seller,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'             => ['required', 'string', 'max:150'],
            'surname'          => ['required', 'string', 'max:150'],
            'email'            => ['required', 'email', 'max:200'],
            'phone'            => ['nullable', 'string', 'max:30'],
            'document_type'    => ['required', 'in:receipt,invoice'],
            'company_name'     => ['nullable', 'string', 'max:255'],
            'vat_country'      => ['nullable', 'string', 'size:2'],
            'vat_number'       => ['nullable', 'string', 'max:30'],
            'delivery_address' => ['required', 'string', 'max:255'],
            'delivery_city'    => ['required', 'string', 'max:100'],
            'delivery_zip'     => ['required', 'string', 'max:20'],
            'delivery_country' => ['required', 'string', 'size:2'],
            'create_account'   => ['nullable', 'boolean'],
            'password'         => ['nullable', 'string', 'min:8', 'confirmed', 'required_if:create_account,1'],
        ]);

        $cart = session('cart', []);
        if (!$cart) {
            return back()->withErrors(['cart' => 'Cart is empty']);
        }

        $firstListingId = array_key_first($cart);
        $firstListing = Listing::query()->with('product')->findOrFail($firstListingId);

        // Tenants cannot purchase their own products
        if (auth()->check() && session('tenant_id') &&
            (int) session('tenant_id') === (int) $firstListing->tenant_id) {
            return back()->withErrors(['cart' => 'You cannot purchase your own products.']);
        }

        $tenantId   = $firstListing->tenant_id;
        $total      = collect($cart)->sum(fn ($i) => (float) $i['price'] * (float) $i['qty']);
        $customerId = Auth::guard('customer')->check() ? Auth::guard('customer')->id() : null;

        $order = Order::query()->create([
            'tenant_id'        => $tenantId,
            'customer_id'      => $customerId,
            'customer_name'    => $data['name'],
            'customer_surname' => $data['surname'],
            'customer_email'   => $data['email'],
            'customer_phone'   => $data['phone'] ?? null,
            'status'           => 'pending_wire',
            'total'            => $total,
            'document_type'    => $data['document_type'],
            'company_name'     => $data['document_type'] === 'invoice' ? ($data['company_name'] ?? null) : null,
            'vat_country'      => $data['document_type'] === 'invoice' ? ($data['vat_country'] ?? null) : null,
            'vat_number'       => $data['document_type'] === 'invoice' ? ($data['vat_number'] ?? null) : null,
            'delivery_address' => $data['delivery_address'],
            'delivery_city'    => $data['delivery_city'],
            'delivery_zip'     => $data['delivery_zip'],
            'delivery_country' => $data['delivery_country'],
        ]);

        foreach ($cart as $listingId => $item) {
            OrderItem::query()->create([
                'order_id'   => $order->id,
                'listing_id' => (int) $listingId,
                'price'      => (float) $item['price'],
                'qty'        => (float) $item['qty'],
            ]);
        }

        session()->forget('cart');

        // Auto-create customer account if guest requested it
        $newCustomer = null;
        if (!$customerId && !empty($data['create_account']) && !empty($data['password'])) {
            if (!Customer::where('email', $data['email'])->exists()) {
                $newCustomer = Customer::create([
                    'name'     => $data['name'],
                    'surname'  => $data['surname'],
                    'email'    => $data['email'],
                    'password' => Hash::make($data['password']),
                    'phone'    => $data['phone'] ?? null,
                    'address'  => $data['delivery_address'],
                    'city'     => $data['delivery_city'],
                    'zip_code' => $data['delivery_zip'],
                    'country'  => $data['delivery_country'],
                ]);
                $order->update(['customer_id' => $newCustomer->id]);
                Auth::guard('customer')->login($newCustomer);
                $request->session()->regenerate();
            }
        }

        // Notify seller(s) + broadcast
        $tenant = Tenant::query()->with('users')->find($tenantId);
        if ($tenant) {
            foreach ($tenant->users as $user) {
                $user->notify(new OrderPlacedNotification($order));
            }
        }

        event(new OrderPlaced($order));

        $seller = $tenant?->users->first();

        return view('public.market.success', [
            'order'       => $order->load(['items.listing.product']),
            'seller'      => $seller,
            'newCustomer' => $newCustomer,
        ]);
    }
}
