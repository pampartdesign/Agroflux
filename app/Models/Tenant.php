<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tenant extends Model
{
    protected $fillable = [
        'name',
        'plan_key',
        'trial_ends_at',
        'location_name',
        'lat',
        'lng',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'lat'           => 'float',
        'lng'           => 'float',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tenant_user')->withTimestamps();
    }

    public function farms(): HasMany
    {
        return $this->hasMany(Farm::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->latestOfMany();
    }
}
