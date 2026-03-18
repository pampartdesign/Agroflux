<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderPlacedNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'      => 'order_placed',
            'title'     => 'New order placed',
            'message'   => 'Order #' . $this->order->id . ' has been placed — total: ' . number_format($this->order->total, 2) . ' €.',
            'order_id'  => $this->order->id,
            'tenant_id' => $this->order->tenant_id,
            'total'     => (string) $this->order->total,
            'status'    => $this->order->status,
        ];
    }
}
