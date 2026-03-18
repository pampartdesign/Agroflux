<?php

namespace App\Notifications\Core;

use App\Models\Tenant;
use App\Notifications\TenantNotification;

class NewOrderPlaced extends TenantNotification
{
    public function __construct(Tenant $tenant, array $payload = [])
    {
        parent::__construct($tenant, $payload);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'core.order.placed',
            'title' => 'New order received',
            'message' => 'A new order has been placed in your marketplace.',
            'order_id' => $this->payload['order_id'] ?? null,
            'tenant_id' => $this->tenant->id,
        ];
    }
}
