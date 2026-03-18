<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Http\Requests\FarmRequest;
use App\Models\Farm;
use App\Services\CurrentTenant;
use App\Services\FeatureGate;
use Illuminate\Http\Request;

class FarmController extends Controller
{
    public function index(Request $request, CurrentTenant $currentTenant)
    {
        $tenant = $currentTenant->model();

        return view('core.farms.index', [
            'tenant' => $tenant,
            'farms'  => Farm::withCount('fields')->orderBy('name')->get(),
        ]);
    }

    public function create(CurrentTenant $currentTenant, FeatureGate $gate)
    {
        $tenant = $currentTenant->model();
        $max    = $gate->maxFarmsForTenant($tenant);
        $count  = Farm::query()->count();

        return view('core.farms.create', [
            'tenant'    => $tenant,
            'maxFarms'  => $max,
            'farmCount' => $count,
        ]);
    }

    public function store(FarmRequest $request, CurrentTenant $currentTenant, FeatureGate $gate)
    {
        $tenant = $currentTenant->model();

        $max   = $gate->maxFarmsForTenant($tenant);
        $count = Farm::query()->count();

        if ($max > 0 && $count >= $max) {
            return back()
                ->withInput()
                ->withErrors(['name' => "Farm limit reached for your plan ({$max})."]);
        }

        Farm::query()->create([
            'tenant_id'     => $tenant->id,
            'region_id'     => $request->integer('region_id') ?: null,
            'name'          => $request->string('name')->toString(),
            'area_ha'       => $request->input('area_ha'),
            'address_line1' => $request->input('address_line1'),
            'city'          => $request->input('city'),
            'postal_code'   => $request->input('postal_code'),
            'latitude'      => $request->input('latitude'),
            'longitude'     => $request->input('longitude'),
            'notes'         => $request->input('notes'),
        ]);

        return redirect()->route('core.farms.index')->with('success', 'Farm created successfully.');
    }

    public function show(Farm $farm, CurrentTenant $currentTenant)
    {
        $tenant = $currentTenant->model();

        if ((int) $farm->tenant_id !== (int) $tenant->id) {
            return redirect()->route('core.farms.index')
                ->with('error', 'You do not have access to that farm.');
        }

        $farm->load('fields');

        return view('core.farms.show', [
            'farm'   => $farm,
            'tenant' => $tenant,
        ]);
    }

    public function edit(Farm $farm)
    {
        return view('core.farms.edit', [
            'farm' => $farm,
        ]);
    }

    public function update(FarmRequest $request, Farm $farm)
    {
        $farm->update([
            'region_id'     => $request->integer('region_id') ?: null,
            'name'          => $request->string('name')->toString(),
            'area_ha'       => $request->input('area_ha'),
            'address_line1' => $request->input('address_line1'),
            'city'          => $request->input('city'),
            'postal_code'   => $request->input('postal_code'),
            'latitude'      => $request->input('latitude'),
            'longitude'     => $request->input('longitude'),
            'notes'         => $request->input('notes'),
        ]);

        return redirect()->route('core.farms.index')->with('success', 'Farm updated successfully.');
    }
}
