<?php

namespace App\Http\Controllers\Water;

use App\Http\Controllers\Controller;
use App\Models\Sensor;
use App\Models\SensorReading;
use App\Services\CurrentTenant;
use Illuminate\Http\Request;

class WaterDashboardController extends Controller
{
    public function index(Request $request, CurrentTenant $currentTenant)
    {
        // ── Moisture sensors (humidity + water-level) ─────────────────────────
        $moistureSensors = Sensor::query()
            ->whereIn('group_key', ['humidity', 'trough_level'])
            ->orderBy('name')
            ->get();

        $moistureSensors->each(function ($sensor) {
            $sensor->latestReading = SensorReading::query()
                ->where('sensor_id', $sensor->id)
                ->orderByDesc('recorded_at')
                ->first();
        });

        // ── Irrigation controllers ────────────────────────────────────────────
        $irrigationSensors = Sensor::query()
            ->where('group_key', 'irrigation')
            ->orderBy('name')
            ->get();

        $irrigationSensors->each(function ($sensor) {
            $sensor->latestReading = SensorReading::query()
                ->where('sensor_id', $sensor->id)
                ->orderByDesc('recorded_at')
                ->first();
        });

        // ── KPIs ──────────────────────────────────────────────────────────────
        $latestValues = $moistureSensors
            ->map(fn($s) => $s->latestReading?->value)
            ->filter()
            ->values();

        $avgMoisture    = $latestValues->count() ? round((float) $latestValues->avg(), 1) : null;
        $activeSensors  = $moistureSensors->merge($irrigationSensors)->where('status', 'online')->count();
        $controllerCount = $irrigationSensors->count();

        // Water used: sum of latest trough_level readings (as a rough proxy)
        $troughReadings = $moistureSensors
            ->where('group_key', 'trough_level')
            ->map(fn($s) => $s->latestReading?->value)
            ->filter();
        $waterUsed = $troughReadings->count() ? round((float) $troughReadings->sum(), 1) : null;

        // ── 24-hour trend data for Chart.js ──────────────────────────────────
        $since  = now()->subHours(24);
        $colors = ['#10b981', '#0ea5e9', '#f59e0b', '#8b5cf6', '#f43f5e', '#ec4899'];

        $datasets = [];
        foreach ($moistureSensors as $i => $sensor) {
            $readings = SensorReading::query()
                ->where('sensor_id', $sensor->id)
                ->where('recorded_at', '>=', $since)
                ->orderBy('recorded_at')
                ->get(['recorded_at', 'value']);

            if ($readings->isEmpty()) {
                continue;
            }

            $datasets[] = [
                'label'           => $sensor->name,
                'data'            => $readings->map(fn($r) => [
                    'x' => $r->recorded_at->format('H:i'),
                    'y' => round((float) $r->value, 2),
                ])->values()->toArray(),
                'borderColor'     => $colors[$i % count($colors)],
                'backgroundColor' => $colors[$i % count($colors)] . '20',
                'tension'         => 0.35,
                'fill'            => false,
                'pointRadius'     => 3,
            ];
        }

        $chartData    = ['datasets' => $datasets];
        $hasChartData = count($datasets) > 0;

        // ── 24-hour hourly heatmap (average moisture per hour across all moisture sensors) ──
        $moistureIds    = $moistureSensors->pluck('id');
        $hourlyAverages = collect();

        if ($moistureIds->count()) {
            $hourlyAverages = SensorReading::query()
                ->whereIn('sensor_id', $moistureIds)
                ->where('recorded_at', '>=', $since)
                ->get(['recorded_at', 'value'])
                ->groupBy(fn($r) => $r->recorded_at->format('Y-m-d H'))
                ->map(fn($g) => (int) round((float) $g->avg('value')));
        }

        $heatmap = [];
        for ($h = 23; $h >= 0; $h--) {
            $slot      = now()->subHours($h);
            $heatmap[] = [
                'label' => $slot->format('H:00'),
                'value' => $hourlyAverages->get($slot->format('Y-m-d H')),
            ];
        }
        $hasHeatmapData = $hourlyAverages->isNotEmpty();

        return view('water.dashboard', compact(
            'moistureSensors',
            'irrigationSensors',
            'avgMoisture',
            'activeSensors',
            'controllerCount',
            'waterUsed',
            'chartData',
            'hasChartData',
            'heatmap',
            'hasHeatmapData'
        ));
    }
}
