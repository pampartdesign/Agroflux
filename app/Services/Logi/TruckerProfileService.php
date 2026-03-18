<?php

namespace App\Services\Logi;

use App\Models\TruckerProfile;

class TruckerProfileService
{
    public function upsert(int $userId, array $data): TruckerProfile
    {
        return TruckerProfile::updateOrCreate(
            ['user_id' => $userId],
            [
                'company_name' => $data['company_name'] ?? null,
                'phone' => $data['phone'] ?? null,
                'vehicle_type' => $data['vehicle_type'] ?? 'van',
                'notes' => $data['notes'] ?? null,
            ]
        );
    }
}
