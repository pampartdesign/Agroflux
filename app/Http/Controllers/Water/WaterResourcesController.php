<?php

namespace App\Http\Controllers\Water;

use App\Http\Controllers\Controller;
use App\Models\WaterResource;
use Illuminate\Http\Request;

class WaterResourcesController extends Controller
{
    public function index(Request $request)
    {
        $resources = WaterResource::orderBy('name')->get();

        $total      = $resources->count();
        $wells      = $resources->where('type', 'Well')->count();
        $reservoirs = $resources->where('type', 'Reservoir')->count();
        $irrigation = $resources->where('type', 'Irrigation System')->count();

        return view('water.resources.index', compact(
            'resources', 'total', 'wells', 'reservoirs', 'irrigation'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:200'],
            'type'        => ['required', 'string', 'max:50'],
            'capacity_m3' => ['nullable', 'numeric', 'min:0'],
            'level_pct'   => ['nullable', 'integer', 'min:0', 'max:100'],
            'notes'       => ['nullable', 'string', 'max:1000'],
        ]);

        WaterResource::create($data);

        return redirect()->route('water.resources.index')->with('success', 'Water source added.');
    }

    public function update(Request $request, WaterResource $waterResource)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:200'],
            'type'        => ['required', 'string', 'max:50'],
            'capacity_m3' => ['nullable', 'numeric', 'min:0'],
            'level_pct'   => ['nullable', 'integer', 'min:0', 'max:100'],
            'notes'       => ['nullable', 'string', 'max:1000'],
        ]);

        $waterResource->update($data);

        return redirect()->route('water.resources.index')->with('success', 'Water source updated.');
    }

    public function destroy(WaterResource $waterResource)
    {
        $waterResource->delete();

        return redirect()->route('water.resources.index')->with('success', 'Water source removed.');
    }
}
