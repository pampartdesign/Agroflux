<?php

namespace App\Notifications;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

abstract class TenantNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Tenant $tenant,
        public readonly array $payload = []
    ) {}

    public function via(object $notifiable): array
    {
        // Database-only for now. Later we can add broadcast/websocket safely.
        return ['database'];
    }
}
