<?php

namespace App\Http\Controllers\Public\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CustomerAuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::guard('customer')->check()) {
            return redirect()->route('customer.dashboard');
        }
        return view('public.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('customer')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('customer.dashboard'));
        }

        return back()->withErrors([
            'email' => __('market.login_failed'),
        ])->onlyInput('email');
    }

    public function showRegister(): View|RedirectResponse
    {
        if (Auth::guard('customer')->check()) {
            return redirect()->route('customer.dashboard');
        }
        return view('public.auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'                  => ['required', 'string', 'max:100'],
            'surname'               => ['required', 'string', 'max:100'],
            'email'                 => ['required', 'email', 'max:200', 'unique:customers,email'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'phone'                 => ['nullable', 'string', 'max:30'],
            'address'               => ['nullable', 'string', 'max:255'],
            'city'                  => ['nullable', 'string', 'max:100'],
            'zip_code'              => ['nullable', 'string', 'max:20'],
            'country'               => ['nullable', 'string', 'size:2'],
        ]);

        $customer = Customer::create($data);

        Auth::guard('customer')->login($customer);
        $request->session()->regenerate();

        return redirect()->route('customer.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('public.marketplace');
    }
}
