@extends('layouts.app')

@section('title', $mission ? __('drone.plan_title_edit', ['name' => $mission->name]) : __('drone.plan_title_new'))

@push('head')
<link rel="stylesheet" href="https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.css">
<script src="https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.js"></script>
<script src="https://unpkg.com/@turf/turf@7.1.0/turf.min.js"></script>
<style>
    #mission-map { height: calc(100vh - 210px); min-height: 500px; border-radius: 0.75rem; }
    .p-lbl { display:block; font-size:.75rem; font-weight:600; color:#4b5563; text-transform:uppercase; letter-spacing:.05em; margin-bottom:.25rem; }
    .p-inp { width:100%; border:1px solid #d1d5db; border-radius:.5rem; padding:.5rem .75rem; font-size:.875rem; }
    .p-inp:focus { outline:none; ring:2px solid #22c55e; border-color:#22c55e; }
</style>
@endpush

@section('content')
<div class="space-y-4">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                {{ $mission ? __('drone.plan_title_edit', ['name' => $mission->name]) : __('drone.plan_title_new') }}
            </h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('drone.plan_subtitle') }}</p>
        </div>
        <div class="flex items-center gap-2">
            @if($mission)
            <a href="{{ route('drone.missions.plan') }}"
               class="inline-flex items-center gap-1.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-3 py-2 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('drone.btn_new_mission') }}
            </a>
            @endif
            <a href="{{ route('drone.missions.index') }}"
               class="text-sm text-gray-600 hover:text-gray-900 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ __('drone.btn_all_missions') }}
            </a>
        </div>
    </div>

    <div class="flex gap-4 items-start">

        {{-- ── Sidebar ──────────────────────────────────────────────────────── --}}
        <div class="w-80 flex-shrink-0 space-y-4">

            {{-- Mission details --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                <h3 class="text-sm font-bold text-gray-800 mb-3">{{ __('drone.section_mission_details') }}</h3>
                <form id="mission-form" method="POST"
                      action="{{ isset($mission) ? route('drone.missions.update', $mission) : route('drone.missions.store') }}"
                      class="space-y-3">
                    @csrf
                    @if(isset($mission)) @method('PUT') @endif

                    {{-- Hidden computed fields --}}
                    <input type="hidden" name="waypoints_geojson" id="waypoints-input">
                    <input type="hidden" name="estimated_duration_minutes" id="duration-input">
                    <input type="hidden" name="status" id="mission-status" value="{{ old('status', $mission?->status ?? 'draft') }}">

                    <div>
                        <label class="p-lbl">{{ __('drone.label_mission_name') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="name"
                               value="{{ old('name', $mission?->name ?? '') }}"
                               class="p-inp" placeholder="{{ __('drone.placeholder_mission_name') }}" required>
                    </div>

                    <div>
                        <label class="p-lbl">{{ __('drone.label_field_boundary') }} <span class="text-red-500">*</span></label>
                        <select name="field_boundary_id" id="boundary-select" class="p-inp" required>
                            <option value="">{{ __('drone.select_field') }}</option>
                            @foreach($boundaries as $b)
                                <option value="{{ $b->id }}"
                                        data-geojson="{{ $b->geojson ?? '' }}"
                                        {{ old('field_boundary_id', $mission?->field_boundary_id ?? request('field_boundary_id')) == $b->id ? 'selected' : '' }}>
                                    {{ $b->name }}@if($b->area_ha) ({{ number_format($b->area_ha, 1) }} ha)@endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="p-lbl">{{ __('drone.label_drone_select') }}</label>
                        <select name="drone_id" id="drone-select" class="p-inp">
                            <option value="">{{ __('drone.select_drone_optional') }}</option>
                            @foreach($drones as $d)
                                <option value="{{ $d->id }}"
                                        data-altitude="{{ $d->default_altitude_m }}"
                                        data-speed="{{ $d->default_speed_ms }}"
                                        data-overlap="{{ $d->default_overlap_pct }}"
                                        data-spacing="{{ $d->default_spacing_m }}"
                                        data-buffer="{{ $d->default_buffer_m }}"
                                        {{ old('drone_id', $mission?->drone_id ?? '') == $d->id ? 'selected' : '' }}>
                                    {{ $d->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="p-lbl">{{ __('drone.label_mission_type') }}</label>
                        @php $mt = old('mission_type', $mission?->mission_type ?? 'survey'); @endphp
                        <select name="mission_type" class="p-inp">
                            @foreach([
                                'survey'                      => __('drone.type_survey'),
                                'spray'                       => __('drone.type_spray'),
                                'imaging'                     => __('drone.type_imaging'),
                                'routine_monitoring'          => __('drone.type_routine_monitoring'),
                                'irrigation_issue_check'      => __('drone.type_irrigation_issue_check'),
                                'soil_moisture_followup'      => __('drone.type_soil_moisture_followup'),
                                'crop_stress_investigation'   => __('drone.type_crop_stress_investigation'),
                                'pest_disease_scouting'       => __('drone.type_pest_disease_scouting'),
                                'drainage_waterlogging_check' => __('drone.type_drainage_waterlogging_check'),
                                'post_weather_damage_check'   => __('drone.type_post_weather_damage_check'),
                                'planting_emergence_check'    => __('drone.type_planting_emergence_check'),
                                'pre_harvest_review'          => __('drone.type_pre_harvest_review'),
                                'boundary_mapping'            => __('drone.type_boundary_mapping'),
                                'spray_operation_review'      => __('drone.type_spray_operation_review'),
                                'manual_custom_request'       => __('drone.type_manual_custom_request'),
                            ] as $val => $label)
                                <option value="{{ $val }}" {{ $mt === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="p-lbl">{{ __('drone.label_planned_datetime') }}</label>
                        <input type="datetime-local" name="planned_at"
                               value="{{ old('planned_at', $mission?->planned_at ? $mission->planned_at->format('Y-m-d\TH:i') : '') }}"
                               class="p-inp">
                    </div>

                    <div>
                        <label class="p-lbl">{{ __('drone.label_notes_mission') }}</label>
                        <textarea name="notes" rows="2" class="p-inp"
                                  placeholder="{{ __('drone.placeholder_notes_mission') }}">{{ old('notes', $mission?->notes ?? '') }}</textarea>
                    </div>
                </form>
            </div>

            {{-- Flight parameters --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                <h3 class="text-sm font-bold text-gray-800 mb-3">{{ __('drone.section_flight_params') }}</h3>
                <div class="space-y-3">
                    <div>
                        <label class="p-lbl">{{ __('drone.label_altitude') }}</label>
                        <input type="number" id="p-altitude" form="mission-form" name="altitude_m"
                               value="{{ old('altitude_m', $mission?->altitude_m ?? 50) }}"
                               min="10" max="400" step="5" class="p-inp">
                    </div>
                    <div>
                        <label class="p-lbl">{{ __('drone.label_speed') }}</label>
                        <input type="number" id="p-speed" form="mission-form" name="speed_ms"
                               value="{{ old('speed_ms', $mission?->speed_ms ?? 8) }}"
                               min="1" max="25" step="0.5" class="p-inp">
                    </div>
                    <div>
                        <label class="p-lbl">{{ __('drone.label_overlap') }}</label>
                        <input type="number" id="p-overlap" form="mission-form" name="overlap_pct"
                               value="{{ old('overlap_pct', $mission?->overlap_pct ?? 70) }}"
                               min="0" max="90" step="5" class="p-inp">
                    </div>
                    <div>
                        <label class="p-lbl">{{ __('drone.label_strip_spacing') }}</label>
                        <input type="number" id="p-spacing" form="mission-form" name="spacing_m"
                               value="{{ old('spacing_m', $mission?->spacing_m ?? 20) }}"
                               min="5" max="200" step="1" class="p-inp">
                    </div>
                    <div>
                        <label class="p-lbl">{{ __('drone.label_boundary_buffer') }}</label>
                        <input type="number" id="p-buffer" form="mission-form" name="buffer_m"
                               value="{{ old('buffer_m', $mission?->buffer_m ?? 5) }}"
                               min="0" max="50" step="1" class="p-inp">
                    </div>
                    <div>
                        <label class="p-lbl">{{ __('drone.label_sweep_angle') }}</label>
                        <input type="number" id="p-angle" form="mission-form" name="angle_deg"
                               value="{{ old('angle_deg', $mission?->angle_deg ?? 0) }}"
                               min="0" max="179" step="1" class="p-inp">
                        <p class="text-xs text-gray-400 mt-1">{{ __('drone.sweep_angle_hint') }}</p>
                    </div>
                </div>

                <button type="button" id="btn-generate"
                        class="mt-4 w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition">
                    {{ __('drone.btn_generate') }}
                </button>
            </div>


        </div>

        {{-- ── Map column ──────────────────────────────────────────────────── --}}
        <div class="flex-1 min-w-0 flex flex-col gap-3">

            {{-- Save button — always visible above map --}}
            <div style="background:#fff; border:1px solid #e5e7eb; border-radius:0.75rem; box-shadow:0 1px 3px rgba(0,0,0,.08); padding:12px;">
                <button type="submit" form="mission-form"
                        style="display:flex; align-items:center; justify-content:center; gap:8px; width:100%; background:#16a34a; color:#fff; font-size:0.875rem; font-weight:600; padding:10px 20px; border:none; border-radius:0.5rem; cursor:pointer;"
                        onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    {{ isset($mission) ? __('drone.btn_update_mission') : __('drone.btn_save_mission') }}
                </button>
            </div>

            {{-- Map --}}
            <div id="mission-map"></div>

            {{-- Flight stats — below map, revealed after Generate --}}
            <div id="stats-row" style="display:none"
                 class="bg-white rounded-xl border border-gray-200 shadow-sm px-4 py-3">
                <div style="display:flex; gap:8px;">
                    <div class="bg-blue-50 border border-blue-100 rounded-lg py-2 text-center" style="flex:1">
                        <div class="text-base font-bold text-blue-900" id="stat-waypoints">—</div>
                        <div class="text-xs text-blue-500">{{ __('drone.stat_wps') }}</div>
                    </div>
                    <div class="bg-blue-50 border border-blue-100 rounded-lg py-2 text-center" style="flex:1">
                        <div class="text-base font-bold text-blue-900" id="stat-dist">—</div>
                        <div class="text-xs text-blue-500">{{ __('drone.stat_km') }}</div>
                    </div>
                    <div class="bg-blue-50 border border-blue-100 rounded-lg py-2 text-center" style="flex:1">
                        <div class="text-base font-bold text-blue-900" id="stat-duration">—</div>
                        <div class="text-xs text-blue-500">{{ __('drone.stat_min') }}</div>
                    </div>
                    <div class="bg-blue-50 border border-blue-100 rounded-lg py-2 text-center" style="flex:1">
                        <div class="text-base font-bold text-blue-900" id="stat-strips">—</div>
                        <div class="text-xs text-blue-500">{{ __('drone.stat_strips') }}</div>
                    </div>
                </div>
            </div>

            {{-- Send to Drone — only on saved missions, below flight stats --}}
            @if(isset($mission))
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                <h3 class="text-sm font-bold text-gray-800 mb-1">{{ __('drone.section_send_to_drone') }}</h3>
                <p class="text-xs text-gray-400 mb-3">{{ __('drone.send_to_drone_desc') }}</p>
                <div style="display:flex; gap:8px;">
                    <a href="{{ route('drone.missions.export', [$mission->id, 'geojson']) }}"
                       style="flex:1; text-align:center; font-size:0.75rem; font-weight:600; background:#334155; color:#fff; padding:8px 12px; border-radius:0.5rem; text-decoration:none;"
                       onmouseover="this.style.background='#1e293b'" onmouseout="this.style.background='#334155'">
                        ↓ GeoJSON
                    </a>
                    <a href="{{ route('drone.missions.export', [$mission->id, 'kml']) }}"
                       style="flex:1; text-align:center; font-size:0.75rem; font-weight:600; background:#334155; color:#fff; padding:8px 12px; border-radius:0.5rem; text-decoration:none;"
                       onmouseover="this.style.background='#1e293b'" onmouseout="this.style.background='#334155'">
                        ↓ KML
                    </a>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const MAPBOX_TOKEN = '{{ $mapboxToken }}';

    // ── Map ──────────────────────────────────────────────────────────────────
    const map = new maplibregl.Map({
        container: 'mission-map',
        style: {
            version: 8,
            sources: {
                'mapbox-satellite': {
                    type: 'raster',
                    tiles: [`https://api.mapbox.com/v4/mapbox.satellite/{z}/{x}/{y}@2x.jpg90?access_token=${MAPBOX_TOKEN}`],
                    tileSize: 512,
                    maxzoom: 22,
                    attribution: '© <a href="https://www.mapbox.com/about/maps/">Mapbox</a> © <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                },
            },
            layers: [{ id: 'satellite', type: 'raster', source: 'mapbox-satellite' }],
        },
        center: [28.9784, 41.0082],
        zoom: 14,
    });
    map.addControl(new maplibregl.NavigationControl(), 'top-right');
    map.addControl(new maplibregl.ScaleControl({ maxWidth: 100, unit: 'metric' }), 'bottom-right');
    map.addControl(new maplibregl.FullscreenControl(), 'top-right');

    let fieldPolygon = null;

    // ── Helper: add/update a GeoJSON layer ───────────────────────────────────
    function upsertLayer(id, data, layerDef) {
        if (map.getLayer(id)) map.removeLayer(id);
        if (map.getSource(id)) map.removeSource(id);
        map.addSource(id, { type: 'geojson', data });
        map.addLayer({ id, source: id, ...layerDef });
    }

    // ── Load field polygon onto map ───────────────────────────────────────────
    function showField(geojsonStr) {
        if (!geojsonStr) return;
        try {
            const parsed = JSON.parse(geojsonStr);
            const feature = parsed.type === 'Feature'
                ? parsed
                : { type: 'Feature', geometry: parsed, properties: {} };
            fieldPolygon = feature;

            upsertLayer('field-fill', feature, {
                type: 'fill',
                paint: { 'fill-color': '#22c55e', 'fill-opacity': 0.15 },
            });
            upsertLayer('field-line', feature, {
                type: 'line',
                paint: { 'line-color': '#16a34a', 'line-width': 2.5 },
            });

            const bbox = turf.bbox(feature);
            map.fitBounds([[bbox[0], bbox[1]], [bbox[2], bbox[3]]], { padding: 80, duration: 800 });
        } catch(e) { console.warn('showField error', e); }
    }

    // ── Boundary selector ─────────────────────────────────────────────────────
    const bSel = document.getElementById('boundary-select');
    bSel.addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        if (opt.dataset.geojson) showField(opt.dataset.geojson);
    });

    // ── Drone selector — fill defaults ────────────────────────────────────────
    document.getElementById('drone-select').addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        if (opt.dataset.altitude) document.getElementById('p-altitude').value = opt.dataset.altitude;
        if (opt.dataset.speed)    document.getElementById('p-speed').value    = opt.dataset.speed;
        if (opt.dataset.overlap)  document.getElementById('p-overlap').value  = opt.dataset.overlap;
        if (opt.dataset.spacing)  document.getElementById('p-spacing').value  = opt.dataset.spacing;
        if (opt.dataset.buffer)   document.getElementById('p-buffer').value   = opt.dataset.buffer;
    });

    // ── On map load: restore pre-selected state ───────────────────────────────
    map.on('load', () => {
        // Restore field boundary for pre-selected option
        const selOpt = bSel.options[bSel.selectedIndex];
        if (selOpt && selOpt.dataset.geojson) {
            showField(selOpt.dataset.geojson);
        }

        // Restore existing waypoints if editing
        @if(isset($mission) && $mission->waypoints_geojson)
        try {
            const wGeo = @json(json_decode($mission->waypoints_geojson));
            if (wGeo) {
                const f = wGeo.type === 'Feature' ? wGeo : { type: 'Feature', geometry: wGeo, properties: {} };
                renderWaypoints(f);
                document.getElementById('stats-row').style.display = 'block';
            }
        } catch(e) { console.warn('Failed to load waypoints', e); }
        @endif
    });

    // ── Generate lawnmower pattern ────────────────────────────────────────────
    document.getElementById('btn-generate').addEventListener('click', generatePattern);

    function generatePattern() {
        if (!fieldPolygon) {
            const bSel2 = document.getElementById('boundary-select');
            bSel2.style.borderColor = '#ef4444';
            bSel2.focus();
            setTimeout(() => { bSel2.style.borderColor = ''; }, 2000);
            return;
        }

        const altitude = parseFloat(document.getElementById('p-altitude').value) || 50;
        const speed    = parseFloat(document.getElementById('p-speed').value)    || 8;
        const spacing  = parseFloat(document.getElementById('p-spacing').value)  || 20;
        const buffer   = parseFloat(document.getElementById('p-buffer').value)   || 0;
        const angle    = parseFloat(document.getElementById('p-angle').value)    || 0;

        // Shrink polygon by buffer
        let poly = fieldPolygon;
        if (buffer > 0) {
            try {
                const shrunk = turf.buffer(fieldPolygon, -(buffer / 1000), { units: 'kilometers' });
                if (shrunk) poly = shrunk;
            } catch(_) {}
        }

        const coords = lawnmower(poly, spacing, angle);

        if (coords.length < 2) {
            alert('Field too small for the given strip spacing. Try reducing the spacing value.');
            return;
        }

        const line = turf.lineString(coords, { altitude_m: altitude });
        renderWaypoints(line);

        const distKm   = turf.length(line, { units: 'kilometers' });
        const duration = Math.ceil((distKm * 1000) / speed / 60);
        const strips   = Math.ceil(coords.length / 2);

        document.getElementById('stat-waypoints').textContent = coords.length;
        document.getElementById('stat-dist').textContent      = distKm.toFixed(2);
        document.getElementById('stat-duration').textContent  = duration;
        document.getElementById('stat-strips').textContent    = strips;

        document.getElementById('waypoints-input').value = JSON.stringify(line.geometry);
        // Promote status to planned once a flight path is generated
        const statusEl = document.getElementById('mission-status');
        if (statusEl && statusEl.value === 'draft') statusEl.value = 'planned';
        document.getElementById('duration-input').value  = duration;

        // Reveal flight stats, hide the pre-generate hint
        document.getElementById('stats-row').style.display = 'block';
    }

    function renderWaypoints(featureOrGeom) {
        const feature = (featureOrGeom.type === 'Feature')
            ? featureOrGeom
            : { type: 'Feature', geometry: featureOrGeom, properties: {} };

        upsertLayer('mission-path', feature, {
            type: 'line',
            paint: { 'line-color': '#f59e0b', 'line-width': 2, 'line-dasharray': [4, 2] },
        });

        const coords = feature.geometry?.coordinates ?? [];
        if (!coords.length) return;

        const pts = turf.featureCollection(coords.map((c, i) => turf.point(c, { index: i })));
        upsertLayer('mission-pts', pts, {
            type: 'circle',
            paint: {
                'circle-radius': 3.5,
                'circle-color': '#f59e0b',
                'circle-stroke-width': 1.5,
                'circle-stroke-color': '#fff',
            },
        });

        // Start = green, End = red
        if (coords.length >= 2) {
            upsertLayer('wp-start', turf.point(coords[0]), {
                type: 'circle',
                paint: { 'circle-radius': 7, 'circle-color': '#22c55e', 'circle-stroke-width': 2, 'circle-stroke-color': '#fff' },
            });
            upsertLayer('wp-end', turf.point(coords[coords.length - 1]), {
                type: 'circle',
                paint: { 'circle-radius': 7, 'circle-color': '#ef4444', 'circle-stroke-width': 2, 'circle-stroke-color': '#fff' },
            });
        }
    }

    // ── Lawnmower algorithm ───────────────────────────────────────────────────
    // Returns array of [lng, lat] coordinate pairs forming a boustrophedon path
    function lawnmower(polygon, spacingM, angleDeg) {
        const bbox = turf.bbox(polygon);
        const cy   = (bbox[1] + bbox[3]) / 2;
        // Convert spacing from metres to degrees (latitude-based approximation)
        const spacingDeg = spacingM / 111320;

        const rad  = (angleDeg * Math.PI) / 180;
        const cosA = Math.cos(rad);
        const sinA = Math.sin(rad);

        // Half-diagonal in degrees — ensures strips cover the whole polygon after rotation
        const diagM = Math.sqrt(
            Math.pow((bbox[2] - bbox[0]) * 111320 * Math.cos(cy * Math.PI / 180), 2) +
            Math.pow((bbox[3] - bbox[1]) * 111320, 2)
        );
        const halfDeg = (diagM / 111320) / 2 + spacingDeg * 2;

        const cx = (bbox[0] + bbox[2]) / 2;

        const nStrips = Math.ceil((halfDeg * 2) / spacingDeg);
        const waypoints = [];

        for (let i = 0; i <= nStrips; i++) {
            const offset = -halfDeg + i * spacingDeg;

            // Strip start and end (in rotated frame, then unrotated)
            const p1 = [
                cx + sinA * offset - cosA * halfDeg,
                cy - cosA * offset - sinA * halfDeg,
            ];
            const p2 = [
                cx + sinA * offset + cosA * halfDeg,
                cy - cosA * offset + sinA * halfDeg,
            ];

            const clipped = clipLineToPoly([p1, p2], polygon);
            if (!clipped) continue;

            // Alternate direction (boustrophedon)
            if (i % 2 === 0) {
                waypoints.push(clipped[0], clipped[1]);
            } else {
                waypoints.push(clipped[1], clipped[0]);
            }
        }

        return waypoints;
    }

    function clipLineToPoly(seg, polygon) {
        try {
            const inside = seg.filter(pt =>
                turf.booleanPointInPolygon(turf.point(pt), polygon)
            );
            const line  = turf.lineString(seg);
            const xPts  = turf.lineIntersect(line, polygon);
            const extra = xPts.features.map(f => f.geometry.coordinates);
            const all   = [...inside, ...extra];
            if (all.length < 2) return null;
            // Sort along line direction from seg[0]
            const [ax, ay] = seg[0];
            all.sort((a, b) => (Math.hypot(a[0]-ax, a[1]-ay)) - (Math.hypot(b[0]-ax, b[1]-ay)));
            return [all[0], all[all.length - 1]];
        } catch(_) { return null; }
    }

})();
</script>
@endpush
