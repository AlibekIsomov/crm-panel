<?php

namespace App\Enums;

enum TaskType: string
{
    case Call = 'call';
    case Meeting = 'meeting';
    case Email = 'email';
    case Task = 'task';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
