<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuthorizedSellerProduct extends Model
{
    protected $fillable = ['authorized_seller_id', 'name', 'sort_order'];

    protected $casts = ['sort_order' => 'integer'];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(AuthorizedSeller::class, 'authorized_seller_id');
    }
}
