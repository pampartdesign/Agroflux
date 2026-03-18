<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class QrCode extends Model
{
    protected $fillable = [
        'qrable_type',
        'qrable_id',
        'public_token',
        'activated_at',
    ];

    protected $casts = [
        'activated_at' => 'datetime',
    ];

    public function qrable(): MorphTo
    {
        return $this->morphTo();
    }
}
