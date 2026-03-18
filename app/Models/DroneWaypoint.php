<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DroneWaypoint extends Model
{
    protected $fillable = [
        'mission_id',
        'sequence',
        'latitude',
        'longitude',
        'altitude_m',
        'action',
    ];

    protected $casts = [
        'latitude'  => 'decimal:7',
        'longitude' => 'decimal:7',
        'altitude_m'=> 'decimal:1',
        'sequence'  => 'integer',
    ];

    public function mission(): BelongsTo
    {
        return $this->belongsTo(DroneMission::class);
    }
}
