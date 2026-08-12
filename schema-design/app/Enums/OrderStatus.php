<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending    = 'pending';
    case InPrinting = 'in_printing';
    case Completed  = 'completed';
    case Delivered  = 'delivered';
    case Cancelled  = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending    => 'قيد الانتظار',
            self::InPrinting => 'جارٍ الطباعة',
            self::Completed  => 'مكتملة',
            self::Delivered  => 'تم التسليم',
            self::Cancelled  => 'ملغاة',
        };
    }

    /** الانتقالات المسموح بها لكل حالة (آلة الحالات). */
    public function canTransitionTo(self $next): bool
    {
        return in_array($next, match ($this) {
            self::Pending    => [self::InPrinting, self::Cancelled],
            self::InPrinting => [self::Completed, self::Cancelled],
            self::Completed  => [self::Delivered],
            self::Delivered, self::Cancelled => [],
        }, true);
    }
}
