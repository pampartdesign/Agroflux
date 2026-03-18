<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasTenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LivestockAnimal extends Model
{
    use BelongsToTenant, HasTenantScope;

    protected $table = 'livestock_animals';

    protected $fillable = [
        'tenant_id',
        'tag',
        'species',
        'breed',
        'gender',
        'dob',
        'status',
        'notes',
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    public function produceLogs(): HasMany
    {
        return $this->hasMany(LivestockProduceLog::class, 'animal_id');
    }

    public function routineChecks(): HasMany
    {
        return $this->hasMany(LivestockRoutineCheck::class, 'animal_id');
    }

    public function statusLabel(): string
    {
        return match($this->status) {
            'active'   => 'Active',
            'pregnant' => 'Pregnant',
            'sick'     => 'Sick',
            'sold'     => 'Sold',
            default    => ucfirst($this->status),
        };
    }
}
