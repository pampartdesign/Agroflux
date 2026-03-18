<?php

namespace App\Services;

use App\Models\QrCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class QrService
{
    public function ensureQr(Model $model): QrCode
    {
        $existing = QrCode::query()
            ->where('qrable_type', get_class($model))
            ->where('qrable_id', $model->getKey())
            ->first();

        if ($existing) return $existing;

        return QrCode::query()->create([
            'qrable_type' => get_class($model),
            'qrable_id' => $model->getKey(),
            'public_token' => Str::uuid()->toString(),
            'activated_at' => now(),
        ]);
    }
}
