<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SensorRuleLog extends Model
{
    protected $fillable = [
        'sensor_rule_id',
        'tenant_id',
        'status',
        'summary',
        'context',
        'evaluated_at',
    ];

    protected $casts = [
        'context'      => 'array',
        'evaluated_at' => 'datetime',
    ];

    public function rule(): BelongsTo
    {
        return $this->belongsTo(SensorRule::class, 'sensor_rule_id');
    }

    public function statusBadge(): array
    {
        return match($this->status) {
            'executed'        => ['label' => 'Executed',         'class' => 'bg-emerald-50 text-emerald-700 border-emerald-100'],
            'skipped_weather' => ['label' => 'Skipped (rain)',   'class' => 'bg-sky-50 text-sky-700 border-sky-100'],
            'skipped_sensor'  => ['label' => 'Skipped (sensor)', 'class' => 'bg-amber-50 text-amber-700 border-amber-100'],
            'waiting_retry'   => ['label' => 'Waiting retry',    'class' => 'bg-violet-50 text-violet-700 border-violet-100'],
            'retried'         => ['label' => 'Retried',          'class' => 'bg-teal-50 text-teal-700 border-teal-100'],
            default           => ['label' => ucfirst($this->status), 'class' => 'bg-slate-50 text-slate-500 border-slate-200'],
        };
    }
}
