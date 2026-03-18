<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'surname',
        'email',
        'password',
        'phone',
        'address',
        'city',
        'zip_code',
        'country',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function fullName(): string
    {
        return trim($this->name . ' ' . $this->surname);
    }

    public function initials(): string
    {
        return strtoupper(
            substr($this->name, 0, 1) . substr($this->surname, 0, 1)
        );
    }
}
