<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasTenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FarmRoutineTask extends Model
{
    use BelongsToTenant, HasTenantScope;

    protected $table = 'farm_routine_tasks';

    protected $fillable = [
        'tenant_id',
        'field_id',
        'type',
        'status',
        'scheduled_at',
        'completed_at',
        'notes',
    ];

    protected $casts = [
        'scheduled_at' => 'date',
        'completed_at' => 'date',
    ];

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }
}
