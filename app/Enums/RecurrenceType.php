<?php

namespace App\Enums;

enum RecurrenceType: string
{
    case Daily = 'daily';
    case Weekly = 'weekly';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getDaysToAdd(): int
    {
        return match ($this) {
            self::Daily => 1,
            self::Weekly => 7,
        };
    }
}
