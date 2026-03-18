<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasTenantScope;
use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    use BelongsToTenant, HasTenantScope;

    protected $table = 'inventory_items';

    protected $fillable = [
        'tenant_id',
        'name',
        'category',
        'quantity',
        'unit',
        'min_qty',
        'supplier',
        'expires_at',
        'notes',
    ];

    protected $casts = [
        'quantity'   => 'float',
        'min_qty'    => 'float',
        'expires_at' => 'date',
    ];

    public function isLowStock(): bool
    {
        return $this->min_qty !== null && $this->quantity > 0 && $this->quantity <= $this->min_qty;
    }

    public function isOutOfStock(): bool
    {
        return $this->quantity <= 0;
    }
}
