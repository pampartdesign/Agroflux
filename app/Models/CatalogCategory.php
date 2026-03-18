<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CatalogCategory extends Model
{
    protected $fillable = ['name', 'slug', 'parent_id'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(CatalogCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(CatalogCategory::class, 'parent_id');
    }

    /** Products that have this as their top-level category */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    /** Products that have this as their sub-category */
    public function subProducts(): HasMany
    {
        return $this->hasMany(Product::class, 'subcategory_id');
    }
}
