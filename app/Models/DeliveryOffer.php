<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryOffer extends Model
{
    protected $fillable = [
        'delivery_request_id',
        'trucker_user_id',
        'price',
        'currency',
        'message',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(DeliveryRequest::class, 'delivery_request_id');
    }

    public function trucker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trucker_user_id');
    }
}
