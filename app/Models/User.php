<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Concerns\HasTenantMemberships;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;
    use HasTenantMemberships;

    protected $fillable = [
        'name',
        'surname',
        'company_name',
        'phone',
        'address',
        'zip_code',
        'country',
        'bank_name',
        'iban',
        'iris_number',
        'email',
        'password',
        'locale',
        'user_type',  // 'farmer' (default) | 'trucker'
    ];

    /** Trucker users are independent drivers — they don't need a farm org. */
    public function isTrucker(): bool
    {
        return $this->user_type === 'trucker';
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class, 'tenant_user')->withTimestamps();
    }
}
