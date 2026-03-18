<?php

namespace App\Http\Controllers\Drone;

use App\Http\Controllers\Controller;
use App\Models\Field;
use App\Models\FieldBoundary;
use App\Services\CurrentTenant;
use Illuminate\Http\Request;

class FieldBoundaryController extends Controller
{
    public function index()
    {
        $boundaries = FieldBoundary::with('field')
            ->orderByDesc('updated_at')
            ->get();

        return view('drone.fields.index', compact('boundaries'));
    }

    public function map(Request $request, ?FieldBoundary $boundary = null)
    {
        $fields     = Field::orderBy('name')->get();
        $boundaries = FieldBoundary::with('field')->orderBy('name')->get();
        $mapboxToken = config('services.mapbox.public_token', '');

        return view('drone.fields.map', compact('boundary', 'fields', 'boundaries', 'mapboxToken'));
    }

    public function store(Request $request)
    {
        $tenantId = app(CurrentTenant::class)->id();
        abort_unless($tenantId, 403);

        $data = $request->validate([
            'name'         => ['required', 'string', 'max:200'],
            'field_id'     => ['nullable', 'integer'],
            'geojson'      => ['required', 'string'],
            'area_ha'      => ['nullable', 'numeric'],
            'centroid_lat' => ['nullable', 'numeric'],
            'centroid_lng' => ['nullable', 'numeric'],
            'perimeter_m'  => ['nullable', 'numeric'],
            'notes'        => ['nullable', 'string', 'max:2000'],
        ]);

        // Validate GeoJSON structure
        $geo = json_decode($data['geojson'], true);
        abort_unless($geo && isset($geo['type']), 422, 'Invalid GeoJSON');

        $data['tenant_id'] = $tenantId;
        $boundary = FieldBoundary::create($data);

        if ($request->expectsJson()) {
            return response()->json(['id' => $boundary->id, 'message' => 'Boundary saved.']);
        }

        return redirect()->route('drone.fields.index')->with('success', '"' . $boundary->name . '" saved.');
    }

    public function update(Request $request, FieldBoundary $boundary)
    {
        $tenantId = app(CurrentTenant::class)->id();
        abort_unless((int) $boundary->tenant_id === $tenantId, 403);

        $data = $request->validate([
            'name'         => ['required', 'string', 'max:200'],
            'field_id'     => ['nullable', 'integer'],
            'geojson'      => ['required', 'string'],
            'area_ha'      => ['nullable', 'numeric'],
            'centroid_lat' => ['nullable', 'numeric'],
            'centroid_lng' => ['nullable', 'numeric'],
            'perimeter_m'  => ['nullable', 'numeric'],
            'notes'        => ['nullable', 'string', 'max:2000'],
        ]);

        $geo = json_decode($data['geojson'], true);
        abort_unless($geo && isset($geo['type']), 422, 'Invalid GeoJSON');

        $boundary->update($data);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Boundary updated.']);
        }

        return redirect()->route('drone.fields.index')->with('success', '"' . $boundary->name . '" updated.');
    }

    public function destroy(FieldBoundary $boundary)
    {
        $tenantId = app(CurrentTenant::class)->id();
        abort_unless((int) $boundary->tenant_id === $tenantId, 403);

        $boundary->delete();

        return back()->with('success', 'Field boundary deleted.');
    }
}
