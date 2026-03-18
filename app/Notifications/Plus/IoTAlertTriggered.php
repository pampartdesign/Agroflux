<?php

namespace App\Notifications\Plus;

use App\Models\Tenant;
use App\Notifications\TenantNotification;

class IoTAlertTriggered extends TenantNotification
{
    public function __construct(Tenant $tenant, array $payload = [])
    {
        parent::__construct($tenant, $payload);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'plus.iot.alert',
            'title' => 'IoT alert',
            'message' => $this->payload['message'] ?? 'A sensor alert was triggered.',
            'sensor_id' => $this->payload['sensor_id'] ?? null,
            'tenant_id' => $this->tenant->id,
        ];
    }
}
