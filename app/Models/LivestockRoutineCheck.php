<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasTenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LivestockRoutineCheck extends Model
{
    use BelongsToTenant, HasTenantScope;

    protected $table = 'livestock_routine_checks';

    protected $fillable = [
        'tenant_id',
        'animal_id',
        'type',
        'status',
        'checked_at',
        'notes',
    ];

    protected $casts = [
        'checked_at' => 'date',
    ];

    public function animal(): BelongsTo
    {
        return $this->belongsTo(LivestockAnimal::class, 'animal_id');
    }
}
