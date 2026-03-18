<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantMember extends Model
{
    protected $table = 'tenant_users';

    protected $fillable = [
        'tenant_id',
        'user_id',
        'role',
        'permissions',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    /**
     * All module keys the platform supports, with human labels.
     */
    public static function allModules(): array
    {
        return [
            'farm'         => 'Farm Management',
            'livestock'    => 'Livestock Management',
            'water'        => 'Water Management',
            'core'         => 'Products, Listings & Orders',
            'traceability' => 'Traceability',
            'iot_sim'      => 'IoT Simulator',
            'iot'          => 'IoT Real Sensors',
            'equipment'    => 'Equipment',
            'inventory'    => 'Inventory',
            'logi'         => 'Logistics & Delivery',
            'drone'        => 'Drones & Field Mapping',
            'marketplace'  => 'Marketplace',
            'members'      => 'Organization Members',
        ];
    }

    /**
     * null permissions = inherit from plan (unrestricted within plan).
     * An explicit array restricts the member to only those modules.
     */
    public function hasModuleAccess(string $module): bool
    {
        if ($this->permissions === null) {
            return true;
        }

        return in_array($module, $this->permissions, true);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
