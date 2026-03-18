<?php

namespace App\Enums;

enum DeliveryRequestStatus: string
{
    case DRAFT = 'draft';
    case OPEN = 'open';
    case OFFERED = 'offered';
    case ACCEPTED = 'accepted';
    case SELF_DELIVERED = 'self_delivered';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public static function labels(): array
    {
        return [
            self::DRAFT->value => 'Draft',
            self::OPEN->value => 'Open',
            self::OFFERED->value => 'Offered',
            self::ACCEPTED->value => 'Accepted',
            self::SELF_DELIVERED->value => 'Self Delivered',
            self::COMPLETED->value => 'Completed',
            self::CANCELLED->value => 'Cancelled',
        ];
    }
}
