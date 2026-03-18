<?php

namespace App\Http\Controllers\Drone;

use App\Http\Controllers\Controller;
use App\Models\Drone;
use App\Models\DroneMission;
use App\Models\DroneWaypoint;
use App\Models\FieldBoundary;
use App\Services\CurrentTenant;
use Illuminate\Http\Request;

class DroneMissionController extends Controller
{
    public function index()
    {
        $missions = DroneMission::with(['boundary', 'drone'])
            ->orderByDesc('updated_at')
            ->get();

        return view('drone.missions.index', compact('missions'));
    }

    public function plan(Request $request, ?DroneMission $mission = null)
    {
        // Ownership check when editing an existing mission
        if ($mission) {
            $tenantId = app(CurrentTenant::class)->id();
            abort_unless((int) $mission->tenant_id === $tenantId, 403);
        }

        $boundaries  = FieldBoundary::orderBy('name')->get();
        $drones      = Drone::where('status', 'active')->orderBy('name')->get();
        $mapboxToken = config('services.mapbox.public_token', '');

        return view('drone.missions.plan', compact('mission', 'boundaries', 'drones', 'mapboxToken'));
    }

    public function store(Request $request)
    {
        $tenantId = app(CurrentTenant::class)->id();
        abort_unless($tenantId, 403);

        $data = $this->validateMission($request);
        $data['tenant_id'] = $tenantId;

        $mission = DroneMission::create($data);

        // Sync waypoints if provided
        if ($request->filled('waypoints_json')) {
            $this->syncWaypoints($mission, $request->input('waypoints_json'));
        }

        if ($request->expectsJson()) {
            return response()->json(['id' => $mission->id, 'message' => 'Mission saved.']);
        }

        return redirect()->route('drone.missions.index')->with('success', '"' . $mission->name . '" created.');
    }

    public function update(Request $request, DroneMission $mission)
    {
        $tenantId = app(CurrentTenant::class)->id();
        abort_unless((int) $mission->tenant_id === $tenantId, 403);

        $data = $this->validateMission($request);
        $mission->update($data);

        if ($request->filled('waypoints_json')) {
            $this->syncWaypoints($mission, $request->input('waypoints_json'));
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Mission updated.']);
        }

        return redirect()->route('drone.missions.index')->with('success', '"' . $mission->name . '" updated.');
    }

    public function updateStatus(Request $request, DroneMission $mission)
    {
        $tenantId = app(CurrentTenant::class)->id();
        abort_unless((int) $mission->tenant_id === $tenantId, 403);

        $data = $request->validate([
            'status' => ['required', 'in:draft,planned,in_progress,completed,aborted'],
        ]);

        $mission->update($data);

        return back()->with('success', 'Mission status updated.');
    }

    public function destroy(DroneMission $mission)
    {
        $tenantId = app(CurrentTenant::class)->id();
        abort_unless((int) $mission->tenant_id === $tenantId, 403);

        $mission->waypoints()->delete();
        $mission->delete();

        return back()->with('success', 'Mission deleted.');
    }

    public function export(DroneMission $mission, string $format)
    {
        $tenantId = app(CurrentTenant::class)->id();
        abort_unless((int) $mission->tenant_id === $tenantId, 403);
        abort_unless(in_array($format, ['geojson', 'kml']), 400, 'Unsupported format');

        // Primary source: coordinates stored in waypoints_geojson (LineString)
        $coordinates = $this->coordinatesFromGeojson($mission->waypoints_geojson);

        // Fallback: drone_waypoints table rows (legacy / manual sync)
        if (empty($coordinates)) {
            $coordinates = $mission->waypoints()->orderBy('sequence')->get()
                ->map(fn($wp) => [(float)$wp->longitude, (float)$wp->latitude, (float)$wp->altitude_m])
                ->values()->toArray();
        }

        if ($format === 'geojson') {
            return $this->exportGeoJson($mission, $coordinates);
        }

        return $this->exportKml($mission, $coordinates);
    }

    // ── Private helpers ──────────────────────────────────────────────────────

    /**
     * Extract [[lng, lat, alt?], ...] coordinate array from a stored GeoJSON string.
     * Handles both LineString geometry and Feature-wrapped geometry.
     */
    private function coordinatesFromGeojson(?string $geojson): array
    {
        if (!$geojson) return [];
        try {
            $geo = json_decode($geojson, true);
            if (!$geo) return [];
            // Feature wrapper
            if (($geo['type'] ?? '') === 'Feature') {
                $geo = $geo['geometry'] ?? [];
            }
            // LineString
            if (($geo['type'] ?? '') === 'LineString' && !empty($geo['coordinates'])) {
                return $geo['coordinates'];
            }
        } catch (\Throwable $e) {}
        return [];
    }

