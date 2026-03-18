<?php

namespace App\Http\Controllers\Plus;

use App\Http\Controllers\Controller;
use App\Models\Sensor;
use App\Models\SensorRule;
use App\Models\SensorRuleLog;
use App\Services\CurrentTenant;
use Illuminate\Http\Request;

class SensorRuleController extends Controller
{
    public function index(CurrentTenant $currentTenant)
    {
        $rules = SensorRule::query()
            ->with(['triggerSensor', 'conditionSensor'])
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->paginate(20);

        // Eager-load the 3 most-recent log entries per rule for the index cards
        $recentLogs = SensorRuleLog::query()
            ->whereIn('sensor_rule_id', $rules->pluck('id'))
            ->orderByDesc('evaluated_at')
            ->get()
            ->groupBy('sensor_rule_id')
            ->map(fn($group) => $group->take(3));

        $sensors = Sensor::query()->orderBy('group_key')->orderBy('name')->get();

        return view('plus.iot.rules.index', [
            'rules'      => $rules,
            'recentLogs' => $recentLogs,
            'sensors'    => $sensors,
        ]);
    }

    public function create()
    {
        $sensors = Sensor::query()->orderBy('group_key')->orderBy('name')->get();

        return view('plus.iot.rules.create', [
            'sensors' => $sensors,
            'rule'    => null,
        ]);
    }

    public function store(Request $request, CurrentTenant $currentTenant)
    {
        $data = $this->validated($request);

        $tenantId = $currentTenant->id();
        if (!$tenantId) {
            return redirect()->route('tenant.select')
                ->with('error', 'Please select a tenant before creating a rule.');
        }

        SensorRule::query()->create(array_merge($data, ['tenant_id' => $tenantId]));

        return redirect()->route('plus.iot.rules.index')
            ->with('status', 'Rule "' . $data['name'] . '" created successfully.');
    }

    public function edit(SensorRule $rule)
    {
        $sensors = Sensor::query()->orderBy('group_key')->orderBy('name')->get();

        return view('plus.iot.rules.edit', [
            'rule'    => $rule,
            'sensors' => $sensors,
        ]);
    }

    public function update(Request $request, SensorRule $rule)
    {
        $data = $this->validated($request);
        $rule->update($data);

        return redirect()->route('plus.iot.rules.index')
            ->with('status', 'Rule "' . $rule->name . '" updated successfully.');
    }

    public function destroy(SensorRule $rule)
    {
        $name = $rule->name;
        $rule->delete();

        return redirect()->route('plus.iot.rules.index')
            ->with('status', "Rule \"{$name}\" deleted.");
    }

    public function toggleActive(SensorRule $rule)
    {
        $rule->update(['is_active' => !$rule->is_active]);

        $state = $rule->fresh()->is_active ? 'enabled' : 'disabled';
        return redirect()->route('plus.iot.rules.index')
            ->with('status', "Rule \"{$rule->name}\" {$state}.");
    }

    /**
     * Full execution log for a single rule (paginated).
     */
    public function logs(SensorRule $rule)
    {
        $logs = SensorRuleLog::query()
            ->where('sensor_rule_id', $rule->id)
            ->orderByDesc('evaluated_at')
            ->paginate(30);

        return view('plus.iot.rules.logs', [
            'rule' => $rule,
            'logs' => $logs,
        ]);
    }

    // ── Validation ────────────────────────────────────────────────────────────

    private function validated(Request $request): array
    {
        return $request->validate([
            'name'                       => ['required', 'string', 'max:150'],
            'description'                => ['nullable', 'string', 'max:500'],
            'is_active'                  => ['nullable', 'boolean'],
            // Trigger
            'trigger_type'               => ['required', 'in:time,sensor_threshold'],
            'trigger_time'               => ['nullable', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'],
            'trigger_sensor_id'          => ['nullable', 'integer', 'exists:sensors,id'],
            'trigger_operator'           => ['nullable', 'in:lt,gt,lte,gte,eq'],
            'trigger_threshold'          => ['nullable', 'numeric'],
            // Weather condition
            'weather_condition_enabled'  => ['nullable', 'boolean'],
            'weather_rain_skip_pct'      => ['nullable', 'integer', 'min:1', 'max:100'],
            // Sensor condition
            'sensor_condition_enabled'   => ['nullable', 'boolean'],
            'condition_sensor_id'        => ['nullable', 'integer', 'exists:sensors,id'],
            'condition_operator'         => ['nullable', 'in:lt,gt,lte,gte,eq'],
            'condition_threshold'        => ['nullable', 'numeric'],
            // Action
            'action_type'                => ['required', 'in:log,notify,both'],
            'action_notes'               => ['nullable', 'string', 'max:500'],
            // Retry
            'retry_type'                 => ['required', 'in:wait_window,next_scheduled,both'],
            'retry_wait_minutes'         => ['nullable', 'integer', 'min:1', 'max:1440'],
        ], [], [
            'weather_rain_skip_pct' => 'rain skip threshold (%)',
            'retry_wait_minutes'    => 'retry wait (minutes)',
        ]);
    }
}
