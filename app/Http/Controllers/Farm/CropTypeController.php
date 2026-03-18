<?php

namespace App\Http\Controllers\Farm;

use App\Http\Controllers\Controller;
use App\Models\Crop;
use App\Models\CropCategory;
use App\Models\CropType;
use App\Services\CurrentTenant;
use Illuminate\Http\Request;

class CropTypeController extends Controller
{
    public function index()
    {
        $cropTypes = CropType::with('crop.category', 'fields.farm')->orderBy('name')->get();

        // Global crops grouped by category for the crop dropdown
        $cropsByCategory = CropCategory::with(['crops' => function ($q) {
            $q->where('is_active', true)->orderBy('name');
        }])->orderBy('name')->get();

        return view('farm.crop-types.index', compact('cropTypes', 'cropsByCategory'));
    }

    public function store(Request $request)
    {
        $tenantId = app(CurrentTenant::class)->id();

        $data = $request->validate([
            'crop_id'               => ['required', 'integer', 'exists:crops,id'],
            'min_soil_moisture_pct' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'min_daily_water_lt'    => ['nullable', 'numeric', 'min:0'],
            'min_temperature_c'     => ['nullable', 'numeric', 'min:-50', 'max:60'],
            'max_temperature_c'     => ['nullable', 'numeric', 'min:-50', 'max:60'],
            'min_soil_ph'           => ['nullable', 'numeric', 'min:0', 'max:14'],
            'max_soil_ph'           => ['nullable', 'numeric', 'min:0', 'max:14'],
            'min_sunlight_h'        => ['nullable', 'numeric', 'min:0', 'max:24'],
            'growing_days'          => ['nullable', 'integer', 'min:1', 'max:1825'],
            'notes'                 => ['nullable', 'string', 'max:2000'],
        ]);

        $data['name']      = Crop::find($data['crop_id'])?->name;
        $data['tenant_id'] = $tenantId;

        CropType::create($data);

        return back()->with('success', 'Crop profile added successfully.');
    }

    public function update(Request $request, CropType $cropType)
    {
        $tenantId = app(CurrentTenant::class)->id();

        if ($tenantId && (int) $cropType->tenant_id !== $tenantId) {
            return redirect()->route('farm.crop-types.index')
                ->with('error', 'You do not have permission to edit this crop profile.');
        }

        $data = $request->validate([
            'crop_id'               => ['required', 'integer', 'exists:crops,id'],
            'min_soil_moisture_pct' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'min_daily_water_lt'    => ['nullable', 'numeric', 'min:0'],
            'min_temperature_c'     => ['nullable', 'numeric', 'min:-50', 'max:60'],
            'max_temperature_c'     => ['nullable', 'numeric', 'min:-50', 'max:60'],
            'min_soil_ph'           => ['nullable', 'numeric', 'min:0', 'max:14'],
            'max_soil_ph'           => ['nullable', 'numeric', 'min:0', 'max:14'],
            'min_sunlight_h'        => ['nullable', 'numeric', 'min:0', 'max:24'],
            'growing_days'          => ['nullable', 'integer', 'min:1', 'max:1825'],
            'notes'                 => ['nullable', 'string', 'max:2000'],
        ]);

        $data['name'] = Crop::find($data['crop_id'])?->name;

        $cropType->update($data);

        return back()->with('success', 'Crop profile updated.');
    }

    public function destroy(CropType $cropType)
    {
        $tenantId = app(CurrentTenant::class)->id();

        if ($tenantId && (int) $cropType->tenant_id !== $tenantId) {
            return redirect()->route('farm.crop-types.index')
                ->with('error', 'You do not have permission to remove this crop profile.');
        }

        // Prevent deletion if fields are using this crop profile
        if ($cropType->fields()->count() > 0) {
            return back()->with('error',
                'Cannot remove "' . ($cropType->crop?->name ?? $cropType->name) . '" — it is assigned to one or more fields. ' .
                'Please reassign or remove those fields first.'
            );
        }

        $cropType->delete();

        return back()->with('success', 'Crop profile removed.');
    }
}
