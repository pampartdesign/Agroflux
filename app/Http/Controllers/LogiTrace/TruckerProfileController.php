<?php

namespace App\Http\Controllers\LogiTrace;

use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Models\TruckerProfile;
use App\Services\CurrentTenant;
use Illuminate\Http\Request;

class TruckerProfileController extends Controller
{
    public function edit(Request $request, CurrentTenant $tenant)
    {
        $this->authorizeTrucker($request);

        $profile = TruckerProfile::query()->firstOrCreate(
            ['tenant_id' => $tenant->id()],
            ['user_id' => $request->user()->id, 'email' => $request->user()->email]
        );

        return view('logitrace.profile.edit', [
            'profile' => $profile,
            'regions' => Region::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, CurrentTenant $tenant)
    {
        $this->authorizeTrucker($request);

        $profile = TruckerProfile::query()->where('tenant_id', $tenant->id())->firstOrFail();

        $data = $request->validate([
            'company_name' => ['nullable','string','max:200'],
            'contact_name' => ['nullable','string','max:200'],
            'phone' => ['nullable','string','max:50'],
            'email' => ['nullable','email','max:200'],
            'region_id' => ['nullable','integer'],
            'city' => ['nullable','string','max:120'],
            'address_line' => ['nullable','string','max:255'],
            'supports_van' => ['sometimes','boolean'],
            'supports_small_pickup' => ['sometimes','boolean'],
            'supports_refrigerated_truck' => ['sometimes','boolean'],
            'notes' => ['nullable','string'],
        ]);

        $data['supports_van'] = (bool) ($request->boolean('supports_van'));
        $data['supports_small_pickup'] = (bool) ($request->boolean('supports_small_pickup'));
        $data['supports_refrigerated_truck'] = (bool) ($request->boolean('supports_refrigerated_truck'));

        $profile->update($data);

        return redirect()->route('logi.profile.edit')->with('status', 'Profile updated.');
    }

    private function authorizeTrucker(Request $request): void
    {
        $user = $request->user();
        if (!$user || !method_exists($user, 'hasRole') || !$user->hasRole('trucker')) {
            abort(403, 'Trucker access required.');
        }
    }
}
