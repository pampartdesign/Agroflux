<?php

namespace App\Http\Controllers\Public\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class CustomerAccountController extends Controller
{
    public function dashboard(): View
    {
        $customer = Auth::guard('customer')->user();
        $recentOrders = Order::where('customer_id', $customer->id)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('public.account.dashboard', compact('customer', 'recentOrders'));
    }

    public function orders(): View
    {
        $customer = Auth::guard('customer')->user();
        $orders = Order::where('customer_id', $customer->id)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('public.account.orders', compact('customer', 'orders'));
    }

    public function orderShow(Order $order): View|\Symfony\Component\HttpFoundation\Response
    {
        $customer = Auth::guard('customer')->user();

        if ((int) $order->customer_id !== (int) $customer->id) {
            abort(403);
        }

        $order->load(['items.listing.product', 'tenant']);

        return view('public.account.order', compact('customer', 'order'));
    }

    public function profile(): View
    {
        $customer = Auth::guard('customer')->user();
        return view('public.account.profile', compact('customer'));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $customer = Auth::guard('customer')->user();

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'surname'  => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:200', 'unique:customers,email,' . $customer->id],
            'phone'    => ['nullable', 'string', 'max:30'],
            'address'  => ['nullable', 'string', 'max:255'],
            'city'     => ['nullable', 'string', 'max:100'],
            'zip_code' => ['nullable', 'string', 'max:20'],
            'country'  => ['nullable', 'string', 'size:2'],
        ]);

        $customer->update($data);

        return back()->with('success', __('market.profile_saved'));
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $customer = Auth::guard('customer')->user();

        $request->validate([
            'current_password' => ['required', 'current_password:customer'],
            'password'         => ['required', Password::min(8), 'confirmed'],
        ]);

        $customer->update(['password' => Hash::make($request->input('password'))]);

        return back()->with('success', __('market.password_updated'));
    }
}
