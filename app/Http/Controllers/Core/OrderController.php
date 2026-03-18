<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $q = Order::query()->orderByDesc('created_at');

        if ($status = $request->string('status')->toString()) {
            $q->where('status', $status);
        }

        if ($search = $request->string('q')->toString()) {
            $q->where(function ($qq) use ($search) {
                $qq->where('customer_name', 'like', '%'.$search.'%')
                   ->orWhere('customer_email', 'like', '%'.$search.'%')
                   ->orWhere('id', $search);
            });
        }

        return view('core.orders.index', [
            'orders' => $q->paginate(15)->withQueryString(),
        ]);
    }

    public function show(Order $order)
    {
        return view('core.orders.show', [
            'order' => $order->load(['items.listing.product']),
        ]);
    }

    public function print(Order $order)
    {
        $order->load(['tenant.users', 'items.listing.product']);
        $seller = $order->tenant?->users->first();

        return view('core.orders.print', [
            'order'  => $order,
            'seller' => $seller,
        ]);
    }
}
