@extends('layouts.app')

@section('title', isset($boundary) ? __('drone.draw_title_edit', ['name' => $boundary->name]) : __('drone.draw_title_new'))

@push('head')
<link rel="stylesheet" href="https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.css">
<script src="https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.js"></script>
<script src="https://unpkg.com/@turf/turf@7.1.0/turf.min.js"></script>
<style>
    #field-map { height: calc(100vh - 220px); min-height: 480px; border-radius: 0.75rem; }
    /* Custom draw toolbar */
    .draw-toolbar { position:absolute; top:10px; left:10px; z-index:10; display:flex; flex-direction:column; gap:4px; }
    .draw-btn {
        background:#fff; border:1px solid #d1d5db; border-radius:8px;
        width:36px; height:36px; display:flex; align-items:center; justify-content:center;
        cursor:pointer; box-shadow:0 1px 3px rgba(0,0,0,.15); transition:all .15s;
        font-size:16px; title:attr(title);
    }
    .draw-btn:hover { background:#f0fdf4; border-color:#16a34a; }
    .draw-btn.active { background:#16a34a; color:#fff; border-color:#16a34a; }
    .draw-btn:disabled { opacity:.4; cursor:not-allowed; }
</style>
@endpush

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                {{ isset($boundary) ? __('drone.draw_title_edit', ['name' => $boundary->name]) : __('drone.draw_title_new') }}
            </h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('drone.draw_subtitle') }}</p>
        </div>
        <a href="{{ route('drone.fields.index') }}"
           class="text-sm text-gray-600 hover:text-gray-900 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            {{ __('drone.back_to_field_maps') }}
        </a>
    </div>

    <div class="flex gap-4 items-start">

        {{-- ── Sidebar ──────────────────────────────────────────────────────── --}}
        <div class="w-80 flex-shrink-0 bg-white rounded-xl border border-gray-200 shadow-sm p-4 space-y-4">

            {{-- Load existing --}}
            @if($boundaries->count())
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('drone.label_load_existing') }}</label>
                <select id="boundary-selector" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
                    <option value="">{{ __('drone.select_to_load') }}</option>
                    @foreach($boundaries as $b)
                        <option value="{{ $b->id }}"
                                data-url="{{ route('drone.fields.map.edit', $b) }}"
                                {{ (isset($boundary) && $boundary->id === $b->id) ? 'selected' : '' }}>
                            {{ $b->name }}@if($b->area_ha) ({{ number_format($b->area_ha, 1) }} ha)@endif
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            {{-- Form --}}
            <form id="field-form" method="POST"
                  action="{{ isset($boundary) ? route('drone.fields.update', $boundary) : route('drone.fields.store') }}">
                @csrf
                @if(isset($boundary)) @method('PUT') @endif
                <input type="hidden" name="geojson"      id="geojson-input">
                <input type="hidden" name="area_ha"      id="area-input">
                <input type="hidden" name="centroid_lat" id="centroid-lat-input">
                <input type="hidden" name="centroid_lng" id="centroid-lng-input">
                <input type="hidden" name="perimeter_m"  id="perimeter-input">

                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">
                        {{ __('drone.label_field_name') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="field-name"
                           value="{{ old('name', $boundary->name ?? '') }}"
                           placeholder="{{ __('drone.placeholder_field_name') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500" required>
                </div>

                <div class="mt-3">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">{{ __('drone.label_notes_field') }}</label>
                    <textarea name="notes" rows="2"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500"
                              placeholder="{{ __('drone.placeholder_notes_field') }}">{{ old('notes', $boundary->notes ?? '') }}</textarea>
                </div>

                {{-- Stats --}}
                <div class="grid grid-cols-3 gap-2 mt-3">
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-2.5 text-center">
                        <div class="text-xl font-bold text-gray-900" id="stat-area">—</div>
                        <div class="text-xs text-gray-500 mt-0.5">{{ __('drone.stat_hectares') }}</div>
                    </div>
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-2.5 text-center">
                        <div class="text-xl font-bold text-gray-900" id="stat-perim">—</div>
                        <div class="text-xs text-gray-500 mt-0.5">{{ __('drone.stat_perimeter') }}</div>
                    </div>
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-2.5 text-center">
                        <div class="text-xl font-bold text-gray-900 text-sm" id="stat-pts">—</div>
                        <div class="text-xs text-gray-500 mt-0.5">{{ __('drone.stat_vertices') }}</div>
                    </div>
                </div>
                <div id="centroid-info" class="hidden mt-1 text-xs text-gray-500">
                    <span class="font-medium">{{ __('drone.centroid_label') }}</span> <span id="centroid-text"></span>
                </div>

                {{-- No-polygon warning (shown before any polygon is drawn) --}}
                <div id="no-polygon-warning" class="{{ isset($boundary) ? 'hidden' : '' }} mt-3 flex items-start gap-2 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2 text-xs text-amber-800">
                    <svg class="w-3.5 h-3.5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                    {{ __('drone.draw_polygon_first') }}
                </div>

                <div class="flex gap-2 mt-3">
                    <button type="button" id="btn-clear"
                            class="flex-1 text-sm font-medium border border-gray-300 text-gray-700 hover:bg-gray-50 px-3 py-2 rounded-lg transition">
                        {{ __('drone.btn_clear') }}
                    </button>
                    <button type="submit" id="btn-save"
                            class="flex-1 text-sm font-semibold bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg transition shadow-sm">
                        {{ isset($boundary) ? __('drone.btn_update_field') : __('drone.btn_save_field') }}
                    </button>
                </div>

                @error('name')    <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                @error('geojson') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </form>

            {{-- Instructions --}}
            <div class="border-t pt-3">
                <p class="text-xs font-semibold text-gray-600 mb-2">{{ __('drone.how_to_draw') }}</p>
                <ul class="text-xs text-gray-500 space-y-1.5" id="draw-instructions">
                    <li class="flex items-start gap-1.5"><span class="text-green-500 mt-0.5">①</span> Click <strong>✏ Draw</strong> to start</li>
                    <li class="flex items-start gap-1.5"><span class="text-green-500 mt-0.5">②</span> Click on the map to add corner points</li>
                    <li class="flex items-start gap-1.5"><span class="text-green-500 mt-0.5">③</span> Click <strong>first point</strong> again to close the polygon</li>
                    <li class="flex items-start gap-1.5"><span class="text-green-500 mt-0.5">④</span> Or click <strong>⊙ Finish</strong> to complete</li>
                    <li class="flex items-start gap-1.5"><span class="text-green-500 mt-0.5">⑤</span> Drag points to adjust, then <strong>{{ __('drone.btn_save_field') }}</strong></li>
                </ul>
            </div>
        </div>

        {{-- ── Map ──────────────────────────────────────────────────────────── --}}
        <div class="flex-1 min-w-0 relative">
            <div id="field-map"></div>

            {{-- Custom draw toolbar overlaid on map --}}
            <div class="draw-toolbar" id="draw-toolbar">
                <button class="draw-btn" id="btn-draw-poly" title="Draw polygon">✏️</button>
                <button class="draw-btn" id="btn-finish" title="Finish polygon" disabled>⊙</button>
                <button class="draw-btn" id="btn-undo" title="Undo last point" disabled>↩</button>
                <button class="draw-btn" id="btn-delete-last" title="Delete selected polygon" disabled>🗑</button>
            </div>

            {{-- Map status bar --}}
            <div id="map-status"
                 class="absolute bottom-3 left-1/2 -translate-x-1/2 bg-black/60 text-white text-xs px-3 py-1.5 rounded-full pointer-events-none hidden">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    const MAPBOX_TOKEN = '{{ $mapboxToken }}';
    const EXISTING_GEO = @json(isset($boundary) && $boundary->geojson ? json_decode($boundary->geojson) : null);

    // ── Map init ──────────────────────────────────────────────────────────────
    const map = new maplibregl.Map({
        container: 'field-map',
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

    // ── State ─────────────────────────────────────────────────────────────────
    let drawing   = false;
    let drawPoints = [];
    let savedPolygon = null;
    let markers = [];
    let previewLine = null;

    // ── Status bar helper ─────────────────────────────────────────────────────
    function status(msg, duration = 3000) {
        const el = document.getElementById('map-status');
        el.textContent = msg;
        el.classList.remove('hidden');
        clearTimeout(el._t);
        if (duration) el._t = setTimeout(() => el.classList.add('hidden'), duration);
    }

    // ── Draw toolbar buttons ──────────────────────────────────────────────────
    const btnDraw   = document.getElementById('btn-draw-poly');
    const btnFinish = document.getElementById('btn-finish');
    const btnUndo   = document.getElementById('btn-undo');
    const btnDel    = document.getElementById('btn-delete-last');

    function setDrawing(on) {
        drawing = on;
        btnDraw.classList.toggle('active', on);
        btnFinish.disabled = !on;
        btnUndo.disabled   = !on;
        map.getCanvas().style.cursor = on ? 'crosshair' : '';
        if (on) status('Click on the map to add polygon points. Click first point to close.', 0);
        else    status('');
    }

    btnDraw.addEventListener('click', () => {
        if (drawing) { finishPolygon(); return; }
        drawPoints = [];
        clearMarkers();
        setDrawing(true);
    });

    btnFinish.addEventListener('click', () => finishPolygon());

    btnUndo.addEventListener('click', () => {
        if (!drawPoints.length) return;
        drawPoints.pop();
        const last = markers.pop();
        if (last) last.remove();
        updatePreview();
    });

    btnDel.addEventListener('click', () => {
        clearPolygon();
        resetStats();
    });

    // ── Map click — add draw point ────────────────────────────────────────────
    map.on('click', (e) => {
        if (!drawing) return;
        const pt = [e.lngLat.lng, e.lngLat.lat];

        if (drawPoints.length >= 3) {
            const firstPx = map.project(drawPoints[0]);
            const clickPx = map.project(pt);
            const dist = Math.hypot(firstPx.x - clickPx.x, firstPx.y - clickPx.y);
            if (dist < 20) { finishPolygon(); return; }
        }

        drawPoints.push(pt);
        addMarker(pt, drawPoints.length === 1);
        updatePreview();
        status(`${drawPoints.length} point(s) added. Click first point or ⊙ Finish to close.`, 0);
    });

    // ── Mouse move — rubber-band line ─────────────────────────────────────────
    map.on('mousemove', (e) => {
        if (!drawing || drawPoints.length === 0) return;
        const coords = [...drawPoints, [e.lngLat.lng, e.lngLat.lat]];
        updatePreview(coords);
    });

    // ── Add a vertex marker ───────────────────────────────────────────────────
    function addMarker(lngLat, isFirst) {
        const el = document.createElement('div');
        el.style.cssText = `
            width:${isFirst ? 14 : 10}px; height:${isFirst ? 14 : 10}px;
            border-radius:50%; cursor:${isFirst ? 'pointer' : 'default'};
            background:${isFirst ? '#16a34a' : '#fff'};
            border:2px solid #16a34a; box-shadow:0 1px 3px rgba(0,0,0,.4);
        `;
        if (isFirst) {
            el.title = 'Click to close polygon';
            el.addEventListener('click', (ev) => { ev.stopPropagation(); finishPolygon(); });
        }
        const marker = new maplibregl.Marker({ element: el, anchor: 'center' })
            .setLngLat(lngLat)
            .addTo(map);
        markers.push(marker);
    }

    function clearMarkers() {
        markers.forEach(m => m.remove());
        markers = [];
    }

    // ── Update rubber-band preview line ───────────────────────────────────────
    function updatePreview(coords) {
        const pts = coords ?? drawPoints;
        if (pts.length < 2) {
            if (map.getSource('preview')) map.getSource('preview').setData({ type: 'FeatureCollection', features: [] });
            return;
        }
        const data = { type: 'Feature', geometry: { type: 'LineString', coordinates: pts }, properties: {} };
        if (!map.getSource('preview')) {
            map.addSource('preview', { type: 'geojson', data });
            map.addLayer({ id: 'preview-line', type: 'line', source: 'preview',
                paint: { 'line-color': '#16a34a', 'line-width': 2, 'line-dasharray': [3, 2] } });
        } else {
            map.getSource('preview').setData(data);
        }
    }

    // ── Finish / close polygon ────────────────────────────────────────────────
    function finishPolygon() {
        if (drawPoints.length < 3) {
            status('Need at least 3 points to make a polygon.', 3000);
            return;
        }
        setDrawing(false);
        clearMarkers();

        const ring = [...drawPoints, drawPoints[0]];
        const feature = turf.polygon([ring]);
        savedPolygon = feature;

        renderPolygon(feature);
        updateStats(feature);
        btnDel.disabled = false;
        drawPoints = [];

        if (map.getSource('preview')) map.getSource('preview').setData({ type: 'FeatureCollection', features: [] });
        status('Polygon saved. Drag vertices to adjust, then click Save Field.', 4000);
    }

    // ── Render saved polygon ──────────────────────────────────────────────────
    function renderPolygon(feature) {
        const geom = feature.type === 'Feature' ? feature : { type: 'Feature', geometry: feature, properties: {} };

        ['poly-fill','poly-line','poly-vertices'].forEach(id => {
            if (map.getLayer(id)) map.removeLayer(id);
            if (map.getSource(id)) map.removeSource(id);
        });

        map.addSource('poly-fill', { type: 'geojson', data: geom });
        map.addLayer({ id: 'poly-fill', type: 'fill', source: 'poly-fill',
            paint: { 'fill-color': '#22c55e', 'fill-opacity': 0.2 } });
        map.addLayer({ id: 'poly-line', type: 'line', source: 'poly-fill',
            paint: { 'line-color': '#16a34a', 'line-width': 2.5 } });

        const coords = geom.geometry.coordinates[0].slice(0, -1);
        const pts = turf.featureCollection(coords.map(c => turf.point(c)));
        map.addSource('poly-vertices', { type: 'geojson', data: pts });
        map.addLayer({ id: 'poly-vertices', type: 'circle', source: 'poly-vertices',
            paint: { 'circle-radius': 5, 'circle-color': '#fff', 'circle-stroke-width': 2, 'circle-stroke-color': '#16a34a' } });
    }

    // ── Clear polygon ─────────────────────────────────────────────────────────
    function clearPolygon() {
        savedPolygon = null;
        ['poly-fill','poly-line','poly-vertices','preview','preview-line'].forEach(id => {
            if (map.getLayer(id)) map.removeLayer(id);
            if (map.getSource(id)) map.removeSource(id);
        });
        clearMarkers();
        drawPoints = [];
        setDrawing(false);
        btnDel.disabled = true;
    }

    // ── Stats ─────────────────────────────────────────────────────────────────
    function updateStats(feature) {
        try {
            const areaM2   = turf.area(feature);
            const areaHa   = areaM2 / 10000;
            const perimM   = turf.length(feature, { units: 'meters' });
            const centroid = turf.centroid(feature);
            const [lng, lat] = centroid.geometry.coordinates;
            const coords   = feature.geometry?.coordinates?.[0] ?? [];

            document.getElementById('stat-area').textContent  = areaHa.toFixed(2);
            document.getElementById('stat-perim').textContent = Math.round(perimM);
            document.getElementById('stat-pts').textContent   = coords.length - 1;
            document.getElementById('centroid-text').textContent = lat.toFixed(5) + ', ' + lng.toFixed(5);
            document.getElementById('centroid-info').classList.remove('hidden');

            document.getElementById('geojson-input').value      = JSON.stringify(feature.geometry);
            document.getElementById('area-input').value         = areaHa.toFixed(6);
            document.getElementById('centroid-lat-input').value = lat.toFixed(8);
            document.getElementById('centroid-lng-input').value = lng.toFixed(8);
            document.getElementById('perimeter-input').value    = Math.round(perimM);

            document.getElementById('no-polygon-warning').classList.add('hidden');
        } catch(e) { console.warn('Stats error', e); }
    }

    function resetStats() {
        ['stat-area','stat-perim','stat-pts'].forEach(id => document.getElementById(id).textContent = '—');
        document.getElementById('centroid-info').classList.add('hidden');
        document.getElementById('no-polygon-warning').classList.remove('hidden');
        document.getElementById('geojson-input').value = '';
    }

    // ── Load existing polygon on map load ─────────────────────────────────────
    map.on('load', () => {
        if (EXISTING_GEO) {
            const feature = EXISTING_GEO.type === 'Feature'
                ? EXISTING_GEO
                : { type: 'Feature', geometry: EXISTING_GEO, properties: {} };
            savedPolygon = feature;
            renderPolygon(feature);
            updateStats(feature);
            btnDel.disabled = false;
            const bbox = turf.bbox(feature);
            map.fitBounds([[bbox[0], bbox[1]], [bbox[2], bbox[3]]], { padding: 60, duration: 800 });
        } else {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(pos => {
                    map.flyTo({ center: [pos.coords.longitude, pos.coords.latitude], zoom: 15 });
                }, () => {});
            }
        }
    });

    // ── Form submit guard ─────────────────────────────────────────────────────
    document.getElementById('field-form').addEventListener('submit', (e) => {
        const geojson = document.getElementById('geojson-input').value;
        if (!geojson) {
            e.preventDefault();
            document.getElementById('no-polygon-warning').classList.remove('hidden');
            document.getElementById('no-polygon-warning').scrollIntoView({ behavior: 'smooth', block: 'center' });
            status('Please draw a field boundary on the map first.', 4000);
            return false;
        }
        const name = document.getElementById('field-name').value.trim();
        if (!name) {
            e.preventDefault();
            document.getElementById('field-name').focus();
            status('Please enter a field name.', 3000);
            return false;
        }
    });

    // ── Clear button ──────────────────────────────────────────────────────────
    document.getElementById('btn-clear').addEventListener('click', () => {
        clearPolygon();
        resetStats();
    });

    // ── Boundary selector → navigate ──────────────────────────────────────────
    const sel = document.getElementById('boundary-selector');
    if (sel) {
        sel.addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            if (opt.dataset.url) window.location.href = opt.dataset.url;
        });
    }

})();
</script>
@endpush
