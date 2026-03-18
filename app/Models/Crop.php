<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Crop extends Model
{
    // Global reference table — no tenant scope
    protected $fillable = [
        'category_id',
        'name',
        'scientific_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(CropCategory::class, 'category_id');
    }

    public function cropTypes(): HasMany
    {
        return $this->hasMany(CropType::class, 'crop_id');
    }
}
