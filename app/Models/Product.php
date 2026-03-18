<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasTenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use BelongsToTenant, HasTenantScope;

    protected $fillable = [
        'tenant_id',
        'category_id',
        'subcategory_id',
        'sku',
        'default_name',
        'default_description',
        'unit',
        'stock_status',
        'inventory',
        'unit_price',
        'image_path',
    ];

    protected $casts = [
        'inventory'  => 'decimal:3',
        'unit_price' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(CatalogCategory::class, 'category_id');
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(CatalogCategory::class, 'subcategory_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ProductTranslation::class);
    }

    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class);
    }
}
