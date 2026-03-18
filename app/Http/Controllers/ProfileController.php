<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Services\CurrentTenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $tenant       = app(CurrentTenant::class)->model();
        $subscription = $tenant?->activeSubscription()->with('plan')->first();
        $upgradePlans = Plan::where('is_active', true)
            ->when($subscription?->plan_id, fn($q, $id) => $q->where('id', '!=', $id))
            ->orderBy('name')
            ->get();

        return view('profile.edit', [
            'user'         => $request->user(),
            'tenant'       => $tenant,
            'subscription' => $subscription,
            'upgradePlans' => $upgradePlans,
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name'         => ['required', 'string', 'max:150'],
            'surname'      => ['nullable', 'string', 'max:150'],
            'company_name' => ['nullable', 'string', 'max:200'],
            'phone'        => ['nullable', 'string', 'max:40'],
            'address'      => ['nullable', 'string', 'max:300'],
            'zip_code'     => ['required', 'string', 'max:20'],
            'country'      => ['required', 'string', 'max:100'],
            'email'        => ['required', 'email', 'max:200'],
            'bank_name'    => ['nullable', 'string', 'max:150'],
            'iban'         => ['nullable', 'string', 'max:50'],
            'iris_number'  => ['nullable', 'string', 'max:50'],
        ]);

        $user->fill($data);
        $user->save();

        return redirect()->route('profile.edit')->with('status', 'profile-updated');
    }

    public function updatePayment(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'bank_name'   => ['nullable', 'string', 'max:150'],
            'iban'        => ['nullable', 'string', 'max:50'],
            'iris_number' => ['nullable', 'string', 'max:50'],
        ]);

        $user->fill($data);
        $user->save();

        return redirect()->route('profile.edit')->with('status', 'payment-updated');
    }

    public function destroy(Request $request)
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
