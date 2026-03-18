<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CropCategory extends Model
{
    // Global reference table — no tenant scope
    protected $fillable = ['name'];

    public function crops(): HasMany
    {
        return $this->hasMany(Crop::class, 'category_id');
    }
}
