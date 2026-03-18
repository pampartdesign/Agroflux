<?php

namespace App\Http\Controllers\Equipment;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    public function index(Request $request)
    {
        $equipment = Equipment::orderBy('name')->get();

        $total       = $equipment->count();
        $operational = $equipment->where('status', 'operational')->count();
        $maintenance = $equipment->where('status', 'maintenance')->count();
        $outOfService= $equipment->where('status', 'out_of_service')->count();

        return view('equipment.index', compact(
            'equipment', 'total', 'operational', 'maintenance', 'outOfService'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'            => ['required', 'string', 'max:200'],
            'category'        => ['required', 'string', 'max:50'],
            'serial'          => ['nullable', 'string', 'max:100'],
            'status'          => ['required', 'in:operational,maintenance,out_of_service'],
            'purchased_at'    => ['nullable', 'date'],
            'next_service_at' => ['nullable', 'date'],
            'notes'           => ['nullable', 'string', 'max:1000'],
        ]);

        Equipment::create($data);

        return redirect()->route('equipment.index')->with('success', 'Equipment added.');
    }

    public function update(Request $request, Equipment $equipment)
    {
        $data = $request->validate([
            'name'            => ['required', 'string', 'max:200'],
            'category'        => ['required', 'string', 'max:50'],
            'serial'          => ['nullable', 'string', 'max:100'],
            'status'          => ['required', 'in:operational,maintenance,out_of_service'],
            'purchased_at'    => ['nullable', 'date'],
            'next_service_at' => ['nullable', 'date'],
            'notes'           => ['nullable', 'string', 'max:1000'],
        ]);

        $equipment->update($data);

        return redirect()->route('equipment.index')->with('success', 'Equipment updated.');
    }

    public function destroy(Equipment $equipment)
    {
        $equipment->delete();

        return redirect()->route('equipment.index')->with('success', 'Equipment removed.');
    }
}
