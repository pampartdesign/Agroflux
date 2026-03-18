<?php

namespace App\Notifications\Logi;

use App\Models\Tenant;
use App\Notifications\TenantNotification;

class OfferReceived extends TenantNotification
{
    public function __construct(Tenant $tenant, array $payload = [])
    {
        parent::__construct($tenant, $payload);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'logi.offer.received',
            'title' => 'New delivery offer',
            'message' => 'A trucker sent a delivery offer for your request.',
            'request_id' => $this->payload['request_id'] ?? null,
            'offer_id' => $this->payload['offer_id'] ?? null,
            'tenant_id' => $this->tenant->id,
        ];
    }
}
