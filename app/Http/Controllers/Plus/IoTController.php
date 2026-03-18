<?php

namespace App\Http\Controllers\Plus;

use App\Http\Controllers\Controller;
use App\Http\Requests\SensorReadingRequest;
use App\Http\Requests\SensorRequest;
use App\Models\Alert;
use App\Models\Sensor;
use App\Models\SensorReading;
use App\Models\SensorRule;
use App\Models\SensorRuleLog;
use App\Services\CurrentTenant;
use Illuminate\Http\Request;

class IoTController extends Controller
{
    public function dashboard(CurrentTenant $currentTenant)
    {
        $sensors = Sensor::query()
            ->orderBy('group_key')
            ->orderBy('name')
            ->get();

        // Attach latest reading to each sensor
        $sensors->each(function ($sensor) {
            $sensor->latestReading = SensorReading::query()
                ->where('sensor_id', $sensor->id)
                ->orderByDesc('recorded_at')
                ->first();
        });

        // ── 24h trend datasets per sensor group ───────────────────────────────
        $since  = now()->subHours(24);
        $colors = ['#10b981', '#0ea5e9', '#f59e0b', '#8b5cf6', '#f43f5e', '#ec4899'];

        $groupCharts = [];
        foreach ($sensors->groupBy('group_key') as $groupKey => $groupSensors) {
            $datasets = [];
            foreach ($groupSensors->values() as $i => $sensor) {
                $readings = SensorReading::query()
                    ->where('sensor_id', $sensor->id)
                    ->where('recorded_at', '>=', $since)
                    ->orderBy('recorded_at')
                    ->get(['recorded_at', 'value']);

                if ($readings->isEmpty()) continue;

                $datasets[] = [
                    'label'           => $sensor->name,
                    'data'            => $readings->map(fn($r) => [
                        'x' => $r->recorded_at->format('H:i'),
                        'y' => round((float) $r->value, 1),
                    ])->values()->toArray(),
                    'borderColor'     => $colors[$i % count($colors)],
                    'backgroundColor' => $colors[$i % count($colors)] . '20',
                    'tension'         => 0.4,
                    'fill'            => true,
                    'pointRadius'     => 0,
                    'borderWidth'     => 2,
                ];
            }
            $groupCharts[$groupKey] = [
                'datasets' => $datasets,
                'hasData'  => count($datasets) > 0,
            ];
        }

        return view('plus.iot.dashboard', [
            'sensors'       => $sensors,
            'sensorCount'   => $sensors->count(),
            'onlineCount'   => $sensors->where('status', 'online')->count(),
            'alertCount'    => Alert::query()->whereNull('resolved_at')->count(),
            'latestReadings'=> SensorReading::query()->with('sensor')->orderByDesc('recorded_at')->limit(5)->get(),
            'groupCharts'   => $groupCharts,
        ]);
    }

    public function sensors()
    {
        $sensors = Sensor::query()->orderBy('group_key')->orderBy('name')->paginate(20);

        $rules = SensorRule::query()
            ->with(['triggerSensor', 'conditionSensor'])
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        // Most-recent log entry per rule (for the inline "Last Run" column)
        $lastLogs = SensorRuleLog::query()
            ->whereIn('sensor_rule_id', $rules->pluck('id'))
            ->orderByDesc('evaluated_at')
            ->get()
            ->groupBy('sensor_rule_id')
            ->map(fn($group) => $group->first());

        return view('plus.iot.sensors.index', [
            'sensors'  => $sensors,
            'rules'    => $rules,
            'lastLogs' => $lastLogs,
        ]);
    }

    public function createSensor()
    {
        return view('plus.iot.sensors.create');
    }

    public function updateSensor(Request $request, Sensor $sensor)
    {
        $data = $request->validate([
            'name'   => ['required', 'string', 'max:200'],
            'status' => ['required', 'in:online,offline'],
        ]);

        $sensor->update($data);

        return redirect()->route('plus.iot.sensors.index')->with('status', 'Sensor updated successfully.');
    }

    public function storeSensor(SensorRequest $request, CurrentTenant $currentTenant)
    {
        $tenantId = $currentTenant->id();

        if (!$tenantId) {
            return redirect()->route('tenant.select')
                ->with('error', 'Please select a tenant before adding a sensor.');
        }

        Sensor::query()->create(array_merge(
            $request->validated(),
            ['tenant_id' => $tenantId]
        ));
        return redirect()->route('plus.iot.sensors.index')->with('status', 'Sensor added successfully.');
    }

    public function simulator()
    {
        return view('plus.iot.simulator', [
            'sensors' => Sensor::query()->orderBy('group_key')->orderBy('name')->get(),
        ]);
    }

    public function ping(Request $request)
    {
        $data = $request->validate([
            'sensor_id' => ['required', 'integer', 'exists:sensors,id'],
        ]);

        $sensor = Sensor::query()->findOrFail($data['sensor_id']);

        // Simulator ping: flip status to online and create a sample reading
        $sensor->update(['status' => 'online']);

        SensorReading::query()->create([
            'tenant_id' => $sensor->tenant_id,
            'sensor_id' => $sensor->id,
            'value' => random_int(10, 90),
            'payload' => ['simulated' => true],
            'is_manual' => false,
            'recorded_at' => now(),
        ]);

        return back();
    }

    public function manualEntry()
    {
        return view('plus.iot.manual-entry', [
            'sensors' => Sensor::query()->orderBy('group_key')->orderBy('name')->get(),
        ]);
    }

    public function storeManualEntry(SensorReadingRequest $request)
    {
        $sensor = Sensor::query()->findOrFail($request->integer('sensor_id'));

        SensorReading::query()->create([
            'tenant_id' => $sensor->tenant_id,
            'sensor_id' => $sensor->id,
            'value' => $request->input('value'),
            'payload' => ['manual' => true],
            'is_manual' => true,
            'recorded_at' => $request->date('recorded_at'),
        ]);

        return redirect()->route('plus.iot.dashboard');
    }
}
