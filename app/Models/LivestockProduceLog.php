<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasTenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LivestockProduceLog extends Model
{
    use BelongsToTenant, HasTenantScope;

    protected $table = 'livestock_produce_logs';

    protected $fillable = [
        'tenant_id',
        'animal_id',
        'type',
        'quantity',
        'unit',
        'logged_at',
        'notes',
    ];

    protected $casts = [
        'logged_at' => 'date',
        'quantity'  => 'decimal:2',
    ];

    public function animal(): BelongsTo
    {
        return $this->belongsTo(LivestockAnimal::class, 'animal_id');
    }
}
