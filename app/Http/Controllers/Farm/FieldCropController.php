<?php

namespace App\Http\Controllers\Farm;

use App\Http\Controllers\Controller;
use App\Models\CropType;
use App\Models\Farm;
use App\Models\Field;
use App\Services\CurrentTenant;
use Illuminate\Http\Request;

class FieldCropController extends Controller
{
    public function index(Request $request)
    {
        $fields    = Field::with('farm', 'cropType.crop')->orderBy('farm_id')->orderBy('name')->get();
        $farms     = Farm::orderBy('name')->get();
        $cropTypes = CropType::with('crop')->orderBy('name')->get(); // Tenant's own crop profiles

        $totalFields      = $fields->count();
        $activeFields     = $fields->where('status', 'active')->count();
        $totalHectares    = $fields->sum('area_ha');
        $harvestsThisYear = $fields->where('status', 'harvested')
            ->filter(fn ($f) => $f->harvest_at && $f->harvest_at->year === now()->year)
            ->count();

        return view('farm.field-crop.index', compact(
            'fields', 'farms', 'cropTypes',
            'totalFields', 'activeFields', 'totalHectares', 'harvestsThisYear'
        ));
    }

    public function store(Request $request)
    {
        $tenantId = app(CurrentTenant::class)->id();

        // Guard: tenant must have crop profiles before registering fields
        if (CropType::count() === 0) {
            return redirect()->route('farm.crop-types.index')
                ->with('error', 'Please add your crop profiles in Crop Management before registering fields.');
        }

        $data = $request->validate([
            'farm_id'      => ['required', 'integer', 'exists:farms,id'],
            'name'         => ['required', 'string', 'max:150'],
            'area_ha'      => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'crop_type_id' => ['nullable', 'integer', 'exists:crop_types,id'],
            'status'       => ['required', 'in:active,fallow,harvested,prep'],
            'planted_at'   => ['nullable', 'date'],
            'harvest_at'   => ['nullable', 'date'],
            'notes'        => ['nullable', 'string', 'max:2000'],
        ]);

        $farm = Farm::withoutGlobalScopes()->findOrFail($data['farm_id']);

        if ($tenantId && (int) $farm->tenant_id !== $tenantId) {
            return back()
                ->withInput()
                ->with('error', 'The selected farm does not belong to your account.');
        }

        // Verify the selected crop profile belongs to this tenant
        if (!empty($data['crop_type_id'])) {
            $cropType = CropType::find($data['crop_type_id']); // HasTenantScope auto-filters
            if (!$cropType) {
                return back()
                    ->withInput()
                    ->with('error', 'The selected crop profile does not belong to your account.');
            }
            // Denormalise crop name for dashboard chart
            $data['crop_type'] = $cropType->crop?->name ?? $cropType->name;
        }

        $data['tenant_id'] = $farm->tenant_id;

        Field::create($data);

        return back()->with('success', 'Field added successfully.');
    }

    public function update(Request $request, Field $field)
    {
        $tenantId = app(CurrentTenant::class)->id();

        if ($tenantId && (int) $field->tenant_id !== $tenantId) {
            return redirect()->route('farm.fields.index')
                ->with('error', 'You do not have permission to edit this field.');
        }

        $data = $request->validate([
            'farm_id'      => ['required', 'integer', 'exists:farms,id'],
            'name'         => ['required', 'string', 'max:150'],
            'area_ha'      => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'crop_type_id' => ['nullable', 'integer', 'exists:crop_types,id'],
            'status'       => ['required', 'in:active,fallow,harvested,prep'],
            'planted_at'   => ['nullable', 'date'],
            'harvest_at'   => ['nullable', 'date'],
            'notes'        => ['nullable', 'string', 'max:2000'],
        ]);

        // Denormalise crop name
        if (!empty($data['crop_type_id'])) {
            $cropType          = CropType::find($data['crop_type_id']);
            $data['crop_type'] = $cropType?->crop?->name ?? $cropType?->name;
        } else {
            $data['crop_type'] = null;
        }

        $field->update($data);

        return back()->with('success', 'Field updated successfully.');
    }

    public function destroy(Field $field)
    {
        $tenantId = app(CurrentTenant::class)->id();

        if ($tenantId && (int) $field->tenant_id !== $tenantId) {
            return redirect()->route('farm.fields.index')
                ->with('error', 'You do not have permission to remove this field.');
        }

        $field->delete();

        return back()->with('success', 'Field removed.');
    }
}