    private function validateMission(Request $request): array
    {
        return $request->validate([
            'name'               => ['required', 'string', 'max:200'],
            'field_boundary_id'  => ['nullable', 'integer'],
            'drone_id'           => ['nullable', 'integer'],
            'mission_type'       => ['required', 'in:spray,imaging,survey,routine_monitoring,irrigation_issue_check,soil_moisture_followup,crop_stress_investigation,pest_disease_scouting,drainage_waterlogging_check,post_weather_damage_check,planting_emergence_check,pre_harvest_review,boundary_mapping,spray_operation_review,manual_custom_request'],
            'status'             => ['required', 'in:draft,planned,in_progress,completed,aborted'],
            'altitude_m'         => ['required', 'numeric', 'min:1', 'max:500'],
            'speed_ms'           => ['required', 'numeric', 'min:0.5', 'max:30'],
            'spacing_m'          => ['required', 'numeric', 'min:1', 'max:200'],
            'angle_deg'          => ['required', 'integer', 'min:0', 'max:359'],
            'overlap_pct'        => ['required', 'integer', 'min:0', 'max:95'],
            'buffer_m'           => ['required', 'numeric', 'min:0', 'max:100'],
            'waypoints_geojson'  => ['nullable', 'string'],
            'notes'              => ['nullable', 'string', 'max:2000'],
            'planned_at'         => ['nullable', 'date'],
        ]);
    }

    private function syncWaypoints(DroneMission $mission, string $json): void
    {
        $points = json_decode($json, true);
        if (!is_array($points)) return;

        $mission->waypoints()->delete();
        $seq = 1;
        foreach ($points as $pt) {
            DroneWaypoint::create([
                'mission_id'  => $mission->id,
                'sequence'    => $seq++,
                'latitude'    => $pt['lat'] ?? $pt['latitude'],
                'longitude'   => $pt['lng'] ?? $pt['longitude'],
                'altitude_m'  => $pt['altitude_m'] ?? $mission->altitude_m,
                'action'      => $pt['action'] ?? 'waypoint',
            ]);
        }
    }

    private function exportGeoJson(DroneMission $mission, array $coordinates): \Symfony\Component\HttpFoundation\Response
    {
        // Ensure altitude is included (index 2); default to mission altitude if missing
        $alt = (float)$mission->altitude_m;
        $coords3d = array_map(fn($c) => [
            (float)$c[0],
            (float)$c[1],
            isset($c[2]) ? (float)$c[2] : $alt,
        ], $coordinates);

        $geojson = [
            'type' => 'FeatureCollection',
            'features' => [
                [
                    'type' => 'Feature',
                    'geometry' => [
                        'type'        => 'LineString',
                        'coordinates' => $coords3d,
                    ],
                    'properties' => [
                        'name'         => $mission->name,
                        'mission_type' => $mission->mission_type,
                        'altitude_m'   => $alt,
                        'speed_ms'     => (float)$mission->speed_ms,
                        'waypoints'    => count($coords3d),
                    ],
                ],
            ],
        ];

        $filename = 'mission_' . $mission->id . '_' . str_replace(' ', '_', $mission->name) . '.geojson';

        return response(json_encode($geojson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), 200, [
            'Content-Type'        => 'application/geo+json',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function exportKml(DroneMission $mission, array $coordinates): \Symfony\Component\HttpFoundation\Response
    {
        $alt = (float)$mission->altitude_m;
        $placemarks = '';
        foreach ($coordinates as $i => $c) {
            $lng    = (float)$c[0];
            $lat    = (float)$c[1];
            $wpAlt  = isset($c[2]) ? (float)$c[2] : $alt;
            $seq    = $i + 1;
            $placemarks .= sprintf(
                '<Placemark><name>WP%d</name><Point><coordinates>%.7f,%.7f,%.1f</coordinates></Point></Placemark>' . "\n",
                $seq, $lng, $lat, $wpAlt
            );
        }

        $kml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<kml xmlns="http://www.opengis.net/kml/2.2">' . "\n"
            . '<Document>' . "\n"
            . '<name>' . htmlspecialchars($mission->name) . '</name>' . "\n"
            . '<description>Type: ' . $mission->mission_type . ' | Alt: ' . $mission->altitude_m . 'm | Speed: ' . $mission->speed_ms . 'm/s</description>' . "\n"
            . $placemarks
            . '</Document></kml>';

        $filename = 'mission_' . $mission->id . '_' . str_replace(' ', '_', $mission->name) . '.kml';

        return response($kml, 200, [
            'Content-Type'        => 'application/vnd.google-earth.kml+xml',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
