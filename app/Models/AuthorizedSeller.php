<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AuthorizedSeller extends Model
{
    protected $fillable = [
        'company_name',
        'category',
        'short_description',
        'address',
        'phone',
        'email',
        'featured_image',
        'website_url',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(AuthorizedSellerProduct::class)->orderBy('sort_order')->orderBy('name');
    }

    public function featuredImageUrl(): ?string
    {
        return $this->featured_image
            ? asset('uploads/' . $this->featured_image)
            : null;
    }
}
