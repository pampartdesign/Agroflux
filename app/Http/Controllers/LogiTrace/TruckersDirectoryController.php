<?php

namespace App\Http\Controllers\LogiTrace;

use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Models\TruckerProfile;
use Illuminate\Http\Request;

class TruckersDirectoryController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user || !method_exists($user, 'hasRole') || !($user->hasRole('farmer') || $user->hasRole('admin') || $user->hasRole('super-admin'))) {
            abort(403, 'Farmer/Admin access required.');
        }

        $q = TruckerProfile::query()->with('region')->orderBy('company_name');

        if ($regionId = $request->integer('region_id')) {
            $q->where('region_id', $regionId);
        }

        if ($vehicle = $request->string('vehicle')->toString()) {
            if ($vehicle === 'van') $q->where('supports_van', true);
            if ($vehicle === 'small_pickup') $q->where('supports_small_pickup', true);
            if ($vehicle === 'refrigerated_truck') $q->where('supports_refrigerated_truck', true);
        }

        return view('logitrace.truckers.index', [
            'truckers' => $q->paginate(15)->withQueryString(),
            'regions' => Region::query()->orderBy('name')->get(),
        ]);
    }
}
