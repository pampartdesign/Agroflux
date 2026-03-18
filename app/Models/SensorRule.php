<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasTenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SensorRule extends Model
{
    use BelongsToTenant, HasTenantScope;

    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'is_active',
        // Trigger
        'trigger_type',
        'trigger_time',
        'trigger_sensor_id',
        'trigger_operator',
        'trigger_threshold',
        // Weather condition
        'weather_condition_enabled',
        'weather_rain_skip_pct',
        // Sensor condition
        'sensor_condition_enabled',
        'condition_sensor_id',
        'condition_operator',
        'condition_threshold',
        // Action
        'action_type',
        'action_notes',
        // Retry
        'retry_type',
        'retry_wait_minutes',
        // Tracking
        'last_triggered_at',
        'last_status',
        'next_retry_at',
    ];

    protected $casts = [
        'is_active'                  => 'boolean',
        'weather_condition_enabled'  => 'boolean',
        'sensor_condition_enabled'   => 'boolean',
        'trigger_threshold'          => 'decimal:2',
        'condition_threshold'        => 'decimal:2',
        'last_triggered_at'          => 'datetime',
        'next_retry_at'              => 'datetime',
    ];

    public function triggerSensor(): BelongsTo
    {
        return $this->belongsTo(Sensor::class, 'trigger_sensor_id');
    }

    public function conditionSensor(): BelongsTo
    {
        return $this->belongsTo(Sensor::class, 'condition_sensor_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    public function triggerLabel(): string
    {
        if ($this->trigger_type === 'time') {
            return 'Every day at ' . $this->trigger_time;
        }

        if ($this->trigger_sensor_id && $this->triggerSensor) {
            $opLabel = self::operatorLabel($this->trigger_operator);
            return "{$this->triggerSensor->name} {$opLabel} {$this->trigger_threshold} {$this->triggerSensor->unit}";
        }

        return '—';
    }

    public function statusBadge(): array
    {
        return match($this->last_status) {
            'executed'        => ['label' => 'Executed',        'class' => 'bg-emerald-50 text-emerald-700 border-emerald-100'],
            'skipped_weather' => ['label' => 'Skipped (rain)',  'class' => 'bg-blue-50 text-blue-700 border-blue-100'],
            'skipped_sensor'  => ['label' => 'Skipped (sensor)','class' => 'bg-amber-50 text-amber-700 border-amber-100'],
            'waiting_retry'   => ['label' => 'Waiting retry',   'class' => 'bg-violet-50 text-violet-700 border-violet-100'],
            'retried'         => ['label' => 'Retried',         'class' => 'bg-teal-50 text-teal-700 border-teal-100'],
            default           => ['label' => 'Never run',       'class' => 'bg-slate-50 text-slate-500 border-slate-200'],
        };
    }

    public static function operatorLabel(?string $op): string
    {
        return match($op) {
            'lt'  => '<',
            'gt'  => '>',
            'lte' => '≤',
            'gte' => '≥',
            'eq'  => '=',
            default => '?',
        };
    }
}
