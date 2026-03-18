<?php

namespace App\Http\Controllers\Drone;

use App\Http\Controllers\Controller;
use App\Models\Drone;
use App\Services\CurrentTenant;
use Illuminate\Http\Request;

class DroneController extends Controller
{
    public function index()
    {
        $drones = Drone::orderBy('name')->get();
        return view('drone.drones.index', compact('drones'));
    }

    public function store(Request $request)
    {
        $tenantId = app(CurrentTenant::class)->id();
        abort_unless($tenantId, 403);

        $data = $request->validate([
            'name'                => ['required', 'string', 'max:200'],
            'model'               => ['nullable', 'string', 'max:200'],
            'serial_number'       => ['nullable', 'string', 'max:100'],
            'status'              => ['required', 'in:active,maintenance,retired'],
            'default_altitude_m'  => ['required', 'numeric', 'min:1', 'max:500'],
            'default_speed_ms'    => ['required', 'numeric', 'min:0.5', 'max:30'],
            'default_overlap_pct' => ['required', 'integer', 'min:0', 'max:95'],
            'default_spacing_m'   => ['required', 'numeric', 'min:1', 'max:100'],
            'default_buffer_m'    => ['required', 'numeric', 'min:0', 'max:50'],
            'notes'               => ['nullable', 'string', 'max:2000'],
        ]);

        $data['tenant_id'] = $tenantId;
        Drone::create($data);

        return back()->with('success', 'Drone registered successfully.');
    }

    public function update(Request $request, Drone $drone)
    {
        $tenantId = app(CurrentTenant::class)->id();
        abort_unless((int) $drone->tenant_id === $tenantId, 403);

        $data = $request->validate([
            'name'                => ['required', 'string', 'max:200'],
            'model'               => ['nullable', 'string', 'max:200'],
            'serial_number'       => ['nullable', 'string', 'max:100'],
            'status'              => ['required', 'in:active,maintenance,retired'],
            'default_altitude_m'  => ['required', 'numeric', 'min:1', 'max:500'],
            'default_speed_ms'    => ['required', 'numeric', 'min:0.5', 'max:30'],
            'default_overlap_pct' => ['required', 'integer', 'min:0', 'max:95'],
            'default_spacing_m'   => ['required', 'numeric', 'min:1', 'max:100'],
            'default_buffer_m'    => ['required', 'numeric', 'min:0', 'max:50'],
            'notes'               => ['nullable', 'string', 'max:2000'],
        ]);

        $drone->update($data);

        return back()->with('success', '"' . $drone->name . '" updated.');
    }

    public function destroy(Drone $drone)
    {
        $tenantId = app(CurrentTenant::class)->id();
        abort_unless((int) $drone->tenant_id === $tenantId, 403);

        $drone->delete();

        return back()->with('success', 'Drone removed.');
    }
}
