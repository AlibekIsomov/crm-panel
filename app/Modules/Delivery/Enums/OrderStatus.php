<?php

namespace App\Modules\Delivery\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case IN_DELIVERY = 'in_delivery';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Ожидает оплаты',
            self::PAID => 'Оплачен',
            self::IN_DELIVERY => 'В пути',
            self::DELIVERED => 'Доставлен',
            self::CANCELLED => 'Отменён',
        };
    }

    public function canTransitionTo(self $targetStatus): bool
    {
        return match ($this) {
            self::PENDING => in_array($targetStatus, [self::PAID, self::CANCELLED]),
            self::PAID => in_array($targetStatus, [self::IN_DELIVERY, self::CANCELLED]),
            self::IN_DELIVERY => in_array($targetStatus, [self::DELIVERED, self::CANCELLED]),
            self::DELIVERED, self::CANCELLED => false,
        };
    }
}
