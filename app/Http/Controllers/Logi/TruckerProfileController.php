<?php

namespace App\Http\Controllers\Logi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Logi\UpsertTruckerProfileRequest;
use App\Models\TruckerProfile;
use App\Services\Logi\TruckerProfileService;
use Illuminate\Http\Request;

class TruckerProfileController extends Controller
{
    public function edit(Request $request)
    {
        $profile = TruckerProfile::query()->where('user_id', $request->user()->id)->first();

        return view('logitrace.trucker.profile', [
            'profile' => $profile,
        ]);
    }

    public function update(UpsertTruckerProfileRequest $request, TruckerProfileService $service)
    {
        $service->upsert($request->user()->id, $request->validated());

        return redirect()->route('logi.trucker.profile.edit')->with('status', 'Profile saved');
    }
}
